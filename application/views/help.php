<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <?php
        $help      = $this->lang->line('help');
        $questions = $help['question'] ?? [];
        $answers   = $help['answer']   ?? [];
        $faqs      = [];
        foreach ($questions as $i => $question) {
            $faqs[] = ['q' => $question, 'a' => $answers[$i] ?? ''];
        }
        ?>

        <div class="home-faq-section home-info-panel not-prose">

            <div class="home-info-panel-header">
                <div>
                    <p class="home-info-panel-eyebrow"><?= htmlspecialchars($help['eyebrow'] ?? 'Need a quick answer?') ?></p>
                    <h2 class="h2"><i class="fa-solid fa-circle-question mr-2"></i><?= htmlspecialchars($title) ?></h2>
                    <!-- Intro may contain a trusted anchor tag defined in the language file -->
                    <p class="home-info-panel-copy mb-0"><?= $introHelp ?></p>
                </div>
                <div class="home-info-panel-stat">
                    <span class="home-info-panel-stat-value"><?= count($faqs) ?></span>
                    <span class="home-info-panel-stat-label"><?= htmlspecialchars($help['stat_label'] ?? 'quick answers') ?></span>
                </div>
            </div>

            <div class="home-faq-grid mt-3">
                <?php foreach ($faqs as $fi => $faq): ?>
                <article class="card bg-base-100 border border-base-300 shadow-sm home-faq-card" role="region" aria-labelledby="faq-q<?= $fi ?>">
                    <div class="card-body">
                        <div id="faq-q<?= $fi ?>" class="card-title home-info-card-title home-faq-card-question">
                            <span><?= htmlspecialchars($faq['q']) ?></span>
                        </div>
                        <!-- Answers may contain trusted HTML (e.g. anchor tags) defined in language files. -->
                        <p class="home-faq-card-answer mb-0"><?= $faq['a'] ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

        </div>

    </div>

</div>
