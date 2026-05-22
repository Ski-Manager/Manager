<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['celebrity_visit_title'] . '</h2>';
echo '<p>'  . $this->lang->line('building')['celebrity_visit_intro'] . '</p>';

?>

<!-- ===== How it works ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['celebrity_visit_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['celebrity_visit_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo sprintf($this->lang->line('building')['celebrity_visit_mechanic_chance'], $visit_chance); ?></li>
                <li><?php echo $this->lang->line('building')['celebrity_visit_mechanic_types']; ?></li>
                <li><?php echo sprintf($this->lang->line('building')['celebrity_visit_mechanic_good_slopes'], $rep_good_slopes); ?></li>
                <li><?php echo sprintf($this->lang->line('building')['celebrity_visit_mechanic_base'], $rep_base); ?></li>
                <li><?php echo sprintf($this->lang->line('building')['celebrity_visit_mechanic_lift_fail'], $rep_lift_fail); ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Visit history ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <h4 class="h4"><?php echo sprintf($this->lang->line('building')['celebrity_visit_history_title'], $history_days); ?></h4>

        <?php if (empty($visits)): ?>
            <p class="text-base-content/60"><?php echo $this->lang->line('building')['celebrity_visit_no_history']; ?></p>
        <?php else: ?>
            <table class="table table-sm table-zebra" style="max-width:700px;">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('building')['celebrity_visit_col_date']; ?></th>
                        <th><?php echo $this->lang->line('building')['celebrity_visit_col_type']; ?></th>
                        <th><?php echo $this->lang->line('building')['celebrity_visit_col_slopes']; ?></th>
                        <th><?php echo $this->lang->line('building')['celebrity_visit_col_lift']; ?></th>
                        <th><?php echo $this->lang->line('building')['celebrity_visit_col_rep']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visits as $v): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($v->visit_date); ?></td>
                        <td><?php echo $this->lang->line('building')['celebrity_visit_type_' . $v->visit_type]; ?></td>
                        <td>
                            <?php if ($v->slopes_good): ?>
                                <span class="badge badge-success"><?php echo $this->lang->line('building')['celebrity_visit_slopes_good']; ?></span>
                            <?php else: ?>
                                <span class="badge badge-neutral"><?php echo $this->lang->line('building')['celebrity_visit_slopes_avg']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($v->lift_failed): ?>
                                <span class="badge badge-error"><?php echo $this->lang->line('building')['celebrity_visit_lift_failed']; ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><?php echo $this->lang->line('building')['celebrity_visit_lift_ok']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $rep = (int)$v->rep_change;
                            if ($rep > 0) {
                                echo '<span class="text-success font-bold">+' . $rep . '</span>';
                            } elseif ($rep < 0) {
                                echo '<span class="text-error font-bold">' . $rep . '</span>';
                            } else {
                                echo '<span class="text-base-content/60">0</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</div>

<style>
.container-border {
    border: 1px solid #ccc;
    padding: 15px;
    margin-top: 20px;
    border-radius: 5px;
    background-color: #f9f9f9;
}
.padding_top_bot_15 {
    padding-top: 15px;
    padding-bottom: 15px;
}
</style>
