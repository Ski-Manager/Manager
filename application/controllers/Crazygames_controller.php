<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Crazygames_controller
 *
 * Handles CrazyGames SDK authentication (Full Implementation scenario).
 * The browser posts the JWT from SDK.getUserToken() here; we verify the
 * RS256 signature against the CrazyGames JWKS, then look up / auto-create
 * the game_players row keyed on the permanent cg_user_id claim.
 */
class Crazygames_controller extends CI_Controller {

    /** CrazyGames public key endpoint */
    const PUBKEY_URL = 'https://sdk.crazygames.com/publicKey.json';

    /** Local key cache file (1-hour TTL) */
    const JWKS_CACHE = 'cg_pubkey.json';

    /** Cache TTL in seconds */
    const JWKS_TTL = 3600;

    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
    }

    /**
     * verify_token  POST endpoint called by crazygames.js
     *
     * Expects: { "token": "<JWT>" }
     * Returns: { "success": true } or { "success": false, "error": "..." }
     */
    public function verify_token() {
        header('Content-Type: application/json');

        if ($this->input->method() !== 'post') {
            echo json_encode(['success' => false, 'error' => 'POST required']);
            return;
        }

        $raw = $this->input->raw_input_stream;
        $body = json_decode($raw, true);
        $token = isset($body['token']) ? trim($body['token']) : '';

        if (empty($token)) {
            echo json_encode(['success' => false, 'error' => 'No token']);
            return;
        }

        $payload = $this->_verify_jwt($token);
        if (!$payload) {
            echo json_encode(['success' => false, 'error' => 'Invalid token']);
            return;
        }

        $cg_user_id  = isset($payload['userId'])         ? $payload['userId']         : '';
        $cg_username = isset($payload['username'])        ? $payload['username']        : '';
        $cg_avatar   = isset($payload['profilePicture']) ? $payload['profilePicture'] : '';

        if (empty($cg_user_id)) {
            echo json_encode(['success' => false, 'error' => 'Missing userId in token']);
            return;
        }

        // Look up existing player by cg_user_id
        $player = $this->users_model->get_user_by_cg_id($cg_user_id);

        if (!$player) {
            // New CrazyGames user — auto-create an account
            $username = $this->_unique_username($cg_username);
            $insert   = [
                'username'       => $username,
                'email'          => 'cg_' . $cg_user_id . '@crazygames.local',
                'cg_user_id'     => $cg_user_id,
                'cg_username'    => $cg_username,
                'activated'      => 1,
                'preferred_lang' => 'english',
                'genepis'        => 0,
            ];
            $ok = $this->users_model->create_username($insert);
            if (!$ok) {
                echo json_encode(['success' => false, 'error' => 'Account creation failed']);
                return;
            }
            // Re-fetch to get auto-generated id_player and genepis/resort data
            $player = $this->users_model->get_user_by_cg_id($cg_user_id);
            if (!$player) {
                echo json_encode(['success' => false, 'error' => 'Account fetch failed']);
                return;
            }
        } else {
            // Existing user — refresh their display name if it changed
            if (!empty($cg_username) && $player['cg_username'] !== $cg_username) {
                $this->users_model->update_cg_username($player['id_player'], $cg_username);
            }
        }

        // Set CI session — identical structure to Login_controller::googleCallback()
        $username    = $player['username'];
        $resort_id   = $this->users_model->get_resort_id($player['id_player']);
        $is_admin    = $this->users_model->check_if_admin($username) ? 1 : 0;
        $player_lang = $this->users_model->get_user_preferred_lang($player['id_player']);

        $this->session->set_userdata([
            'login_username'  => $username,
            'login_id_resort' => $resort_id,
            'is_logged_in'    => true,
            'site_lang'       => $player_lang ? $player_lang : 'english',
            'is_admin'        => $is_admin,
        ]);

        $this->users_model->last_connection_player($username);
        $this->users_model->disable_vacation_mode($username);
        $this->session->set_userdata('difficulty_mode', $this->users_model->get_difficulty_mode($player['id_player']));

        echo json_encode(['success' => true]);
    }

    // ----------------------------------------------------------------
    //  Private helpers
    // ----------------------------------------------------------------

    /**
     * _verify_jwt  Verifies a CrazyGames RS256 JWT and returns its payload.
     *
     * @param  string $token  Raw JWT string
     * @return array|false  Decoded payload array or FALSE on failure
     */
    private function _verify_jwt($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        list($header_b64, $payload_b64, $sig_b64) = $parts;

        $header  = json_decode($this->_b64_decode($header_b64), true);
        $payload = json_decode($this->_b64_decode($payload_b64), true);

        if (!is_array($header) || !is_array($payload)) return false;
        if (($header['alg'] ?? '') !== 'RS256') return false;

        // Reject expired tokens
        if (!empty($payload['exp']) && (int) $payload['exp'] <= time()) return false;

        $pem = $this->_get_public_key();
        if (!$pem) return false;

        $data      = $header_b64 . '.' . $payload_b64;
        $signature = $this->_b64_decode($sig_b64);

        $result = openssl_verify($data, $signature, $pem, OPENSSL_ALGO_SHA256);
        if ($result !== 1) return false;

        return $payload;
    }

    /**
     * _get_public_key  Fetches and caches the CrazyGames RSA public key.
     * The key arrives as PKCS#1 ("BEGIN RSA PUBLIC KEY"); openssl_verify needs
     * PKCS#8 ("BEGIN PUBLIC KEY"), so we load it via openssl_pkey_get_public
     * which handles both formats.
     *
     * @return resource|false  OpenSSL key resource or FALSE on failure
     */
    private function _get_public_key() {
        $cache_path = APPPATH . 'cache/' . self::JWKS_CACHE;
        $pem        = null;

        // Use cached PEM if fresh
        if (file_exists($cache_path) && (time() - filemtime($cache_path)) < self::JWKS_TTL) {
            $cached = json_decode(file_get_contents($cache_path), true);
            if (!empty($cached['publicKey'])) {
                $pem = $cached['publicKey'];
            }
        }

        // Fetch fresh key if needed
        if (!$pem) {
            $ch = curl_init(self::PUBKEY_URL);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $raw = curl_exec($ch);
            $err = curl_errno($ch);
            curl_close($ch);

            if ($err || !$raw) return false;
            $data = json_decode($raw, true);
            if (empty($data['publicKey'])) return false;

            $pem = $data['publicKey'];
            @file_put_contents($cache_path, $raw);
        }

        // openssl_pkey_get_public handles both PKCS#1 and PKCS#8 PEM formats
        return openssl_pkey_get_public($pem);
    }

    /** Base64url decode */
    private function _b64_decode($input) {
        $input = strtr($input, '-_', '+/');
        $pad   = strlen($input) % 4;
        if ($pad) $input .= str_repeat('=', 4 - $pad);
        return base64_decode($input);
    }

    /**
     * _unique_username  Returns a sanitized username that is unique in game_players.
     * Falls back to appending incrementing numbers if the base name is taken.
     *
     * @param  string $raw  Raw CrazyGames username
     * @return string
     */
    private function _unique_username($raw) {
        // Sanitize: keep alphanumeric + underscore, 3-22 chars
        $base = preg_replace('/[^a-zA-Z0-9_]/', '', $raw);
        $base = substr($base, 0, 22);
        if (strlen($base) < 3) $base = 'Skier' . substr(md5($raw), 0, 5);

        $candidate = $base;
        $i = 1;
        while ($this->users_model->get_user_id($candidate) !== false) {
            $candidate = substr($base, 0, 18) . $i;
            $i++;
        }
        return $candidate;
    }
}
