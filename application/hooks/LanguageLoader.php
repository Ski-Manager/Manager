<?php
class LanguageLoader
{
    private $allowed_languages = ['english', 'french'];

    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');

        $siteLang = $ci->session->userdata('site_lang');

        // If the session has no valid language (e.g. after session expiry or
        // a new browser session), fall back to the persistent cookie that is
        // written by Language_switcher::switchLang().
        if (!$siteLang || !in_array($siteLang, $this->allowed_languages, TRUE)) {
            $cookieLang = $ci->input->cookie('site_lang');
            if (in_array($cookieLang, $this->allowed_languages, TRUE)) {
                $siteLang = $cookieLang;
            } else {
                $siteLang = 'english';
            }
            $ci->session->set_userdata('site_lang', $siteLang);
        }

        // Re-load every language file that the controller constructor already
        // loaded, but with the (now-correct) language.  This fixes the case
        // where the constructor ran with an empty session and loaded English
        // translations; by un-registering each file first we force CI3's
        // Lang::load() to merge the correct translations on top.
        foreach (array_keys($ci->lang->is_loaded) as $langfile) {
            if ($ci->lang->is_loaded[$langfile] !== $siteLang) {
                // Strip the '_lang.php' suffix added by CI3's Lang class.
                // Only proceed when the file follows the expected naming convention.
                if (substr($langfile, -9) === '_lang.php') {
                    $filebase = substr($langfile, 0, -9);
                    unset($ci->lang->is_loaded[$langfile]);
                    $ci->lang->load($filebase, $siteLang);
                }
            }
        }
    }
}
