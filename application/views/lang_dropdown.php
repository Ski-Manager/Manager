<?php

$site_lang = $this->session->userdata('site_lang') ?: 'english';
$is_french = ($site_lang === 'french');

if ($is_french) {
    $lang      = 'fr';
    $full_lang = 'english';
    $drop_lang = 'en';
} else {
    $lang      = 'en';
    $full_lang = 'french';
    $drop_lang = 'fr';
}
?>
<div class="join dropdown absolute_top_left_corner">
    <button type="button" class="btn btn-secondary dropdown-trigger" tabindex="0">
        <span class="lang-sm lang-lbl" lang="<?php echo $lang; ?>"></span>
    </button>
    <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-max" role="menu">
        <li>
            <a href="<?php echo base_url().'language_switcher/switchLang/'.$full_lang; ?>">
                <span class="lang-sm lang-lbl" lang="<?php echo $drop_lang; ?>"></span>
            </a>
        </li>
    </ul>
</div>
