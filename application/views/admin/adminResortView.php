<?php
// Assuming ACTIVE_SECTORS is defined elsewhere
// const ACTIVE_SECTORS = 10; // Replace 10 with the actual value
?>

<div class="w-full">
    <?php
    echo '<h2 class="h2 page-title">' . $this->lang->line('admin_page')['resortlist'] . '</h2>';
    ?>
    <div class="overflow-x-auto">  <div class="button-group" style="margin-bottom: 15px;">
            <?php echo $delete_button_all; ?>
        </div>

        <table id="myTable1" class="table table-zebra table-bordered myTableLeaderboard rounded-table">
            <thead>
                <tr>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['IDresort']; ?></th>
                    <th><?php echo $this->lang->line('admin_page')['Resortname']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['IDplayer']; ?></th>
                    <th class="nickname-column"><?php echo $this->lang->line('admin_page')['Nickname']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Sectors']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('home')['big_slopes']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('home')['big_lifts']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Build']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Staff']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Equip']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Cash']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Rep']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Visitors']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Injuries']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Skipass']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Status']; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('admin_page')['Actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data_resort as $rec_resort) : ?>
                    <tr data-id_resort="<?php echo $rec_resort->id_resort; ?>" data-id_player="<?php echo $rec_resort->id_player; ?>">
                        <td class="text-center"><?php echo $rec_resort->id_resort; ?></td>
                        <td><?php echo htmlspecialchars($rec_resort->resort_name); ?></td>
                        <td class="text-center"><?php echo $rec_resort->id_player; ?></td>
                        <td><?php echo htmlspecialchars($rec_resort->username); ?></td>
                        <td class="text-center">
                            <?php
                            for ($sector_id = 1; $sector_id <= ACTIVE_SECTORS; $sector_id++) : ?>
                                <span class="badge"><?php echo htmlspecialchars((string)$sector[$rec_resort->id_resort][$sector_id]); ?></span>
                            <?php endfor; ?>
                        </td>
                        <td class="text-center"><?php echo $num_slopes[$rec_resort->id_resort]; ?></td>
                        <td class="text-center"><?php echo $num_lifts[$rec_resort->id_resort]; ?></td>
                        <td class="text-center"><?php echo $num_buildings[$rec_resort->id_resort]; ?></td>
                        <td class="text-center"><?php echo $num_staff[$rec_resort->id_resort]; ?></td>
                        <td class="text-center"><?php echo $num_equipments[$rec_resort->id_resort]; ?></td>
                        <td class="text-center"><?php echo number_format((float)$rec_resort->cash, 2, ',', ' '); ?> €</td>
                        <td class="text-center"><?php echo $rec_resort->reputation; ?></td>
                        <td class="text-center"><?php echo $visitors_today[$rec_resort->id_resort] . ' / ' . $visitors_sum[$rec_resort->id_resort]; ?></td>
                        <td class="text-center"><?php echo $injuries_today[$rec_resort->id_resort] . ' / ' . $injuries_sum[$rec_resort->id_resort]; ?></td>
                        <td class="text-center"><?php echo $rec_resort->skipass_daily . ' / ' . $rec_resort->skipass_weekly; ?></td>
                        <td class="text-center"><?php echo htmlspecialchars((string)$status_tourist_info[$rec_resort->id_resort]['id_status']); ?></td>
                         <td class="text-center">
                            <button class="btn btn-error btn-sm btn-delete" data-id="<?php echo $rec_resort->id_resort; ?>"><i class="fas fa-trash"></i></button>
                            <a href="<?php echo base_url('admin/admin_resort_controller/edit_resort/' . $rec_resort->id_resort); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <dialog id="confirmDeleteModal" class="modal modal-middle" aria-labelledby="confirmDeleteModalLabel">
        <div class="modal-box">
            <h5 class="h5 font-bold text-lg mb-4" id="confirmDeleteModalLabel"><?php echo $this->lang->line('admin_page')['confirm_delete_title']; ?></h5>
            <div class="mb-4">
                <?php echo $this->lang->line('admin_page')['confirm_delete']; ?>
            </div>
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('confirmDeleteModal').close();"><?php echo $this->lang->line('admin_page')['cancel']; ?></button>
                <button type="button" class="btn btn-error" id="confirmDeleteButton"><?php echo $this->lang->line('admin_page')['delete']; ?></button>
            </div>
        </div>
    </dialog>
</div>

<!-- CSS loaded asynchronously to avoid render-blocking and reduce TBT -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'" crossorigin="anonymous">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous"></noscript>
<link rel="preload" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css"></noscript>

<style>
/* ... (Your other CSS styles) ... */
.rounded-table {
    border-radius: 10px;
    overflow: hidden;
}
.modal-box {
    border-radius: 10px;
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
.page-title{
    margin-bottom: 1rem;
}
.badge{
    margin-right: 3px;
}
.nickname-column{
    width:15%;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) return;

    $('#myTable1').DataTable({
        scrollX: true
    });

    let deleteId;
    var confirmDeleteModal = document.getElementById('confirmDeleteModal');

    // Show modal when any delete button is clicked
    $('.btn-delete').on('click', function() {
        deleteId = $(this).data('id');
        confirmDeleteModal.showModal();
    });

    $('#confirmDeleteButton').on('click', function() {
        $.ajax({
            url: '<?php echo base_url("admin/admin_resort_controller/delete_resort"); ?>',
            type: 'POST',
            data: { id_resort: deleteId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Use fnDestroy() to properly remove the row and DataTables bindings
                    $('#myTable1').DataTable().row('[data-id_resort="' + deleteId + '"]').remove().draw(false);
                    confirmDeleteModal.close();
                    smToast(response.message, 'success');
                } else {
                    smToast(response.message, 'error');
                    confirmDeleteModal.close();
                }
            },
            error: function() {
                smToast('An error occurred during deletion.', 'error');
                confirmDeleteModal.close();
            }
        });
    });
});
</script>
