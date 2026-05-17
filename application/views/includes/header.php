<!doctype html>
<?php
$site_lang = $this->session->userdata('site_lang');
$html_lang = ($site_lang === 'french') ? 'fr' : 'en';

if (!function_exists('asset_version')) {
    function asset_version($relative_path) {
        static $cache = [];
        if (!isset($cache[$relative_path])) {
            $full = FCPATH . $relative_path;
            $cache[$relative_path] = file_exists($full) ? filemtime($full) : '1';
        }
        return $cache[$relative_path];
    }
}
?>
<html lang="<?php echo $html_lang; ?>">
<!-- Apply saved theme before first paint to avoid flash of unstyled content.
     Kept on one line intentionally so it executes before any stylesheet loads.
     NOTE: The DARK_THEMES list below must stay in sync with the one in footer.php. -->
<script>(function(){try{var t=localStorage.getItem('sm_theme');if(t){document.documentElement.setAttribute('data-theme',t);var dk=['dark'];document.documentElement.setAttribute('data-bs-theme',dk.indexOf(t)!==-1?'dark':'light');}}catch(e){}}());document.documentElement.classList.add('preload');</script>
<head>
<meta charset="UTF-8">

<!-- Critical layout CSS – inlined to prevent CLS from deferred stylesheets.
     These rules mirror the values in custom.css (which includes the former
     simple-sidebar.css) so the page skeleton has the correct dimensions before
     any async stylesheet loads. -->
<style>
.bgimage{height:clamp(50px,12vw,160px);position:relative;overflow:hidden}
.wrapper{padding-left:65px}
@media(min-width:768px){.wrapper{padding-left:200px}}
@media(max-width:767px){.wrapper{padding-left:0!important;padding-right:0!important}}
/* On mobile the sidebar is a fixed overlay — it has zero layout impact */
@media(max-width:767px){#sidebar-wrapper{position:fixed!important;left:0;top:0;height:100%;z-index:1050;transform:translateX(-100%);transition:transform .25s ease;margin-left:0!important;float:none!important;top:0;overflow-y:auto;width:260px!important;border-radius:0!important;border:none!important}
  #sidebar-wrapper.mobile-open{transform:translateX(0)}
  #sidebar-mobile-backdrop{display:none;position:fixed;inset:0;z-index:1049;background:rgba(0,0,0,.5)}
  #sidebar-mobile-backdrop.active{display:block}
}
.home-intro-img{height:480px;object-fit:cover;display:block;width:100%}
/* Ensure DaisyUI tooltip elements (.tooltip[data-tip]) are always visible.
   tailwind.css sets .tooltip{opacity:0;display:block} for Bootstrap tooltip
   compat; the [data-tip].tooltip selector (specificity 0,2,0) overrides that
   so DaisyUI tooltips render correctly regardless of stylesheet load order. */
[data-tip].tooltip{opacity:1}
#sidebar-wrapper .icon_sidebar{display:flex;align-items:center;gap:5px}
/* Suppress all CSS transitions/animations during the initial page-load state
   restore (sidebar toggle, dark-mode) to avoid forced reflows from layout-
   affecting properties (width, margin-left) before the page is interactive. */
.preload #sidebar-wrapper,.preload .wrapper,.preload #page-content-wrapper,.preload [data-theme]{transition:none!important;animation-duration:.001s!important}
/* Critical dark-mode colours – applied immediately from data-theme set above
   so controller pages never flash white before stylesheets load. */
