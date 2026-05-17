<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mountain_cam_model
 *
 * Manages per-resort mountain webcam settings stored in game_resort_mountain_cams.
 */
class Mountain_cam_model extends CI_Model {

    /**
     * get_settings_DB  Returns the mountain-cam settings row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('is_enabled, num_cams, cam_quality, stream_mode, social_media, night_vision, weather_overlay')
            ->from('game_resort_mountain_cams')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'is_enabled'      => 0,
                'num_cams'        => MOUNTAIN_CAM_DEFAULT_CAMS,
                'cam_quality'     => 1,
                'stream_mode'     => 0,
                'social_media'    => 0,
                'night_vision'    => 0,
                'weather_overlay' => 0,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the mountain-cam settings for a resort.
     *
     * @param int $id_resort
     * @param int $is_enabled   0 or 1
     * @param int $num_cams     Number of cameras
     * @param int $cam_quality  Quality level (1, 2, or 3)
     * @return bool
     */
    public function save_settings_DB($id_resort, $is_enabled, $num_cams, $cam_quality, $stream_mode, $social_media, $night_vision, $weather_overlay) {
        $data = [
            'id_resort'       => (int)$id_resort,
            'is_enabled'      => (int)$is_enabled,
            'num_cams'        => (int)$num_cams,
            'cam_quality'     => (int)$cam_quality,
            'stream_mode'     => (int)$stream_mode,
            'social_media'    => (int)$social_media,
            'night_vision'    => (int)$night_vision,
            'weather_overlay' => (int)$weather_overlay,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_mountain_cams');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_mountain_cams', $data);
        } else {
            $this->db->insert('game_resort_mountain_cams', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
