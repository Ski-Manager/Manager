<div class="w-full">
    <div class="container-border p-3">

        <!-- Hero section -->
        <div class="grid grid-cols-12 gap-3 items-center mb-5">
            <div class="md:col-span-6 mb-4 mb-md-0">
                <h1 class="h1 mb-3"><?php echo htmlspecialchars($this->lang->line('home')['index2_controller_title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="lead mb-4"><?php echo htmlspecialchars($this->lang->line('home')['index2_controller_intro'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo base_url('register_controller'); ?>" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-user-plus mr-2"></i><?php echo htmlspecialchars($this->lang->line('login_form')['login_create'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <a href="<?php echo base_url('home_controller'); ?>" class="btn btn-outline btn-lg">
                        <i class="fa-solid fa-house mr-2"></i><?php echo htmlspecialchars($this->lang->line('navbar')['home'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>
            </div>
            <div class="md:col-span-6 text-center">
                <img src="<?php echo base_url('img/images/homeintroimage.avif'); ?>"
                     alt="<?php echo htmlspecialchars($this->lang->line('home')['introImage'], ENT_QUOTES, 'UTF-8'); ?>"
                     class="img-fluid rounded shadow"
                     width="800" height="436"
                     fetchpriority="high">
            </div>
        </div>

        <!-- Feature cards -->
        <h2 class="h2 text-center mb-4">
            <i class="fa-regular fa-stars mr-2"></i><?php echo htmlspecialchars($this->lang->line('home')['index2_features_title'], ENT_QUOTES, 'UTF-8'); ?>
        </h2>
        <div class="grid grid-cols-12 gap-3 mb-5">
            <?php
            $index2_features = [
                ['icon' => 'bi-gift-fill',       'text' => $this->lang->line('home')['index2_feature_1']],
                ['icon' => 'bi-map-fill',         'text' => $this->lang->line('home')['index2_feature_2']],
                ['icon' => 'bi-people-fill',      'text' => $this->lang->line('home')['index2_feature_3']],
                ['icon' => 'bi-trophy-fill',      'text' => $this->lang->line('home')['index2_feature_4']],
                ['icon' => 'bi-cloud-snow-fill',  'text' => $this->lang->line('home')['feature_weather_title'] ?? 'Dynamic Weather'],
                ['icon' => 'bi-currency-euro',    'text' => $this->lang->line('home')['feature_finance_title'] ?? 'Manage Finances'],
                ['icon' => 'bi-graph-up-arrow',   'text' => $this->lang->line('home')['feature_grow_title']    ?? 'Grow & Expand'],
                ['icon' => 'bi-controller',       'text' => $this->lang->line('navbar')['minigames']           ?? 'Minigames'],
            ];
            foreach ($index2_features as $f):
            ?>
            <div class="col">
                <div class="home-feature-card h-full p-3 text-center">
                    <div class="home-feature-icon mb-2"><i class="bi <?php echo htmlspecialchars($f['icon']); ?>"></i></div>
                    <p class="mb-0 small"><?php echo $f['text']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- How to get started (re-uses home lang keys) -->
        <h2 class="h2 text-center mb-4">
            <i class="fa-solid fa-circle-play mr-2"></i><?php echo htmlspecialchars($this->lang->line('home')['how_to_play_title'] ?? 'How to Get Started', ENT_QUOTES, 'UTF-8'); ?>
        </h2>
        <?php
        $steps = [
            ['num' => 1, 'icon' => 'bi-person-plus-fill', 'title' => $this->lang->line('home')['how_to_play_step1_title'] ?? 'Create a Free Account',   'desc' => $this->lang->line('home')['how_to_play_step1_desc'] ?? 'Register in seconds.'],
            ['num' => 2, 'icon' => 'bi-geo-alt-fill',      'title' => $this->lang->line('home')['how_to_play_step2_title'] ?? 'Choose Your Mountain',     'desc' => $this->lang->line('home')['how_to_play_step2_desc'] ?? 'Pick a location and customise your resort name.'],
            ['num' => 3, 'icon' => 'bi-map-fill',          'title' => $this->lang->line('home')['how_to_play_step3_title'] ?? 'Build Slopes &amp; Lifts', 'desc' => $this->lang->line('home')['how_to_play_step3_desc'] ?? 'Draw ski runs and install chairlifts.'],
            ['num' => 4, 'icon' => 'bi-building-fill',     'title' => $this->lang->line('home')['how_to_play_step4_title'] ?? 'Expand Facilities',        'desc' => $this->lang->line('home')['how_to_play_step4_desc'] ?? 'Add restaurants, hotels and more.'],
            ['num' => 5, 'icon' => 'bi-trophy-fill',       'title' => $this->lang->line('home')['how_to_play_step5_title'] ?? 'Manage &amp; Compete',     'desc' => $this->lang->line('home')['how_to_play_step5_desc'] ?? 'Monitor finances and climb the leaderboard.'],
        ];
        ?>
        <div class="grid grid-cols-12 gap-3 mb-5">
            <?php foreach ($steps as $step): ?>
            <div class="col">
                <div class="home-howto-card h-full p-3 text-center">
                    <div class="home-howto-num mb-2"><?php echo $step['num']; ?></div>
                    <div class="home-feature-icon mb-2"><i class="bi <?php echo htmlspecialchars($step['icon']); ?>"></i></div>
                    <h3 class="h3 font-semibold"><?php echo htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="text-base-content/60 small mb-0"><?php echo htmlspecialchars($step['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA -->
        <div class="text-center py-4">
            <a href="<?php echo base_url('register_controller'); ?>" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-user-plus mr-2"></i><?php echo htmlspecialchars($this->lang->line('login_form')['login_create'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>

    </div>
</div>
