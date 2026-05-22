<style>
.competitors-page {
    padding: 15px;
    font-family: 'Roboto', sans-serif;
    color: var(--color-base-content);
    line-height: 1.7;
    overflow-x: hidden;
}
.page-title {
    margin-bottom: 25px;
    color: var(--color-primary);
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 1px;
}
.competitors-section {
    background-color: var(--color-base-200);
    border: 1px solid var(--color-base-300);
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 3px 7px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.competitors-section:hover {
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    border-color: var(--color-primary);
}
.intro-text {
    line-height: 1.6;
    color: var(--color-base-content);
    opacity: .75;
    font-size: 0.95rem;
}
.penalty-card {
    background-color: color-mix(in oklab, var(--color-warning), transparent 80%);
    border: 1px solid var(--color-warning);
    border-radius: 10px;
    padding: 12px 18px;
    margin-bottom: 20px;
    font-size: 0.95rem;
    color: var(--color-warning-content);
}
.penalty-card strong {
    font-size: 1.1rem;
}
.competitor-table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--color-base-100);
    border-radius: 8px;
    overflow: hidden;
}
.competitor-table th,
.competitor-table td {
    padding: 11px 13px;
    border-bottom: 1px solid var(--color-base-300);
    font-size: 0.875rem;
    text-align: left;
    vertical-align: middle;
}
.competitor-table th {
    background-color: var(--color-base-200);
    font-weight: 600;
    color: var(--color-base-content);
    font-size: 0.9rem;
}
.competitor-table tr:hover td {
    background-color: color-mix(in oklab, var(--color-base-content), transparent 92%);
}
.bar-wrap {
    background-color: var(--color-base-300);
    border-radius: 6px;
    height: 10px;
    min-width: 80px;
    overflow: hidden;
}
.bar-fill {
    height: 10px;
    border-radius: 6px;
    background-color: var(--color-error);
    transition: width 0.4s ease;
}
.action-btn {
    padding: 6px 12px;
    font-size: 0.8rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.25s ease, transform 0.15s ease;
}
.btn-counter-marketing {
    background-color: var(--color-info);
    color: var(--color-info-content);
    margin-right: 5px;
}
.btn-counter-marketing:hover {
    filter: brightness(1.1);
    color: var(--color-info-content);
    transform: scale(1.03);
}
.btn-mega-lift {
    background-color: var(--color-success);
    color: var(--color-success-content);
}
.btn-mega-lift:hover {
    filter: brightness(1.1);
    color: var(--color-success-content);
    transform: scale(1.03);
}
.action-btn.disabled-btn {
    background-color: var(--color-base-300);
    color: var(--color-base-content);
    opacity: .5;
    cursor: not-allowed;
}
@media (max-width: 768px) {
    .competitors-page { padding: 10px; }
    .page-title { font-size: 1.75rem; }
    .competitor-table th,
    .competitor-table td { padding: 9px 10px; font-size: 0.8rem; }
}
</style>

