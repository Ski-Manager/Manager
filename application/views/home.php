<?php
// -----------------------------
// Config & DB via CI instance
// -----------------------------
$CI =& get_instance();
$secret_token   = $CI->config->item('home_upload_token');
$hcaptcha_secret  = $CI->config->item('hcaptcha_secret');
$hcaptcha_sitekey = $CI->config->item('hcaptcha_sitekey');

$can_upload = isset($_POST['upload_token']) && hash_equals($secret_token, $_POST['upload_token']);

$upload_message = "";

// -----------------------------
// Helpers
// -----------------------------
function hcaptcha_verify(string $secret, string $response): bool {
    if ($response === "") return false;

    $ch = curl_init("https://hcaptcha.com/siteverify");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            "secret" => $secret,
            "response" => $response,
        ]),
        CURLOPT_TIMEOUT => 10,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);

    if (!$raw) return false;
    $json = json_decode($raw, true);
    return !empty($json["success"]);
}

function crop_resize_to_800x436(string $src_path, string $dst_path): bool {
    $data = @file_get_contents($src_path);
    if ($data === false) return false;

    $src = @imagecreatefromstring($data);
    if (!$src) return false;

    $src_w = imagesx($src);
    $src_h = imagesy($src);

    $dst_w = 800;
    $dst_h = 436;
    $dst = imagecreatetruecolor($dst_w, $dst_h);

    // keep transparency if any
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    // center-crop to match aspect ratio
    $src_ratio = $src_w / $src_h;
    $dst_ratio = $dst_w / $dst_h;

    if ($src_ratio > $dst_ratio) {
        // crop width
        $crop_w = (int)round($src_h * $dst_ratio);
        $crop_h = $src_h;
        $src_x = (int)(($src_w - $crop_w) / 2);
        $src_y = 0;
    } else {
        // crop height
        $crop_w = $src_w;
        $crop_h = (int)round($src_w / $dst_ratio);
        $src_x = 0;
        $src_y = (int)(($src_h - $crop_h) / 2);
    }

    imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $crop_w, $crop_h);

    $ok = imagepng($dst, $dst_path);
    imagedestroy($src);
    imagedestroy($dst);

    return $ok;
}

// -----------------------------
// Handle Image Upload
// -----------------------------
if ($can_upload && isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {

    $hcaptcha_response = $_POST['h-captcha-response'] ?? "";

    if (!hcaptcha_verify($hcaptcha_secret, $hcaptcha_response)) {
        $upload_message = "❌ hCaptcha failed.";
    } else {
        $target_dir = __DIR__ . "/../../img/images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        $allowed = ["png","jpg","jpeg"];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed, true)) {
            $upload_message = "❌ Invalid type. Use PNG or JPG.";
        } else {
            // Find next number based on existing files
            $existing = glob($target_dir . "homeIntroImage*.{png,jpg,jpeg}", GLOB_BRACE);
            $max = -1;

            foreach ($existing as $file) {
                if (preg_match('/homeIntroImage(\d+)\./', basename($file), $m)) {
                    $num = (int)$m[1];
                    if ($num > $max) $max = $num;
                }
            }

            $new_num  = $max + 1;
            $new_file = $target_dir . "homeIntroImage{$new_num}.png";

            if (crop_resize_to_800x436($_FILES['image']['tmp_name'], $new_file)) {
                $upload_message = "✅ Uploaded as homeIntroImage{$new_num}.png";
            } else {
                $upload_message = "❌ Could not process image (GD or permissions issue).";
            }
        }
    }
}

