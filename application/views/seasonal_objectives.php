<div class="w-full">

    <?php
    echo $title;
    echo $intro;
    ?>

    <!-- Current season info -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="md:col-span-12">
            <p>
                <strong><?php echo htmlspecialchars($lang['current_season'], ENT_QUOTES, 'UTF-8'); ?>:</strong>
                <?php echo (int) $current_season; ?>
                &nbsp;&mdash;&nbsp;
                <strong><?php echo htmlspecialchars($lang['day_of_season'], ENT_QUOTES, 'UTF-8'); ?>:</strong>
                <?php echo htmlspecialchars((string) $day_of_season, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </div>
    </div>

    <!-- Objectives table -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="md:col-span-12">
            <?php echo $objectives_html; ?>
        </div>
    </div>

</div>
