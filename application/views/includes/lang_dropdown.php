<?php

$site_lang  = $this->session->userdata('site_lang') ?: 'english';
$is_french  = ($site_lang === 'french');
$home_lang  = $this->lang->line('home');
$label      = (is_array($home_lang) && isset($home_lang['language'])) ? $home_lang['language'] : 'Language';

?>
<div class="dropdown">
    <button type="button"
            class="btn btn-info btn-sm dropdown-trigger"
            tabindex="0"
            aria-expanded="false"
            aria-label="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>">
        <span class="lang-sm lang-lbl" lang="<?php echo $is_french ? 'fr' : 'en'; ?>" aria-hidden="true"></span>
        <span class="ml-1"><?php echo $is_french ? 'FR' : 'EN'; ?></span>
    </button>
    <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-max">
        <li>
            <a class="<?php echo !$is_french ? 'active' : ''; ?>"
               href="<?php echo base_url('language_switcher/switchLang/english'); ?>">
                <span class="lang-sm lang-lbl mr-1" lang="en" aria-hidden="true"></span>
                English
            </a>
        </li>
        <li>
            <a class="<?php echo $is_french ? 'active' : ''; ?>"
               href="<?php echo base_url('language_switcher/switchLang/french'); ?>">
                <span class="lang-sm lang-lbl mr-1" lang="fr" aria-hidden="true"></span>
                Français
            </a>
        </li>
    </ul>
</div>