// -----------------------------
// Changelog
// -----------------------------
$changelog_entries = [];
$has_category = $CI->db->field_exists('category', 'changelog');
$has_type     = !$has_category && $CI->db->field_exists('type', 'changelog');
if ($has_category) {
    $select = "id, version, change_date, description, category";
} elseif ($has_type) {
    $select = "id, version, change_date, description, type AS category";
} else {
    $select = "id, version, change_date, description";
}
// Normalise misspelled or legacy type values to canonical ENUM keys
$changelog_type_map = [
    'enhancment' => 'enhancement',
    'Enhancement' => 'enhancement',
    'New Feature' => 'feature',
    'Bug Fix'     => 'bug',
    'Balance'     => 'enhancement',
    'UI'          => 'enhancement',
    'Other'       => 'other',
];
$has_public = $CI->db->field_exists('public_visible', 'changelog');
$where = $has_public ? " WHERE public_visible = 1" : "";
$r = $CI->db->query("SELECT {$select} FROM changelog{$where} ORDER BY change_date DESC, id DESC");
if ($r && $r->num_rows() > 0) {
    foreach ($r->result_array() as $row) {
        $ver  = (string)$row['version'];
        $date = date("F j, Y", strtotime($row['change_date']));
        if (!isset($changelog_entries[$ver])) {
            $changelog_entries[$ver] = ['date'=>$date, 'changes'=>[]];
        }
        $raw_cat = isset($row['category']) ? (string)$row['category'] : 'other';
        $cat     = $changelog_type_map[$raw_cat] ?? strtolower($raw_cat);
        $changelog_entries[$ver]['changes'][] = [
            'text'     => (string)$row['description'],
            'category' => $cat,
        ];
    }
}

// Category badge colours (Bootstrap 5 badge classes)
$changelog_badge_class = [
    'feature'     => 'bg-success',
    'bug'         => 'bg-error',
    'enhancement' => 'bg-info',
    'other'       => 'bg-neutral',
];

// Human-readable labels for each ENUM value
$changelog_badge_label = [
    'feature'     => 'New Feature',
    'bug'         => 'Bug Fix',
    'enhancement' => 'Enhancement',
    'other'       => 'Other',
];

// -----------------------------
// Home intro image (fixed image)
// -----------------------------
$introImage = '<picture>'
    . '<source srcset="' . base_url('img/images/homeintroimage.avif') . '" type="image/avif">'
    . '<img src="' . base_url('img/images/homeintroimage.png') . '" alt="' . htmlspecialchars($CI->lang->line('home')['introImage'] ?? 'Ski resort management game', ENT_QUOTES, 'UTF-8') . '" class="img-fluid home-intro-img" width="800" height="436" fetchpriority="high">'
    . '</picture>';
?>

<!-- VideoGame Structured Data (home page only) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "VideoGame",
  "name": "Ski-Manager",
  "url": "<?php echo htmlspecialchars(rtrim(base_url(), '/') . '/', ENT_QUOTES, 'UTF-8'); ?>",
  "description": "Ski-Manager is a free online ski resort management game. Build slopes, lifts, restaurants and hotels, manage your staff and budget, and grow your resort from a small mountain into a world-class destination.",
  "image": "<?php echo htmlspecialchars(rtrim(base_url(), '/') . '/img/images/homeintroimage.png', ENT_QUOTES, 'UTF-8'); ?>",
  "genre": ["Strategy", "Simulation", "Management"],
  "applicationCategory": "BrowserGame",
  "operatingSystem": "Any",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "EUR"
  },
  "inLanguage": ["en", "fr"]
}
</script>

<!-- PAGE CONTENT WRAPPER -->
<div id="page-content-wrapper" class="p-3">
  <div class="container-border">

