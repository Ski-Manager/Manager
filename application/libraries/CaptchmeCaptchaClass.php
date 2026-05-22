<?php
/*----------
Copyright (c) 2011-2012 Attentive Ads SAS -- http://www.captchme.com

Librairie PHP permettant l'utilisation du service de captchas publicitaires CaptchMe.

Documentation : http://www.captchme.net
Conditions d'utilisation : http://www.captchme.com/informations-legales-captchme.html#CGU
Version : 1.0.9

CaptchMe ne garantit pas que le programme soit exempt d'erreurs, qu'il fonctionne tout le
temps, qu'il réponde à des exigences particulières, que les erreurs du programme seront
corrigées, que de nouvelles versions seront mises à disposition.
Le programme est livre "tel quel" sans aucune garantie d'aucune sorte. L'utilisateur
accepte notamment que l'usage du programme et de ses fonctionnalités, ainsi que de la
documentation incluse, se fasse sous sa responsabilité exclusive. Si des dommages tels
que pertes de données, pertes d'exploitation, arrêt d'exploitation d'entreprise, devaient
résulter de l'utilisation de ce logiciel, CaptchMe n'accepterait aucune responsabilité
quelle qu'elle soit.
Le programme reste propriété inaliénable de CaptchMe.
Le programme doit rester inchangé. En particulier, il ne peut être modifié, décompilé,
analysé, ni mis à disposition de tiers.

La bibliothèque PHP CaptchMe contient du code du plugin PHP reCAPTCHA, en accord avec ses
conditions d'utilisation rappelées ci-après:

---
Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
AUTHORS:
  Mike Crawford
  Ben Maurer

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
---

Pour toute question concernant le plugin, l'intégration ou l'utilisation de CaptchMe,
vous pouvez contacter le support technique à l'adresse sos@captchme.com
----------*/

/**
 * Captchme server
 */
if (!defined('CAPTCHME_API_SERVER')) {
    define("CAPTCHME_API_SERVER", "api.captchme.net");
}


define("CAPTCHME_LIB", "1.0.9");

class CaptchmeException extends Exception { }

class CaptchmeCaptchaClass {
    // script API response
    private $scriptResponse;

    // challenge API response
    private $challengeResponse;

    // ad flag
    private $ad;

    // Constructor
    function CaptchmeCaptcha($scriptResponse = '', $challengeResponse = '')
    {
        $this->scriptResponse = $scriptResponse;
        $this->challengeResponse = $challengeResponse;

        // Parse challenge response to set ad flag
        if( $challengeResponse != '' ) {
            $tmp=substr($challengeResponse,strrpos($challengeResponse, ',')+1);
            $tmp=explode(')', $tmp);
            $this->ad=trim((string)$tmp[0]);
        }
    }

    function getScriptResponse(){
        return $this->scriptResponse;
    }

    function getChallengeResponse(){
        return $this->challengeResponse;
    }

    function isAd(){
        return  toStrictBoolean($this->ad);
    }
}

//---------------------------------------------------------------------------------//
// Captcha generation
/**
 * This method load a captche on the server-side. It is useful in cases where it is mandatory to know if
 * an Ad has been served by Captchme. This method ouput a Captcha objet on which it is possible to call
 * "isAdd" method to know wether the captcha returned is an Ad or a standard captcha.
 *
 * Server-side captcha loading is usefull where captchme captcha services are used as micro-payment or
 * when you decide to maximize ad earnings by switching to another ad service when captchme does not
 * return an ad.
 *
 * If you use this method to do server-side captcha loading, you should then call captchme_generate_html method
 * providing the previously return captcha object.
 *
 * @param string $publicKey Your captchme public key
 * @param string $userIp IP of the user asking for a captcha
 * @param string $lang Captcha language (french 'fr' or english 'en', default to french)
 * @param string $format Captcha format (classic or wide, default to classic)
 * @param boolean $ssl Is the request to be made over ssl. This parameter is optional. Default value is false.
 * @param string $errorCode The error code returned by a previous verify call. This parameter is optional. Default
 * value is null.
 * @return CaptchmeCaptcha - A Captcha object
 */