[data-theme="dark"] body{background-color:#1a1d23!important;color:#e8eaf0!important}
[data-theme="dark"] #sidebar-wrapper{background-color:#1e2330!important}
[data-theme="dark"] #page-content-wrapper,[data-theme="dark"] .wrapper{background-color:#1a1d23!important}
/* Cards, tables and jQuery UI widgets – prevent white flash before tailwind.css applies */
[data-theme="dark"] .card{background-color:#1e2330!important;border-color:#2a3349!important}
[data-theme="dark"] .card-header,[data-theme="dark"] .card-footer{background-color:#252d3d!important;border-color:#2a3349!important}
[data-theme="dark"] .table>:not(caption)>*>*{background-color:#1a1d23!important;color:#e8eaf0!important;border-b-color:#2a3349!important}
[data-theme="dark"] .ui-widget-content{background:#1e2330!important;color:#e8eaf0!important;border-color:#2a3349!important}
[data-theme="dark"] .ui-widget-header{background:#252d3d!important;color:#e8eaf0!important;border-color:#2a3349!important}
[data-theme="dark"] .ui-state-default,[data-theme="dark"] .ui-widget-content .ui-state-default{background:#252d3d!important;color:#e8eaf0!important;border-color:#2a3349!important}
[data-theme="dark"] .hp-faq-panel{background-color:#1e2330!important;border-color:#2a3349!important}
[data-theme="dark"] .hp-faq-accordion-item,[data-theme="dark"] #changelogAccordion .collapse{background-color:#252d3d!important}
[data-theme="dark"] .hp-faq-accordion-item .collapse-title,[data-theme="dark"] #changelogAccordion .collapse .collapse-title{background-color:#252d3d!important;color:#e8eaf0!important}
[data-theme="dark"] .hp-faq-accordion-item .collapse-content,[data-theme="dark"] #changelogAccordion .collapse .collapse-content{background-color:#252d3d!important;color:#c8cad0!important}
[data-theme="dark"] .hp-faq-accordion-item::details-content,[data-theme="dark"] #changelogAccordion .collapse::details-content{background-color:#252d3d!important}
</style>

<!-- Preload LCP banner image present on every page (CSS background-image) -->
<link rel="preload" as="image" type="image/avif"
      href="<?php echo base_url('img/images/top_banner_large.avif'); ?>">

<!-- Preload LCP hero image on the public landing page and the logged-in home page where it is used immediately -->
<?php if ($this->uri->segment(1) === 'index2_controller' || $this->uri->segment(1) === 'home_controller' || $this->uri->uri_string() === ''): ?>
<link rel="preload" as="image" type="image/avif"
      href="<?php echo base_url('img/images/homeintroimage.avif'); ?>">
<?php endif; ?>

<!-- DNS prefetch / preconnect for third-party origins -->
<link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
<link rel="preconnect" href="https://www.googletagmanager.com">
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="preconnect" href="https://code.jquery.com" crossorigin>
<link rel="dns-prefetch" href="https://static.hotjar.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- Ad networks -->
<link rel="dns-prefetch" href="https://pl28525965.profitablecpmratenetwork.com">
<link rel="dns-prefetch" href="https://pl29335453.profitablecpmratenetwork.com">
<link rel="dns-prefetch" href="https://pl29335451.profitablecpmratenetwork.com">
<link rel="dns-prefetch" href="https://www.profitablecpmratenetwork.com">

<!-- Google Consent Mode v2 – initialise BEFORE gtag.js loads so that
     ad/analytics cookies are blocked until the user has explicitly
     accepted via the cookie-consent banner. -->
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  var _smCookieChoice;
  try { _smCookieChoice = localStorage.getItem('sm_cookie_choice'); } catch (e) {}
  gtag('consent', 'default', {
    'ad_storage':         _smCookieChoice === 'accepted' ? 'granted' : 'denied',
    'analytics_storage':  _smCookieChoice === 'accepted' ? 'granted' : 'denied',
    'ad_user_data':       _smCookieChoice === 'accepted' ? 'granted' : 'denied',
    'ad_personalization': _smCookieChoice === 'accepted' ? 'granted' : 'denied',
    'wait_for_update': 500
  });
  gtag('js', new Date());
  gtag('config', 'G-8SY2PMMDJW');
</script>

<!-- Google AdSense – placed directly in <head> as recommended by Google to
     enable Auto Ads on all pages. The async attribute keeps it non-render-blocking. -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5636695863753930"
     crossorigin="anonymous"></script>

<!-- Google Tag Manager – injected after the page load event to defer
     parsing/compilation of unused code. The consent mode and dataLayer
     queue are already initialised above so no events or ad impressions are lost. -->
<script>
window.addEventListener('load', function() {
    var _gtm = document.createElement('script');
    _gtm.async = true;
    _gtm.src = 'https://www.googletagmanager.com/gtag/js?id=G-8SY2PMMDJW';
    document.head.appendChild(_gtm);
});
</script>

<!-- Tailwind CSS 4.2.1 + DaisyUI 5.5.19 – loaded synchronously so dark-mode
     CSS variables are guaranteed to apply before first paint. -->
<?php $_tv = asset_version('css/tailwind.css'); ?>
<link rel="stylesheet" href="<?php echo base_url().'css/tailwind.css?v='.$_tv; ?>">
<!-- Inter font (Google Fonts) – deferred so it doesn't block rendering -->
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300..700;1,400&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300..700;1,400&display=swap"></noscript>

<!-- Preload the Bootstrap Icons woff2 font so it is available as soon as the
     CSS @font-face rule is parsed, preventing icons from being invisible on
     a cold-cache page load (font-display:block requires the font to be ready
     within ~3 s; the preload ensures it arrives in time). -->
<!-- Font Awesome 7 Free -->
<link rel="preload" href="https://use.fontawesome.com/releases/v7.2.0/css/all.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://use.fontawesome.com/releases/v7.2.0/css/all.css"></noscript>

<?php $_cv = asset_version('css/custom.css'); ?>
<link rel="preload" href="<?php echo base_url().'css/custom.css?v='.$_cv; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="<?php echo base_url().'css/custom.css?v='.$_cv; ?>"></noscript>

<?php
// DataTables CSS is only needed on pages that render a DataTable.
// Keep in sync with $_datatables_controllers in footer.php.
$_dt_css_controllers = ['leaderboard_controller', 'logs_controller', 'hire_staff_controller'];
if (in_array($this->router->class, $_dt_css_controllers, true)):
?>
<link rel="preload" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css"></noscript>
<?php endif; ?>

<?php $_lv = asset_version('css/languages.min.css'); ?>
<link rel="preload" href="<?php echo base_url().'css/languages.min.css?v='.$_lv; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="<?php echo base_url().'css/languages.min.css?v='.$_lv; ?>"></noscript>


<!-- Dynamic Meta Description, Canonical & Open Graph -->
<?php
    $controller_name = $this->uri->segment(1);
    $title_key = $controller_name . '_title';
    $desc_key  = $controller_name . '_meta_desc';

    if (isset($this->lang->line('home')[$title_key])) {
        $page_og_title = $this->lang->line('home')[$title_key] . ' | Ski-Manager';
    } else {
        $page_og_title = 'Ski-Manager – Free Online Ski Resort Management Game';
    }

    if (isset($this->lang->line('home')[$desc_key])) {
        $description = $this->lang->line('home')[$desc_key];
    } else {
        $description = 'Ski-Manager free online game. Manage your ski resort.';
    }

    $uri_string    = ltrim($this->uri->uri_string(), '/');
    // Always build the canonical with https:// regardless of the current
    // request scheme.  This prevents HTTP-accessed pages from producing an
    // HTTP canonical, which Google treats as a soft 404 / wrong canonical.
    $canonical_base = 'https://' . preg_replace('#^https?://#', '', rtrim($this->config->item('base_url'), '/'));
    $canonical_url  = $canonical_base . ($uri_string !== '' ? '/' . $uri_string : '/');
    $og_image      = rtrim($this->config->item('base_url'), '/') . '/img/images/homeintroimage.avif';
    $og_locale     = ($html_lang === 'fr') ? 'fr_FR' : 'en_US';

    echo '<meta name="description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">';
    echo '<link rel="canonical" href="' . htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta property="og:type" content="website">';
    echo '<meta property="og:url" content="' . htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta property="og:title" content="' . htmlspecialchars($page_og_title, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta property="og:description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta property="og:image" content="' . htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta property="og:image:width" content="800">';
    echo '<meta property="og:image:height" content="436">';
    echo '<meta property="og:image:alt" content="Ski-Manager – Free Online Ski Resort Management Game">';
    echo '<meta property="og:site_name" content="Ski-Manager">';
    echo '<meta property="og:locale" content="' . htmlspecialchars($og_locale, ENT_QUOTES, 'UTF-8') . '">';
    $og_locale_alt = ($html_lang === 'fr') ? 'en_US' : 'fr_FR';
    echo '<meta property="og:locale:alternate" content="' . htmlspecialchars($og_locale_alt, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta name="twitter:card" content="summary_large_image">';
    echo '<meta name="twitter:title" content="' . htmlspecialchars($page_og_title, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta name="twitter:description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta name="twitter:image" content="' . htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') . '">';
    echo '<meta name="twitter:image:alt" content="Ski-Manager – Free Online Ski Resort Management Game">';
?>

<!-- WebSite Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Ski-Manager",
  "url": "<?php echo htmlspecialchars(rtrim($this->config->item('base_url'), '/') . '/', ENT_QUOTES, 'UTF-8'); ?>",
  "description": "Free online ski resort management game. Build, manage and grow your dream ski resort.",
  "inLanguage": ["en", "fr"],
  "potentialAction": {
    "@type": "RegisterAction",
    "target": "<?php echo htmlspecialchars(rtrim($this->config->item('base_url'), '/') . '/register_controller', ENT_QUOTES, 'UTF-8'); ?>",
    "name": "Sign Up for Free"
  },
  "sameAs": [
    "https://www.facebook.com/Ski-Manager-1377272882355150/",
    "https://discordapp.com/channels/593708638212718632/593708638695325696"
  ]
}
</script>

<!-- Google Sign-In client ID -->
<?php if ($this->config->item('google_signin_enabled')): ?>
<meta name="google-signin-client_id" content="<?php echo htmlspecialchars($this->config->item('google_client_id'), ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>

<!-- Required Meta -->
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- Apple Touch Icon & PWA manifest -->
<link rel="apple-touch-icon" href="<?php echo base_url('img/icons/app/icon-192.png'); ?>">
<link rel="manifest" href="<?php echo base_url('manifest.json'); ?>">
<meta name="theme-color" content="#252932">
<!-- CrazyGames SDK v3 — deferred so it doesn't block rendering -->
<script defer src="https://sdk.crazygames.com/crazygames-sdk-v3.js"></script>
<!-- Popunder ad script — deferred so it doesn't block rendering -->
<script defer src="https://pl28525965.profitablecpmratenetwork.com/ce/37/c2/ce37c287ef68836c85b2c1d396361fd6.js"></script>

<!-- Title, favicon, </head>, and <body> are added by the calling template -->