<div class="container competitors-page">
    <h2 class="h2 page-title"><?php echo $this->lang->line('competitors')['title']; ?></h2>

    <?php if ($action_msg) : ?>
    <div class="alert alert-<?php echo htmlspecialchars($action_class, ENT_QUOTES, 'UTF-8'); ?> text-center mb-3">
        <?php echo htmlspecialchars($action_msg, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php endif; ?>

    <div class="competitors-section">
        <p class="intro-text"><?php echo $this->lang->line('competitors')['intro']; ?></p>

        <?php if ($penalty > 0) : ?>
        <div class="penalty-card">
            <?php echo $this->lang->line('competitors')['current_penalty_label']; ?>
            <strong><?php echo $penalty; ?>%</strong>
            <?php echo $this->lang->line('competitors')['current_penalty_desc']; ?>
        </div>
        <?php else : ?>
        <div class="alert alert-success mb-3" style="font-size:0.9rem;">
            <?php echo $this->lang->line('competitors')['no_penalty']; ?>
        </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
        <table class="competitor-table">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('competitors')['col_name']; ?></th>
                    <th><?php echo $this->lang->line('competitors')['col_reputation']; ?></th>
                    <th><?php echo $this->lang->line('competitors')['col_ticket_price']; ?></th>
                    <th><?php echo $this->lang->line('competitors')['col_marketing']; ?></th>
                    <th><?php echo $this->lang->line('competitors')['col_ticket_discount']; ?></th>
                    <th><?php echo $this->lang->line('competitors')['col_lift_investment']; ?></th>
                    <th><?php echo $this->lang->line('competitors')['col_actions']; ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($competitors->num_rows() > 0) : ?>
                <?php foreach ($competitors->result() as $c) : ?>
                <?php
                    $marketing_pct   = (int) $c->marketing_level * 10;   // 0-100%
                    $discount_pct    = (int) $c->ticket_discount;         // 0-50%
                    $lift_pct        = (int) $c->lift_investment * 20;    // 0-100%
                    $can_afford_mkt  = $cash >= $cost_marketing;
                    $can_afford_lift = $cash >= $cost_mega_lift;
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($c->competitor_name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                    <td><?php echo (int) $c->base_reputation; ?> / 100</td>
                    <td>
                        <?php echo (int) $c->base_ticket_price; ?> €
                        <?php if ($discount_pct > 0) : ?>
                            <span class="badge badge-warning ml-1" style="font-size:0.75rem;">
                                -<?php echo $discount_pct; ?>%
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="bar-wrap" title="<?php echo $c->marketing_level; ?>/10">
                            <div class="bar-fill" style="width:<?php echo $marketing_pct; ?>%"></div>
                        </div>
                        <small><?php echo $c->marketing_level; ?>/10</small>
                    </td>
                    <td>
                        <div class="bar-wrap" title="<?php echo $discount_pct; ?>%">
                            <div class="bar-fill" style="width:<?php echo min(100, $discount_pct * 2); ?>%; background-color:#f59e0b;"></div>
                        </div>
                        <small><?php echo $discount_pct; ?>%</small>
                    </td>
                    <td>
                        <div class="bar-wrap" title="<?php echo $c->lift_investment; ?>/5">
                            <div class="bar-fill" style="width:<?php echo $lift_pct; ?>%; background-color:#8b5cf6;"></div>
                        </div>
                        <small><?php echo $c->lift_investment; ?>/5</small>
                    </td>
                    <td>
                        <?php if ($can_afford_mkt) : ?>
                        <a href="<?php echo base_url() . 'competitors_controller/counter_marketing/' . $c->id_player_competitor; ?>"
                           class="action-btn btn-counter-marketing"
                           title="<?php echo $this->lang->line('competitors')['btn_counter_marketing_title']; ?>">
                            📣 <?php echo $this->lang->line('competitors')['btn_counter_marketing']; ?>
                        </a>
                        <?php else : ?>
                        <span class="action-btn disabled-btn" title="<?php echo $this->lang->line('competitors')['error_not_enough_cash']; ?>">
                            📣 <?php echo $this->lang->line('competitors')['btn_counter_marketing']; ?>
                        </span>
                        <?php endif; ?>

                        <?php if ($can_afford_lift) : ?>
                        <a href="<?php echo base_url() . 'competitors_controller/invest_mega_lift/' . $c->id_player_competitor; ?>"
                           class="action-btn btn-mega-lift"
                           title="<?php echo $this->lang->line('competitors')['btn_mega_lift_title']; ?>">
                            🚡 <?php echo $this->lang->line('competitors')['btn_mega_lift']; ?>
                        </a>
                        <?php else : ?>
                        <span class="action-btn disabled-btn" title="<?php echo $this->lang->line('competitors')['error_not_enough_cash']; ?>">
                            🚡 <?php echo $this->lang->line('competitors')['btn_mega_lift']; ?>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="text-center text-base-content/60" style="padding:20px;">
                        <?php echo $this->lang->line('competitors')['no_competitors']; ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>

        <div class="mt-3 text-sm opacity-60">
            <strong><?php echo $this->lang->line('competitors')['cost_label']; ?></strong>
            <?php echo $this->lang->line('competitors')['cost_counter_marketing_label']; ?>
            <strong><?php echo number_format($cost_marketing, 0, '.', ' '); ?> €</strong>
            &nbsp;|&nbsp;
            <?php echo $this->lang->line('competitors')['cost_mega_lift_label']; ?>
            <strong><?php echo number_format($cost_mega_lift, 0, '.', ' '); ?> €</strong>
        </div>
    </div>
</div>
