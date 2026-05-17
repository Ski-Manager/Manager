<div class="w-full">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="h4 mb-0"><?php echo $this->lang->line('admin_page')['admin_stats']; ?></h4>
        </div>
        <div class="card-body">
            <ul class="space-y-1">
                <li class="p-3 border border-base-300 rounded bg-base-100 flex justify-between items-center">
                    <?php echo $this->lang->line('admin_page')['activated_accounts']; ?>
                    <span class="badge badge-primary"><?php echo $activated_accounts . '/' . $registered_players; ?></span>
                    <span class="font-weight-light">(<?php echo $ratio_activated; ?>%)</span>
                </li>
                <li class="p-3 border border-base-300 rounded bg-base-100 flex justify-between items-center">
                    <?php echo $this->lang->line('admin_page')['total_resorts']; ?>
                    <span class="badge badge-primary"><?php echo $total_resorts; ?></span>
                    <span class="font-weight-light">(<?php echo $ratio_resorts_activated; ?>% <?php echo $this->lang->line('admin_page')['of_activated_accounts']; ?>)</span>
                </li>
                <li class="p-3 border border-base-300 rounded bg-base-100 flex justify-between items-center">
                    <?php echo $this->lang->line('login_form')['online_players']; ?>
                    <span class="badge badge-success"><?php echo $online_players; ?></span>
                </li>
                <li class="p-3 border border-base-300 rounded bg-base-100">
                    <?php echo $this->lang->line('admin_page')['active']; ?>
                    <div class="progress-group">
                        <div class="progress-label">
                            <span><?php echo $this->lang->line('admin_page')['2days']; ?></span>
                             <span class="float-right"><b><?php echo $active_2days; ?>%</b></span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $active_2days; ?>%" aria-valuenow="<?php echo $active_2days; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                       <div class="progress-label">
                        <span><?php echo $this->lang->line('admin_page')['7days']; ?></span>
                        <span class="float-right"><b><?php echo $active_7days; ?>%</b></span>
                        </div>

                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $active_7days; ?>%" aria-valuenow="<?php echo $active_7days; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <div class="progress-label">
                        <span><?php echo $this->lang->line('admin_page')['30days']; ?></span>
                        <span class="float-right"><b><?php echo $active_30days; ?>%</b></span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $active_30days; ?>%" aria-valuenow="<?php echo $active_30days; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="grid grid-cols-12 gap-3 mt-4">
                <div class="md:col-span-6">
                    <div id="area_chart_accounts" class="chart-container"></div>
                </div>
                <div class="md:col-span-6">
                    <div id="area_chart_resorts" class="chart-container"></div>
                </div>
                <div class="md:col-span-6">
                    <div id="area_chart_lifts" class="chart-container"></div>
                </div>
                <div class="md:col-span-6">
                    <div id="area_chart_slopes" class="chart-container"></div>
                </div>
                <div class="md:col-span-6">
                    <div id="area_chart_achievements" class="chart-container"></div>
                </div>
                <div class="md:col-span-6">
                    <div id="area_chart_daily_visitors" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1rem;
}
.card-header {
    border-b: none;
}
.p-3 border border-base-300 rounded bg-base-100 {
    border: none;
    padding: 0.75rem 1.25rem;
    border-b: 1px solid rgba(0,0,0,.125); /* Subtle border between list items */

}
.p-3 border border-base-300 rounded bg-base-100:last-child {
     border-b: none;
}

.badge {
    font-size: 85%; /* Slightly smaller font size */
}

.progress-group {
    margin-bottom: 1rem;
}
.progress-label{
    display:flex;
    justify-content: space-between;
}
.progress {
    height: 0.5rem; /* Thinner progress bar */
}
.chart-container {
    height: 300px; /* Or whatever height you want for your charts */
    width: 100%; /* Make charts responsive */
    margin-bottom: 1rem;
}
/* Additional styles for better spacing and alignment can be added here */
.flex { /*Bootstrap class*/
    display: flex !important;
}
.justify-between{
    justify-content: space-between !important;
}
.items-center{
    align-items: center !important;
}
.mt-4{
    margin-top: 1.5rem !important;
}
.mb-0{
    margin-bottom: 0 !important;
}
.bg-primary{
    background-color: #007bff !important; /*Bootstrap primary color*/
}
.text-white{
   color: #fff !important;
}
.badge-primary{
   background-color: #007bff !important;
}
.badge-success{
    background-color: #28a745 !important;
}
.bg-success {
    background-color: #28a745!important;
}
.bg-info{
  background-color: #17a2b8 !important;
}
.bg-warning{
 background-color: #ffc107 !important;
}
.font-weight-light{
 font-weight: 300 !important;
}
.badge-pill{
    padding-right: .6em;
    padding-left: .6em;
    border-radius: 10rem;
}
.float-right{
    float:right!important;
}
.progress-sm{
 height: .5rem;
}
</style>