<!-- HERO SECTION – full-bleed banner image with gradient overlay -->
    <section class="home-hero-full">
      <div class="home-hero-bg">
        <picture>
          <source srcset="<?= base_url('img/images/homeintroimage.avif') ?>" type="image/avif">
          <img src="<?= base_url('img/images/homeintroimage.png') ?>"
               alt="<?= htmlspecialchars($CI->lang->line('home')['introImage'] ?? 'Ski resort management game', ENT_QUOTES, 'UTF-8') ?>"
               class="home-hero-bg-img" width="800" height="436" fetchpriority="high">
        </picture>
        <div class="home-hero-gradient"></div>
      </div>
      <div class="home-hero-content">
        <div class="home-hero-badges">
          <span class="home-hero-badge"><i class="fa-solid fa-circle-check mr-1"></i><?= htmlspecialchars($CI->lang->line('home')['badge_free'] ?? 'Free to Play') ?></span>
          <span class="home-hero-badge"><i class="fa-solid fa-globe mr-1"></i><?= htmlspecialchars($CI->lang->line('home')['badge_browser'] ?? 'Browser-Based') ?></span>
          <span class="home-hero-badge"><i class="fa-solid fa-bolt mr-1"></i><?= htmlspecialchars($CI->lang->line('home')['badge_instant'] ?? 'No Download') ?></span>
        </div>
        <h1 class="home-hero-title"><?= htmlspecialchars($CI->lang->line('home')['intro_heading'] ?? 'The Free Online Ski Resort Management Game') ?></h1>
        <p class="home-hero-lead"><?= htmlspecialchars($CI->lang->line('home')['intro_p1'] ?? 'Ski-Manager is a free-to-play browser game where you design, build and run your own ski resort from the ground up.') ?></p>
        <p class="home-hero-sub"><?= htmlspecialchars($CI->lang->line('home')['intro_p2'] ?? 'Starting with a bare mountain and a tight budget, manage snowmaking, staff, finances and weather to attract thousands of tourists each season.') ?></p>
        <div class="home-hero-cta">
          <a href="<?= base_url('register_controller') ?>" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-rocket mr-2"></i><?= htmlspecialchars($CI->lang->line('home')['cta_play_free'] ?? 'Play Free') ?>
          </a>
          <a href="#how-to-section" class="btn btn-lg home-hero-outline-btn">
            <i class="fa-solid fa-circle-play mr-2"></i><?= htmlspecialchars($CI->lang->line('home')['cta_how_to_play'] ?? 'How to Play') ?>
          </a>
        </div>
      </div>
    </section>

    <!-- Padding wrapper for everything below the full-bleed hero -->
    <div class="home-page-body">

    <!-- LATEST NEWS -->
    <div class="news-below mt-5 mb-5">
      <div class="news-panel-header">
        <i class="fa-solid fa-newspaper"></i>
        <span><?= htmlspecialchars($CI->lang->line('home')['news_panel_title'] ?? 'Latest News', ENT_QUOTES, 'UTF-8') ?></span>
        <span class="news-live-dot"></span>
      </div>
      <div class="news-panel-body-wrap">
        <div class="news-panel-body">
          <?= $news_block ?? '' ?>
        </div>
      </div>
    </div>

    <!-- GAME STATS SECTION -->
    <?php if (!empty($home_stats)): ?>
    <div class="home-section-divider"></div>
    <div class="home-stats-section">
      <div class="home-section-heading text-center mb-4">
        <p class="home-eyebrow"><i class="fa-solid fa-chart-bar mr-1"></i><?= htmlspecialchars($CI->lang->line('home')['stats_eyebrow'] ?? 'Live Statistics') ?></p>
        <h3 class="h3 home-section-title"><?= htmlspecialchars($CI->lang->line('home')['stats_section_title'] ?? 'Ski-Manager by the Numbers') ?></h3>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <?php foreach ($home_stats as $idx => $stat): ?>
        <div class="home-stat-card">
          <div class="home-stat-icon-wrap stat-color-<?= $idx ?>">
            <i class="fa-solid <?= htmlspecialchars($stat['icon']) ?>"></i>
          </div>
          <div class="home-stat-value" data-count="<?= (int) str_replace([' ', ',', '.', '\xc2\xa0'], '', $stat['value']) ?>"><?= htmlspecialchars($stat['value']) ?></div>
          <div class="home-stat-label"><?= htmlspecialchars($stat['label']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- KEY FEATURES SECTION -->
    <div class="home-section-divider"></div>
    <div class="home-features-section mt-2">
      <div class="home-section-heading text-center mb-4">
        <p class="home-eyebrow"><i class="fa-solid fa-star mr-1"></i><?= htmlspecialchars($CI->lang->line('home')['features_eyebrow'] ?? 'Why Ski-Manager') ?></p>
        <h3 class="h3 home-section-title"><?= htmlspecialchars($CI->lang->line('home')['features_section_title'] ?? 'Why Play Ski-Manager?') ?></h3>
      </div>
      <?php
        $features = [
            ['icon'=>'fa-map',            'title'=>$CI->lang->line('home')['feature_build_title']    ?? 'Build Your Resort',     'desc'=>$CI->lang->line('home')['feature_build_desc']    ?? 'Place slopes, lifts, restaurants and hotels on an interactive map. Design your dream ski resort from the ground up.'],
            ['icon'=>'fa-euro-sign',      'title'=>$CI->lang->line('home')['feature_finance_title']  ?? 'Manage Finances',       'desc'=>$CI->lang->line('home')['feature_finance_desc']  ?? 'Balance your budget, take out bank loans, earn revenue from lift tickets and services, and grow your financial empire.'],
            ['icon'=>'fa-snowflake',      'title'=>$CI->lang->line('home')['feature_weather_title']  ?? 'Dynamic Weather',       'desc'=>$CI->lang->line('home')['feature_weather_desc']  ?? 'Adapt to real seasonal weather patterns. Manage snowmaking, grooming and energy to keep your resort running smoothly.'],
            ['icon'=>'fa-people-group',   'title'=>$CI->lang->line('home')['feature_staff_title']    ?? 'Hire &amp; Manage Staff','desc'=>$CI->lang->line('home')['feature_staff_desc']    ?? 'Recruit ski instructors, lift operators, patrol and medical teams. Keep your team happy and your guests safe.'],
            ['icon'=>'fa-trophy',         'title'=>$CI->lang->line('home')['feature_compete_title']  ?? 'Compete &amp; Win',     'desc'=>$CI->lang->line('home')['feature_compete_desc']  ?? 'Join tournaments, climb the global leaderboard, and earn achievements. Challenge other resorts and prove your management skills.'],
            ['icon'=>'fa-arrow-trend-up', 'title'=>$CI->lang->line('home')['feature_grow_title']     ?? 'Grow &amp; Expand',     'desc'=>$CI->lang->line('home')['feature_grow_desc']     ?? 'Unlock new technologies, expand your mountain, build an empire and attract thousands of tourists every season.'],
        ];
      ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($features as $fi => $feature): ?>
        <div class="card bg-base-200 shadow-sm home-feature-card feat-card-<?= $fi ?>">
          <div class="card-body p-5">
            <div class="home-feature-icon-wrap feat-color-<?= $fi ?>">
              <i class="fa-solid <?= htmlspecialchars($feature['icon']) ?>"></i>
            </div>
            <h5 class="card-title text-base font-semibold mb-1"><?= $feature['title'] ?></h5>
            <p class="text-base-content/60 text-sm mb-0"><?= $feature['desc'] ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- HOW TO GET STARTED SECTION -->
    <div class="home-section-divider"></div>
    <div id="how-to-section" class="home-howto-section mt-2">
      <div class="home-section-heading text-center mb-4">
        <p class="home-eyebrow"><i class="fa-solid fa-circle-play mr-1"></i><?= htmlspecialchars($CI->lang->line('home')['howto_eyebrow'] ?? 'Get Started') ?></p>
        <h3 class="h3 home-section-title"><?= htmlspecialchars($CI->lang->line('home')['how_to_play_title'] ?? 'How to Get Started') ?></h3>
      </div>
      <?php
        $steps = [
            ['num'=>1, 'icon'=>'fa-user-plus',       'title'=>$CI->lang->line('home')['how_to_play_step1_title'] ?? 'Create a Free Account',  'desc'=>$CI->lang->line('home')['how_to_play_step1_desc'] ?? 'Register in seconds – no credit card or download needed.'],
            ['num'=>2, 'icon'=>'fa-location-dot',    'title'=>$CI->lang->line('home')['how_to_play_step2_title'] ?? 'Choose Your Mountain',    'desc'=>$CI->lang->line('home')['how_to_play_step2_desc'] ?? 'Pick a location and customise your resort name.'],
            ['num'=>3, 'icon'=>'fa-map',             'title'=>$CI->lang->line('home')['how_to_play_step3_title'] ?? 'Build Slopes &amp; Lifts','desc'=>$CI->lang->line('home')['how_to_play_step3_desc'] ?? 'Draw ski runs and install chairlifts to open for the first skiers.'],
            ['num'=>4, 'icon'=>'fa-building',        'title'=>$CI->lang->line('home')['how_to_play_step4_title'] ?? 'Expand Your Facilities',  'desc'=>$CI->lang->line('home')['how_to_play_step4_desc'] ?? 'Add restaurants, hotels, rental shops and medical stations.'],
            ['num'=>5, 'icon'=>'fa-trophy',          'title'=>$CI->lang->line('home')['how_to_play_step5_title'] ?? 'Manage &amp; Compete',    'desc'=>$CI->lang->line('home')['how_to_play_step5_desc'] ?? 'Monitor finances, run campaigns, organise tournaments and climb the leaderboard.'],
        ];
      ?>
      <div class="home-howto-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <?php foreach ($steps as $step): ?>
        <div class="card bg-base-200 shadow-sm home-howto-step">
          <div class="card-body p-4 text-center items-center">
            <div class="home-step-badge"><?= $step['num'] ?></div>
            <div class="home-step-icon"><i class="fa-solid <?= htmlspecialchars($step['icon']) ?>"></i></div>
            <h6 class="card-title text-sm font-semibold mt-1"><?= htmlspecialchars($step['title']) ?></h6>
            <p class="text-base-content/60 text-xs mb-0"><?= htmlspecialchars($step['desc']) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-5">
        <a href="<?= base_url('register_controller') ?>" class="btn btn-primary btn-lg">
          <i class="fa-solid fa-rocket mr-2"></i><?= htmlspecialchars($CI->lang->line('login_form')['login_create'] ?? 'Create a Free Account') ?>
        </a>
      </div>
    </div>

    <!-- COMMUNITY SECTION -->
    <div class="home-section-divider"></div>
    <div class="home-community-section mt-2 mb-4 p-5 text-center">
      <p class="home-eyebrow"><i class="fa-solid fa-users mr-1"></i><?= htmlspecialchars($CI->lang->line('home')['community_eyebrow'] ?? 'Community') ?></p>
      <h3 class="h3 home-section-title mb-3"><i class="fa-solid fa-comment-dots mr-2"></i><?= htmlspecialchars($CI->lang->line('home')['community_section_title'] ?? 'Join the Community') ?></h3>
      <p class="mb-4 text-base-content/70"><?= htmlspecialchars($CI->lang->line('home')['community_section_desc'] ?? 'Connect with other players, share tips, and stay up to date with the latest news.') ?></p>
      <div class="flex flex-wrap gap-3 justify-center">
        <a href="https://discordapp.com/channels/593708638212718632/593708638695325696" target="_blank" rel="noopener noreferrer" class="btn btn-discord btn-lg">
          <i class="fa-brands fa-discord mr-2"></i>Discord
        </a>
        <a href="https://www.facebook.com/Ski-Manager-1377272882355150/" target="_blank" rel="noopener noreferrer" class="btn btn-facebook btn-lg">
          <i class="fa-brands fa-facebook mr-2"></i>Facebook
        </a>
        <a href="<?= base_url('contact_controller') ?>" class="btn btn-contact btn-lg">
          <i class="fa-solid fa-envelope mr-2"></i><?= htmlspecialchars($CI->lang->line('navbar')['contact'] ?? 'Contact Us') ?>
        </a>
      </div>
    </div>

    <!-- FAQ SECTION -->
    <div class="home-faq-section mt-5">
      <?php
        $faqs = [
            ['q'=>$CI->lang->line('home')['faq_q1'] ?? 'Is Ski-Manager free to play?',               'a'=>$CI->lang->line('home')['faq_a1'] ?? 'Yes, Ski-Manager is 100% free to play.'],
            ['q'=>$CI->lang->line('home')['faq_q2'] ?? 'Do I need to download anything?',             'a'=>$CI->lang->line('home')['faq_a2'] ?? 'No download or installation required. Ski-Manager runs in any web browser.'],
            ['q'=>$CI->lang->line('home')['faq_q3'] ?? 'How do I start playing?',                     'a'=>$CI->lang->line('home')['faq_a3'] ?? 'Create a free account and follow the in-game tutorial.'],
            ['q'=>$CI->lang->line('home')['faq_q4'] ?? 'Can I play in multiple languages?',           'a'=>$CI->lang->line('home')['faq_a4'] ?? 'Yes! Ski-Manager is available in English and French.'],
            ['q'=>$CI->lang->line('home')['faq_q5'] ?? 'How is my resort compared to other players?', 'a'=>$CI->lang->line('home')['faq_a5'] ?? 'Your resort is scored on the global leaderboard based on reputation, visitors and results.'],
        ];
      ?>
      <div class="hp-faq-panel">
        <div class="hp-faq-panel-header">
          <div>
            <p class="hp-faq-eyebrow"><?= htmlspecialchars($CI->lang->line('home')['faq_eyebrow'] ?? 'FAQ') ?></p>
            <h2 class="h2"><?= htmlspecialchars($CI->lang->line('home')['faq_title'] ?? 'Frequently Asked Questions') ?></h2>
            <p class="hp-faq-panel-intro"><?= htmlspecialchars($CI->lang->line('home')['faq_intro'] ?? 'Everything you need to know before you start playing.') ?></p>
          </div>
          <div class="hp-faq-stat-box">
            <span class="hp-faq-stat-val"><?= count($faqs) ?></span>
            <span class="hp-faq-stat-lbl"><?= htmlspecialchars($CI->lang->line('home')['faq_stat_label'] ?? 'Questions') ?></span>
          </div>
        </div>
        <div class="space-y-2" id="faqAccordion">
          <?php foreach ($faqs as $fi => $faq): ?>
          <details class="collapse collapse-arrow bg-base-200 rounded-xl hp-faq-accordion-item" <?= $fi === 0 ? 'open' : '' ?>>
            <summary class="collapse-title font-medium">
              <?= htmlspecialchars($faq['q']) ?>
            </summary>
            <div class="collapse-content">
              <p class="hp-faq-item-answer"><?= htmlspecialchars($faq['a']) ?></p>
            </div>
          </details>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <?php if ($can_upload): ?>
      <div class="upload-form mt-4 p-3 bg-base-100 rounded shadow-sm">
        <h3 class="h3">Upload New Home Image</h3>

        <?php if ($upload_message): ?>
          <div class="alert alert-info" role="alert"><?= htmlspecialchars($upload_message) ?></div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
          <input type="hidden" name="upload_token" value="<?= htmlspecialchars($secret_token) ?>">
          <label class="label">Select Image</label>
          <input type="file" name="image" class="file-input border border-base-300 w-full mb-3" accept="image/png,image/jpeg" required>

          <div class="h-captcha mb-3" data-sitekey="<?= htmlspecialchars($hcaptcha_sitekey) ?>"></div>

          <button class="btn btn-primary">Upload</button>
        </form>
      </div>
    <?php endif; ?>

    <div class="mt-5">
      <div class="news-panel-header rounded-top">
        <i class="fa-solid fa-clock-rotate-left mr-2"></i><?= htmlspecialchars($CI->lang->line('home')['changelog_title'] ?? 'Changelog') ?>
        <span class="changelog-latest-badge ml-auto"><?= htmlspecialchars($CI->lang->line('home')['changelog_latest_label'] ?? 'latest') ?></span>
      </div>
      <p class="mt-2 mb-3 text-base-content/60 small"><?= htmlspecialchars($CI->lang->line('home')['changelog_intro'] ?? 'Keep up with the latest updates and improvements made to Ski-Manager. Each entry includes the version, date, and description of changes.') ?></p>

    <?php if (!empty($changelog_entries)): ?>
    <?php
        // Badge colour helper (Bootstrap 5 classes)
        $badge_cls = function(string $cat) use ($changelog_badge_class): string {
            return $changelog_badge_class[$cat] ?? 'bg-neutral';
        };
        $badge_lbl = function(string $cat) use ($changelog_badge_label): string {
            return $changelog_badge_label[$cat] ?? ucfirst($cat);
        };
        $entries = array_values($changelog_entries);
    ?>
    <div class="space-y-2" id="changelogAccordion">
      <?php foreach ($entries as $idx => $data): ?>
        <?php $ver = array_keys($changelog_entries)[$idx]; ?>
        <details class="collapse collapse-arrow bg-base-200 rounded-xl" <?= $idx === 0 ? 'open' : '' ?>>
          <summary class="collapse-title">
            <span class="font-semibold mr-2">v<?= htmlspecialchars($ver) ?></span>
            <span class="text-base-content/60 small mr-3"><?= htmlspecialchars($data['date']) ?></span>
            <span class="badge badge-primary"><?= count($data['changes']) ?> change<?= count($data['changes']) !== 1 ? 's' : '' ?></span>
          </summary>
          <div class="collapse-content p-0">
            <?php
              // Group changes by category
              $changes_by_category = [];
              foreach ($data['changes'] as $change) {
                  $changes_by_category[$change['category']][] = $change['text'];
              }
            ?>
            <?php foreach ($changes_by_category as $cat => $texts): ?>
            <div class="changelog-group-inline px-3 pt-2">
              <span class="badge changelog-badge-sm <?= htmlspecialchars($badge_cls($cat)) ?>"><?= htmlspecialchars($badge_lbl($cat)) ?></span>
              <ul class="changelog-changes-list">
                <?php foreach ($texts as $txt): ?>
                <li><?= htmlspecialchars($txt) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php endforeach; ?>
            <div class="pb-2"></div>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-base-content/60"><?= htmlspecialchars($CI->lang->line('home')['changelog_empty'] ?? 'No changelog entries yet.') ?></p>
    <?php endif; ?>

    </div>

    </div><!-- /.home-page-body -->

  </div>
</div>

<script>
/* ── Stat count-up animation ─────────────────────────────────────────────── */
(function () {
    var els = document.querySelectorAll('.home-stat-value[data-count]');
    if (!els.length || !window.IntersectionObserver) return;
    var fmt = function (n) {
        return n.toLocaleString('fr-FR').replace(/,/g, ' ');
    };
    var animate = function (el) {
        var target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target) || target === 0) return;
        var original = el.textContent;
        var start = performance.now();
        var dur = Math.min(1800, Math.max(800, target / 5));
        var tick = function (now) {
            var p = Math.min((now - start) / dur, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = fmt(Math.round(eased * target));
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = original;
        };
        requestAnimationFrame(tick);
    };
    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { animate(e.target); obs.unobserve(e.target); }
        });
    }, { threshold: 0.4 });
    els.forEach(function (el) { obs.observe(el); });
})();
</script>