function captchme_load_captcha($publicKey, $userIp, $lang = 'fr', $format = 'classic', $ssl = false, $errorCode = null)
{
    // sanity checks
    if ($publicKey == null || $publicKey == '' || $userIp == null || $userIp == '') {
        die ("publicKey and userIp are mandatory. If you do not have valid captchme API key, please get one from <a href='http://www" .
             ".captchme.net'>http://www.captchme.net</a>");
    }

    if ($ssl) {
        $proto =  "https://";
    } else {
        $proto = "http://";
    }

    // Call script API
    $scriptUrl = $proto.CAPTCHME_API_SERVER."/api/script?key=".$publicKey."&user_ip=".$userIp;
    if ($errorCode) {
        $scriptUrl .= "&error=" . $errorCode;
    }

    $response = _captchme_http_get($scriptUrl);
    if( $response == null ) {
        throw new CaptchmeException('captchme-not-reachable');
    }

    // Check response status
    $status = _captchme_get_http_status($response);
    if( $status != "200" ) {
        switch ($status) {
            case 404:
                throw new CaptchmeException('invalid-public-key');
                break;
            case 500:
                throw new CaptchmeException('internal-server-error');
                break;
        }
    }

    // Get script API response
    $scriptResponse = $response[1];
    // Get challenge id
    preg_match("/challenge : '([^']+)'/", $scriptResponse, $matches);
    $challengeKey=$matches[1];

    // Call challenge API
    $challengeUrl = $proto.CAPTCHME_API_SERVER."/api/challenge?key=".$publicKey."&ckey=".$challengeKey."&lang=".$lang."&format=".$format."&user_ip=".$userIp;

    $response = _captchme_http_get($challengeUrl);
    if( $response == null ) {
        throw new CaptchmeException('captchme-not-reachable');
    }

    // Check response status
    $status = _captchme_get_http_status($response);
    if( $status != "200" ) {
        switch ($status) {
            case 404:
                throw new CaptchmeException('invalid-challenge');
                break;
            case 500:
                throw new CaptchmeException('internal-server-error');
                break;
        }
    }

    // Get challenge API response
    $challengeResponse = $response[1];

    $captcha = new CaptchmeCaptchaClass($scriptResponse, $challengeResponse);

    return $captcha;
}

/**
 * This method generate HTML code (using javascript) to include
 * captchme captcha inside the HTML page.
 *
 * @param string $publicKey Your captchme public key
 * @param string $errorCode The error code returned by a previous verify call. This parameter is optional. Default
 * value is null.
 * @param boolean $ssl Is the request to be made over ssl. This parameter is optional. Default value is false.
 * @param array $customAttributes An array of custom key value pairs that will be use to set custom Captcha options
 *     Custom attribute can be one of:
 *     theme : specify the theme to use
 *     lang : specify the captcha language
 *     tabindex : specify the tabindex of captcha input
 *     format : specify the Captchme format
 *     callback : specify a javascript function to be called when the captcha is loaded
 *     custom_message : a javascript array of message tag and their value to customize the captcha language
 *
 *     Please refer to the documentation for more detail on customization.
 * @param CaptchmeCaptcha A captcha object loaded by captchme_load_captcha method to use for html rendering.
 * @return string - The HTML code to include in the form.
 */
function captchme_generate_html($publicKey, $errorCode = null, $ssl = true, $customAttributes = array(), $div = null, $captcha = null)
{
    // Avoid calling captchme services for invalid publicKey
    if ($publicKey == null || $publicKey == '') {
        die ("Invalid public key. If you do not have valid captchme API key, please get one from <a href='http://www" .
             ".captchme.net'>http://www.captchme.net</a>");
    }

    $output = "";

    // If a server side captcha loading has been done
    if( $captcha != null ) {
        $output = "<script type=\"text/javascript\">\r\nvar challenge_hook = function() {\r\n".$captcha->getChallengeResponse()."\r\n};\r\n</script>";
    }


    // Generate custom options
    if ( count($customAttributes)> 0) {
        $output = $output . "<script type=\"text/javascript\">\r\n";
        $output = $output . "var CaptchmeOptions = {\r\n";
        foreach ($customAttributes as $key => $value) {
            if( $key != 'callback' && $key != 'custom_messages') {
                $output = $output . $key . ":'" . $value . "',\r\n";
            } else {
                $output = $output . $key . ":" . $value . ",\r\n";
            }
        }
        if( $captcha != null ) {
            $output = $output . "'challenge_hook' : challenge_hook\r\n";
        }
        $output = $output . "};</script>\r\n";
    } else {
    	if( $captcha != null ) {
	    	$output = $output . "<script type=\"text/javascript\">\r\n";
	    	$output = $output . "var CaptchmeOptions = {\r\n";
	    	$output = $output . "'challenge_hook' : challenge_hook\r\n";
	   		$output = $output . "};</script>\r\n";
    	}
    }

    if( $captcha != null ) {
        $output = $output .  "<script type=\"text/javascript\">".$captcha->getScriptResponse()."</script>";
        return $output;
    } else {
        // Browser side loading

        if ($ssl) {
            $proto =  "https://";
        } else {
            $proto = "http://";
        }

        $url = $proto . CAPTCHME_API_SERVER . '/api/script?key=' . $publicKey;
        if ($errorCode) {
            $url .= "&error=" . $errorCode;
        }

        date_default_timezone_set('Europe/Paris');
        if($div != null)
            $url .= '&div=' . $div;
        else {
            $url .= '&div=' . ($id = base64_encode(date('Y-m-d H:S:i')));

            $output = $output . '<div id="' . $id . '" style="width:300px;text-align:left;font-size:0.80em;border: 1px solid #F92942;"><div style="color: #F92942;margin: 15px 20px 10px 20px">Vous utilisez un logiciel de type AdBlock, qui bloque le service de captchas publicitaires utilisé sur ce site.</div><div style="margin: 15px 20px 10px 20px">Découvrir pourquoi <a href="http://www.captchme.com/internautes-captcha-publicitaire-lisible-ludique-publicite-utile.html#Avantages" target="_blank">Captch Me est bien + agréable et efficace qu\'un captcha classique</a></div><div style="margin: 15px 20px 10px 20px">Découvrir comment <a href="http://'.CAPTCHME_API_SERVER . '/info/fr/adblock.php" target="_blank">débloquer l\'utilisation de Captch Me avec un logiciel de type AdBlock.</a></div></div>';
        }

        $output = $output . '<script type="text/javascript" src="' . $url . '"></script>';
        return $output;
    }
}

