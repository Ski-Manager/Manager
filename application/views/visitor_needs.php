<div class="w-full">
<?php

$vn = $this->lang->line('visitor_needs');

echo '<h2 class="h2"><i class="fa-regular fa-face-smile mr-2"></i>' . $vn['page_title'] . '</h2>';
echo '<p>' . $vn['page_intro'] . '</p>';

// Helper: badge class and label based on score
function vn_badge(float $score, array $vn): string {
    if ($score >= 75)     { return 'bg-success'; }
    elseif ($score >= 50) { return 'bg-warning'; }
    elseif ($score >= 25) { return 'bg-orange'; }
    else                  { return 'bg-error'; }
}
function vn_label(float $score, array $vn): string {
    if ($score >= 75)     { return $vn['score_excellent']; }
    elseif ($score >= 50) { return $vn['score_good']; }
    elseif ($score >= 25) { return $vn['score_average']; }
    else                  { return $vn['score_poor']; }
}
function vn_bar_color(float $score): string {
    if ($score >= 75)     { return 'progress-success'; }
    elseif ($score >= 50) { return 'progress-warning'; }
    elseif ($score >= 25) { return 'progress-warning'; }
    else                  { return 'progress-error'; }
}

$needs = [
    'hunger'  => ['icon' => 'bi-cup-hot',         'score' => $hunger_score,  'title' => $vn['hunger_title'],  'desc' => $vn['hunger_desc'],  'tip' => $vn['tip_hunger']],
    'fatigue' => ['icon' => 'bi-battery-half',     'score' => $fatigue_score, 'title' => $vn['fatigue_title'], 'desc' => $vn['fatigue_desc'], 'tip' => $vn['tip_fatigue']],
    'warmth'  => ['icon' => 'bi-thermometer-sun',  'score' => $warmth_score,  'title' => $vn['warmth_title'],  'desc' => $vn['warmth_desc'],  'tip' => $vn['tip_warmth']],
    'fun'     => ['icon' => 'bi-stars',            'score' => $fun_score,     'title' => $vn['fun_title'],     'desc' => $vn['fun_desc'],     'tip' => $vn['tip_fun']],
];

?>

<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12">

    <!-- Overall satisfaction card -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-4" style="max-width:700px;">
        <h5 class="h5"><?php echo $vn['overall_title']; ?></h5>
        <p class="text-base-content/60 small"><?php echo $vn['overall_desc']; ?></p>

        <div class="flex items-center gap-2 mb-2">
            <progress class="progress <?php echo vn_bar_color((float)$needs_satisfaction); ?> flex-1" style="height:22px;" value="<?php echo (int)$needs_satisfaction; ?>" max="100"></progress>
            <span class="font-bold text-sm" style="min-width:3em;"><?php echo number_format($needs_satisfaction, 1); ?></span>
        </div>

        <p class="mb-0">
            <?php echo $vn['revenue_label']; ?>:
            <strong><?php echo number_format($revenue_multiplier, 2); ?> &times;</strong>
        </p>
    </div>

    <!-- Four need cards -->
    <div class="grid gap-3" style="max-width:900px;">
    <?php foreach ($needs as $key => $need):
        $score      = (float)$need['score'];
        $bar_color  = vn_bar_color($score);
        $badge_class = vn_badge($score, $vn);
        $label       = vn_label($score, $vn);
    ?>
        <div class="col-span-12 md:col-span-6">
            <div class="card h-full">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi <?php echo $need['icon']; ?> mr-2"></i>
                        <?php echo $need['title']; ?>
                        <span class="badge <?php echo $badge_class; ?> ml-2" style="font-size:0.8em;">
                            <?php echo $label; ?>
                        </span>
                    </h5>
                    <p class="card-text text-base-content/60 small"><?php echo $need['desc']; ?></p>

                    <progress class="progress <?php echo $bar_color; ?> w-full mb-1" style="height:14px;" value="<?php echo (int)$score; ?>" max="100"></progress>
                    <div class="flex justify-between">
                        <small class="text-base-content/60">0</small>
                        <small><strong><?php echo number_format($score, 1); ?> / 100</strong></small>
                        <small class="text-base-content/60">100</small>
                    </div>

                    <p class="mt-2 mb-0 text-base-content/60 small"><?php echo $need['tip']; ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div><!-- row -->

    <p class="text-base-content/60 mt-3"><small><?php echo $vn['last_updated']; ?></small></p>

</div>
</div><!-- w-full -->

</div><!-- w-full offset -->
