<div class="w-full">
    <?php
    echo '<legend>' . $this->lang->line('admin_page')['playerlist'] . ':</legend>';
    ?>
    <div class="w-full container-border padding_top_bot_15">
        <div class="md:col-span-12">
            <?php echo $delete_button_all;
            $attributes = array('name' => 'impersonate_password', 'id' => 'impersonate_password', 'placeholder' => $this->lang->line('home')['password'], 'class' => 'password', 'size' => '20');
            echo form_password($attributes); ?>
            <div class="overflow-x-auto">
                <table align="center" id="myTable1" class="table table-zebra myTableLeaderboard rounded-table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('admin_page')['playerlist']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['Nickname']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['IDresort']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['email']; ?></th>
                            <th><?php echo $this->lang->line('home')['country_field']; ?></th>
                            <th><?php echo $this->lang->line('signup')['age']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['registration_time']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['last_connection']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['activated']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['Actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Defines todays time
                        $today = strtotime('now');
                        $now_date = gmdate('Y-m-d H:i:s', $today);
                        $now_newdate = new DateTime($now_date);

                        foreach ($data_player as $rec) {

                            $real_diff_last_connection = '';    // initializes interval string
                            $last_connection = gmdate('Y-m-d H:i:s', $rec->last_connection);    // last connection (gmdate format)
                            $last_connection_date = new DateTime($last_connection); // last connection (newdate format)
                            $interval = $now_newdate->diff($last_connection_date);  // interval in date format


                            $activity_class = '';


                            if ($interval->y > 10) {    // If more than 5 years, display never)
                                $real_diff_last_connection = 'Never';
                            } else {
                                if ($interval->y > 0)
                                    $real_diff_last_connection .= $interval->y . ' years ';
                                if ($interval->m > 0)
                                    $real_diff_last_connection .= $interval->m . ' months ';
                                if ($interval->d > 0) {
                                    $real_diff_last_connection .= $interval->d . ' days ';
                                }
                                if ($interval->h > 0 && $interval->y == 0 && $interval->m == 0) {
                                    $real_diff_last_connection .= $interval->h . ' hours ';
                                    $activity_class = 'lightGreen';
                                    if ($interval->d > 5)
                                        $activity_class = 'Orange';
                                }
                                if ($interval->i > 0 && $interval->y == 0 && $interval->m == 0 && $interval->d == 0) {
                                    $real_diff_last_connection .= $interval->i . ' minutes ';
                                    $activity_class = 'Green';
                                }
                                if ($interval->s > 0 && $interval->y == 0 && $interval->m == 0 && $interval->d == 0 && $interval->h == 0  && $interval->m < 10) {
                                    $real_diff_last_connection .= $interval->s . ' seconds';
                                    $activity_class = 'Green';
                                }
                            }

                            $registration_time = strtotime($rec->registration_time);    // registration time
                            $last_connection_time = $rec->last_connection;    // last connection time
                            $interval_registration_and_connection = $last_connection_time - $registration_time;
                            $interval_now_and_connection = $today - $last_connection_time;
                            if ($interval_registration_and_connection < 43200 && $interval_now_and_connection > 43200 && ($interval->d > 10 || $interval->m > 0 || $interval->y > 0)) {
                                $activity_class = 'Red';
                            }


                            // http://www.php.net/manual/en/datetime.diff.php If you want to quickly scan through the resulting intervals, you can use the undocumented properties of DateInterval.

                            echo '<tr data-id_resort="' . $rec->id_resort . '" data-username="' . $rec->username . '" data-id_player="' . $rec->id_player . '">';
                            echo '<td>' . $rec->id_player . '</td>'
                                . '<td>' . htmlspecialchars($rec->username) . '</td>'
                                . '<td>' . $rec->id_resort . '</td>'
                                . '<td>' . htmlspecialchars($rec->email) . '</td>'
                                . '<td>' . htmlspecialchars($rec->country) . '</td>'
                                . '<td>' . $rec->age . '</td>'
                                . '<td>' . date('Y-m-d H:i', strtotime($rec->registration_time)) . '</td>'
                                . '<td class ="' . $activity_class . '">' . $real_diff_last_connection . '</td>'
                                . '<td>' . $rec->activated . '</td>';
                            echo '<td>' . $delete_button . $duplicate_button . $activate_button . $impersonate_button;
                            echo '<a href="' . base_url('admin/admin_player_controller/edit_player/' . $rec->id_player) . '"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center"><?php echo $pagination; ?></div>
        </div>
    </div>

    <div id="dialog-confirm" style="display:none;">
        <?php echo $this->lang->line('admin_page')['confirm_delete']; ?>
    </div>

</div>

<!-- CSS loaded asynchronously to avoid render-blocking and reduce TBT -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'" crossorigin="anonymous">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous"></noscript>
<link rel="preload" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css"></noscript>

<style>
    /* Rounded Corners for the Table */
    .rounded-table {
        border-radius: 10px; /* Adjust as needed for desired roundness */
        overflow: hidden; /* This is crucial to clip the content within the rounded borders */
    }
     .table thead th {
        background-color: #f8f9fa;
        border-b: 2px solid #dee2e6;
        color: #343a40;
    }

    .table-zebra tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) return;
    $('#myTable1').DataTable({
        scrollX: true,
        paging: false
    });
});
</script>