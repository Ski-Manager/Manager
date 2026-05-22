<?php
$t = $this->lang->line('tutorial');
$steps = [
    ['title' => $t['step1_title'], 'body' => $t['step1_body'], 'icon' => 'bi-house-door'],
    ['title' => $t['step2_title'], 'body' => $t['step2_body'], 'icon' => 'bi-snow'],
    ['title' => $t['step3_title'], 'body' => $t['step3_body'], 'icon' => 'bi-people'],
    ['title' => $t['step4_title'], 'body' => $t['step4_body'], 'icon' => 'bi-building'],
    ['title' => $t['step5_title'], 'body' => $t['step5_body'], 'icon' => 'bi-cash-coin'],
    ['title' => $t['step6_title'], 'body' => $t['step6_body'], 'icon' => 'bi-trophy'],
];
?>
<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <?php echo $title; ?>
        <p class="lead mb-4"><?php echo $t['intro']; ?></p>

        <div class="grid grid-cols-12 gap-3 mb-4">
            <?php foreach ($steps as $i => $step): ?>
            <div class="col">
                <div class="card h-full shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi <?php echo htmlspecialchars($step['icon']); ?> mr-2 text-info"></i>
                            <?php echo $step['title']; ?>
                        </h5>
                        <p class="card-text"><?php echo $step['body']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="card border-warning mb-4">
            <div class="card-header bg-warning font-bold">
                <i class="fa-solid fa-lightbulb mr-1"></i>
                <?php echo htmlspecialchars($t['tip_title']); ?>
            </div>
            <ul class="space-y-1 space-y-1-flush">
                <li class="p-3 border border-base-300 rounded bg-base-100"><?php echo $t['tip1']; ?></li>
                <li class="p-3 border border-base-300 rounded bg-base-100"><?php echo $t['tip2']; ?></li>
                <li class="p-3 border border-base-300 rounded bg-base-100"><?php echo $t['tip3']; ?></li>
                <li class="p-3 border border-base-300 rounded bg-base-100"><?php echo $t['tip4']; ?></li>
            </ul>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="<?php echo base_url('resort_controller'); ?>" class="btn btn-primary">
                <i class="fa-solid fa-house mr-1"></i>
                <?php echo $t['btn_resort']; ?>
            </a>
            <a href="<?php echo base_url('help_controller'); ?>" class="btn btn-outline">
                <i class="fa-regular fa-circle-question mr-1"></i>
                <?php echo $t['btn_help']; ?>
            </a>
        </div>

    </div>
</div>