//---------------------------------------------------------------------------------//
// Captcha server side verification

/**
 * A CaptchmeResponseClass is returned from captchme_verify()
 */
class CaptchmeResponseClass
{

    // Validaity of the captcha resolution
    var $is_valid;

    // Returned message
    var $error;

    // Constructor
    function CaptchmeResponse($is_valid = false, $error = null)
    {
        $this->is_valid = $is_valid;
        $this->error = $error;
    }

}

/**
 * Verify the user's answer to a captchme captcha. This method calls
 * an HTTP POST function to verify the validity of the answer.
 *
 * @param string $privateKey Your captchme private key
 * @param string $challengeKey Key of the challenge to check
 * @param string $response User's answer
 * @param string $userIp IP of the user resolving the captcha
 * @param string  $authenticationKey Your captchme authentication key
 * @return CaptchmeResponseClass
 */
function captchme_verify($privateKey, $challengeKey, $response, $userIp, $authenticationKey = '')
{
    
  /*  $captchmeResponse = new CaptchmeResponseClass();
    // Check challenge and response validity
    if ($privateKey == null || $privateKey == '' ||
        $userIp == null || $userIp == '' ||
        $challengeKey == null || $challengeKey == '' ||
        $response == null || $response == ''
    ) {

        $captchmeResponse->is_valid = false;
        $captchmeResponse->error = 'missing-mandatory-parameter';
        //return new CaptchmeCaptchaClass(false, 'missing-mandatory-parameter');

    }*/

    // Convert response in utf-8
    if (!mb_detect_encoding($response, 'UTF-8', true)) {
        $response = mb_convert_encoding($response, 'UTF-8', 'ISO-8859-1');
    }

    preg_match('@([^/:]+)(:(\d+)|)(/.*|)@', CAPTCHME_API_SERVER, $matches);
    $hostname = $matches[1];
    $port = strlen($matches[3]) > 0 ? $matches[3] : '80';
    $path = $matches[4];

    $response = _captchme_http_post($hostname, "$path/api/verify",
                                    array(
                                         'private_key' => $privateKey,
                                         'challenge_key' => $challengeKey,
                                         'response' => $response,
                                         'user_ip' => $userIp
                                    ),
                                    $port);
    $result = explode("\n", trim($response[1], "\n"));
    $captchmeResponse = new CaptchmeResponseClass();
    if ($authenticationKey != '') {
        # validate message signature
        # Add response result (true or false), the challenge key and the authentication key
        $signature_hash = sha1($result[0] . $challengeKey . $authenticationKey);

        // if the computed signature hash does not match the one returned by the API call
        // we might be in case of captchme service forgery. Do not validate this response.
        if ($signature_hash != $result[2]) {
            $captchmeResponse->is_valid = false;
            $captchmeResponse->error = 'authentication-failed';
            return $captchmeResponse;
        }
    }

    if (trim($result [0]) == 'true') {
        $captchmeResponse->is_valid = true;
    }
    else {
        $captchmeResponse->is_valid = false;
        $captchmeResponse->error = $result [1];
    }
    return $captchmeResponse;

}


/**
 * Encodes the given data into a query string format
 * @param $data - array of string elements to be encoded
 * @return string - encoded request
 */
function _captchme_qsencode($data)
{
    $req = "";
    foreach ($data as $key => $value)
        $req .= $key . '=' . urlencode(stripslashes($value)) . '&';

    // Cut the last '&'
    $req = substr($req, 0, strlen($req) - 1);
    return $req;
}

