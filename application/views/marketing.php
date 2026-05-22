<style>
.marketing-page {
    padding: 15px;
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
    transition: color 0.3s ease;
}
.page-title:hover { color: var(--color-secondary); }
.marketing-section {
    background-color: var(--color-base-200);
    border: 1px solid var(--color-base-300);
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 3px 7px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.marketing-section:hover {
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    border-color: var(--color-primary);
}
.action-message {
    margin-bottom: 15px;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.9rem;
}
.intro-section { margin-bottom: 20px; }
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}
.intro-text {
    line-height: 1.6;
    color: var(--color-base-content);
    opacity: .75;
    font-size: 0.95rem;
}
.instructions {
    margin-top: 12px;
    font-style: italic;
    color: var(--color-base-content);
    opacity: 0.6;
    font-size: 0.9rem;
}
.campaign-table {
    margin-top: 25px;
    border: 1px solid var(--color-base-300);
    border-radius: 12px;
    overflow-x: auto;
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.campaign-table:hover {
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    border-color: var(--color-primary);
}
.campaign-table table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--color-base-100);
}
.campaign-table th, .campaign-table td {
    padding: 12px;
    border-bottom: 1px solid var(--color-base-300);
    text-align: left;
    font-size: 0.9rem;
    color: var(--color-base-content);
}
.campaign-table th {
    background-color: var(--color-base-200);
    font-weight: 600;
    font-size: 0.95rem;
}
.campaign-table tr:hover td {
    background-color: color-mix(in oklab, var(--color-base-content), transparent 92%);
}
.captcha-section {
    margin-top: 25px;
    padding: 12px;
    border: 1px solid var(--color-base-300);
    border-radius: 12px;
    background-color: var(--color-base-100);
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.captcha-section:hover {
    border-color: var(--color-primary);
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
.captcha-section label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: var(--color-base-content);
    font-size: 0.95rem;
}
.captcha-section input {
    padding: 6px;
    border: 1px solid var(--color-base-300);
    border-radius: 6px;
    width: 100%;
    max-width: 280px;
    margin-bottom: 10px;
    background-color: var(--color-base-100);
    color: var(--color-base-content);
    font-size: 0.9rem;
}
.captcha-section input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.captcha-section button {
    padding: 8px 16px;
    background-color: var(--color-success);
    color: var(--color-success-content);
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: filter 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 3px 7px rgba(0,0,0,0.15);
    font-size: 0.9rem;
    font-weight: 500;
}
.captcha-section button:hover {
    filter: brightness(1.1);
    transform: scale(1.03);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.hidden { display: none; }
.stats-card {
    background-color: var(--color-base-100);
    border: 1px solid var(--color-base-300);
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.04);
}
.stats-card h5 {
    color: var(--color-primary);
    font-weight: 700;
    margin-bottom: 12px;
    font-size: 1rem;
}
.stats-item {
    display: inline-block;
    text-align: center;
    padding: 10px 20px;
    border-radius: 8px;
    background-color: color-mix(in oklab, var(--color-primary), transparent 85%);
    margin: 5px;
    min-width: 130px;
}
.stats-item .stats-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--color-primary);
    display: block;
}
.stats-item .stats-label {
    font-size: 0.8rem;
    color: var(--color-base-content);
    opacity: .7;
}
.history-section { margin-top: 25px; }
.history-section h5 {
    color: var(--color-primary);
    font-weight: 700;
    margin-bottom: 12px;
    font-size: 1rem;
}
.history-table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--color-base-100);
    border-radius: 8px;
    overflow: hidden;
}
.history-table th, .history-table td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--color-base-300);
    font-size: 0.875rem;
    text-align: left;
    color: var(--color-base-content);
}
.history-table th {
    background-color: var(--color-base-200);
    font-weight: 600;
}
.history-table tr:hover td {
    background-color: color-mix(in oklab, var(--color-base-content), transparent 92%);
}
.badge-completed {
    background-color: color-mix(in oklab, var(--color-success), transparent 75%);
    color: var(--color-success);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.78rem;
    font-weight: 600;
}
.badge-ongoing {
    background-color: color-mix(in oklab, var(--color-warning), transparent 75%);
    color: var(--color-warning);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.78rem;
    font-weight: 600;
}
@media (max-width: 768px) {
    .marketing-page { padding: 10px; }
    .page-title { font-size: 1.75rem; }
    .marketing-section { padding: 10px; }
    .campaign-table { overflow-x: auto; }
    .campaign-table th, .campaign-table td { padding: 10px; font-size: 0.8rem; }
    .captcha-section { padding: 10px; }
    .captcha-section label { font-size: 0.9rem; }
    .captcha-section input { padding: 6px; max-width: 100%; font-size: 0.8rem; }
    .captcha-section button { padding: 8px 12px; font-size: 0.9rem; }
    .stats-item { min-width: 110px; padding: 8px 12px; }
}
</style>
<div class="container marketing-page">
    <h2 class="h2 page-title"><?php echo $this->lang->line('marketing')['title']; ?></h2>

    <?php if (isset($stats_total_published)) : ?>
    <div class="stats-card">
        <h5 class="h5"><?php echo $this->lang->line('marketing')['stats_title']; ?></h5>
        <div>
            <div class="stats-item">
                <span class="stats-value"><?php echo $stats_total_published; ?></span>
                <span class="stats-label"><?php echo $this->lang->line('marketing')['stats_total_published']; ?></span>
            </div>
            <div class="stats-item">
                <span class="stats-value"><?php echo $stats_max_level; ?>/30</span>
                <span class="stats-label"><?php echo $this->lang->line('marketing')['stats_max_level']; ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="marketing-section">
        <?php if (isset($action)) : ?>
            <div class="alert alert-<?php echo $class; ?> text-center action-message">
                <?php echo $this->lang->line('marketing')[$action]; ?>
            </div>
        <?php endif; ?>
        <div class="intro-section">
            <p class="intro-text"><?php echo $this->lang->line('marketing')['intro']; ?></p>
            <p class="instructions"><?php echo $this->lang->line('marketing')['instructions_validate_campaign']; ?></p>
        </div>
        <?php if ($this->session->flashdata('msg')) : ?>
            <div class="alert alert-info"><?php echo $this->session->flashdata('msg'); ?></div>
        <?php endif; ?>
        <?php if (isset($campaign_message)) : ?>
            <div class="alert alert-warning" id="message_campaign"><?php echo $campaign_message; ?></div>
        <?php endif; ?>
        <div class="overflow-x-auto campaign-table">
            <?php echo $table_campaigns; ?>
        </div>
        <div id="captcha_area" class="captcha-section <?php echo (isset($captcha_data) && $captcha_data != '') ? '' : 'hidden'; ?>">
            <?php if (isset($captcha_data) && $captcha_data != ''): ?>
                <?php echo $captcha_data; ?>
            <?php endif; ?>
        </div>

        <?php if (isset($campaign_history) && $campaign_history->num_rows() > 0) : ?>
        <div class="history-section">
            <h5 class="h5"><?php echo $this->lang->line('marketing')['history_title']; ?></h5>
            <div class="overflow-x-auto">
            <table class="history-table">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('marketing')['campaign']; ?></th>
                        <th><?php echo $this->lang->line('marketing')['history_level']; ?></th>
                        <th><?php echo $this->lang->line('marketing')['history_date']; ?></th>
                        <th><?php echo $this->lang->line('marketing')['history_completed']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaign_history->result() as $h) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($h->campaign_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php $lvl = (int)$h->level; $pct = round($lvl / 30 * 100); ?>
                            <div class="flex items-center gap-2" style="min-width:100px">
                                <progress class="progress progress-success" value="<?php echo $pct; ?>" max="100" style="flex:1"></progress>
                                <span class="text-xs opacity-70"><?php echo $lvl; ?>/30</span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($h->last_executed)), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php if ($h->completed == 1) : ?>
                                <span class="badge-completed"><?php echo $this->lang->line('marketing')['history_yes']; ?></span>
                            <?php else : ?>
                                <span class="badge-ongoing"><?php echo $this->lang->line('marketing')['history_no']; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
        <?php elseif (isset($campaign_history) && $campaign_history->num_rows() == 0) : ?>
        <div class="history-section">
            <h5 class="h5"><?php echo $this->lang->line('marketing')['history_title']; ?></h5>
            <p class="intro-text"><?php echo $this->lang->line('marketing')['history_no_campaigns']; ?></p>
        </div>
        <?php endif; ?>
    </div></div>