/**
 * Submits an HTTP POST to a captchme server
 * @param string $host
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
function _captchme_http_post($host, $path, $data, $port = 80)
{

    $req = _captchme_qsencode($data);

    $http_request = "POST $path HTTP/1.0\r\n";
    $http_request .= "Host: $host:$port\r\n";
    $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
    $http_request .= "Content-Length: " . strlen($req) . "\r\n";
    $http_request .= "User-Agent: captchme/PHP/".CAPTCHME_LIB ."\r\n";
    $http_request .= "\r\n";
    $http_request .= $req;

    $response = '';
    if (false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10))) {
        return new CaptchmeResponse(false, 'captchme-not-reachable');
    }

    fwrite($fs, $http_request);

    while (!feof($fs))
        $response .= fgets($fs, 1024);
    fclose($fs);
    $response = explode("\r\n\r\n", $response, 2);

    return $response;
}

/**
 * Submits an HTTP GET to a captchme server
 * @param string $url
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
function _captchme_http_get($url)
{
        $response = "";
        $parsed_url = parse_url($url);
        $parsed_url['port'] = (empty($parsed_url['port']) ? 80 : $parsed_url['port']);
        if (false == ($fp = fsockopen($parsed_url['host'], $parsed_url['port'], $err_num, $err_msg, 30))) {
            return null;
        }
        //Préparaton de la transmission de cookie
        $listCookies = "";
        date_default_timezone_set('Europe/Paris');

        foreach ($_COOKIE as $key => $value) {
            $listCookies .= urlencode($key) . "=" . str_replace('5C%', '', urlencode($value)) . "; path=/; domain=." . substr(CAPTCHME_API_SERVER, strpos('.', CAPTCHME_API_SERVER)) .";";
        }
        //Envoi du header
        fputs($fp, "GET /{$parsed_url['path']}?{$parsed_url['query']} HTTP/1.0\r\n");
        fputs($fp, "Host: {$parsed_url['host']}\r\n");
        fputs($fp, "Cookie: " . substr($listCookies, 0, -2) . "\r\n");
        fputs($fp, "User-Agent: captchme/PHP/" . CAPTCHME_LIB . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        while(!feof($fp)) {
                $response .= fgets($fp, 128);
        }
        fclose($fp);

        //Traitement des cookies issus de challenge
        $res = cookie_parse($response);
        foreach ($res as $key => $value) {
            setcookie($value['value']['key'],
                      urldecode($value['value']['value']), //Sinon setcookie la double encodera ce qui est considéré comme une attaque
                      $value['expires'],
                      $value['path'], // On ne précise pas le domaine, comme ça il prend celui de l'éditeur
                      $_SERVER["HTTP_HOST"]);
                      //$value['domain']); // On ne précise pas le domaine, comme ça il prend celui de l'éditeur
        }
        $response = explode("\r\n\r\n", $response, 2);
        return $response;
}

/**
 * Retrieve HTTP response status from given HTTP response
 * @param string $response
 * @return string HTTP status
 */
function _captchme_get_http_status($response) {
    $headers=explode("\r\n", $response[0]);
    return substr($headers[0],9,3);
}

function toStrictBoolean ($_val, $_trueValues = array('yes', 'y', 'true'), $_forceLowercase = true){
    if (is_string($_val)) {
        return (in_array(
             ($_forceLowercase?strtolower($_val):$_val)
            , $_trueValues)
        );
    } else {
        return (boolean) $_val;
    }
}

/**
 * Extract cookies of the header from given HTTP response
 * @param string $response
 * @return array cookies
 */
function cookie_parse($response){
    date_default_timezone_set('Europe/Paris');
    $cookies = array();
    $header=explode("\r\n", $response);
    foreach( $header as $line ) {
        if( preg_match( '/^Set-Cookie: /i', $line ) ) {
            $line = preg_replace( '/^Set-Cookie: /i', '', trim( $line ) );
            $csplit = explode( ';', $line );
            $cdata = array();
            foreach( $csplit as $data ) {
                $cinfo = explode( '=', $data );
                $cinfo[0] = trim( $cinfo[0] );
                if( $cinfo[0] == 'expires' ) $cinfo[1] = strtotime( $cinfo[1] );
                if( $cinfo[0] == 'secure' ) $cinfo[1] = "true";
                if( in_array( $cinfo[0], array( 'domain', 'expires', 'path', 'secure', 'comment' ) ) ) {
                    $cdata[trim( $cinfo[0] )] = $cinfo[1];
                }
                else {
                    $cdata['value']['key'] = $cinfo[0];
                    $cdata['value']['value'] = $cinfo[1];
                }
            }
            $cookies[] = $cdata;
        }
    }
    return $cookies;
}
?>
