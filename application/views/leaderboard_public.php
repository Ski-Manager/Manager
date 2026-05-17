<div class="card bg-base-100 shadow-sm"><div class="card-body mb-3">

<div class="w-full padding_top_bot_15">

    <?php echo $title; ?>
    <?php echo $introLeaderboard; ?>

    <div class="mt-3 mb-4 p-3 bg-base-200 rounded-lg text-sm">
        <i class="fa-solid fa-circle-info mr-1"></i>
        Sign in to see your own resort ranking, compare with players in your region, and track your progress.
        <a href="<?php echo base_url('signup'); ?>" class="btn btn-sm btn-primary ml-2">Play for Free</a>
        <a href="<?php echo base_url('login'); ?>" class="btn btn-sm btn-ghost ml-1">Sign In</a>
    </div>

    <div id="lb-public-skeleton" class="sm-skeleton-table" aria-busy="true">
        <?php for ($i = 0; $i < 10; $i++): ?>
        <div class="sm-skeleton-row">
            <div class="skeleton sm-skeleton-cell" style="width:4%"></div>
            <div class="skeleton sm-skeleton-cell" style="width:18%"></div>
            <div class="skeleton sm-skeleton-cell" style="width:12%"></div>
            <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
            <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
            <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
            <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
            <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
        </div>
        <?php endfor; ?>
    </div>

    <div class="overflow-x-auto hidden" id="lb-public-wrapper">
        <table id="lb-public-table" class="table table-zebra w-full text-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('leaderboard')['resort_name'] ?? 'Resort'; ?></th>
                    <th><?php echo $this->lang->line('leaderboard')['resort_country'] ?? 'Country'; ?></th>
                    <th><?php echo $this->lang->line('leaderboard')['reputation'] ?? 'Reputation'; ?></th>
                    <th><?php echo $this->lang->line('leaderboard')['prestige'] ?? 'Prestige'; ?></th>
                    <th><?php echo $this->lang->line('leaderboard')['lift_count']; ?></th>
                    <th><?php echo $this->lang->line('leaderboard')['slope_count']; ?></th>
                    <th><?php echo $this->lang->line('leaderboard')['staff_count']; ?></th>
                </tr>
            </thead>
            <tbody id="lb-public-tbody"></tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="<?php echo base_url('signup'); ?>" class="btn btn-primary">
            <i class="fa-solid fa-mountain mr-1"></i>Build Your Own Resort
        </a>
    </div>

</div>
</div></div>

<script>
(function(){
    fetch('<?php echo base_url(); ?>leaderboard_controller/getPublicDataTable', {method:'POST'})
        .then(r => r.json())
        .then(function(json){
            var rows = (json.Data || []).slice(0, 25);
            var tbody = document.getElementById('lb-public-tbody');
            rows.forEach(function(row, i){
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>'+(i+1)+'</td>'+
                    '<td>'+escHtml(row.resort_name)+'</td>'+
                    '<td>'+escHtml(row.resort_country)+'</td>'+
                    '<td>'+Number(row.reputation).toLocaleString()+'</td>'+
                    '<td>'+Number(row.prestige).toLocaleString()+'</td>'+
                    '<td>'+(row.lift_count||0)+'</td>'+
                    '<td>'+(row.slope_count||0)+'</td>'+
                    '<td>'+(row.staff_count||0)+'</td>';
                tbody.appendChild(tr);
            });
            document.getElementById('lb-public-skeleton').classList.add('hidden');
            document.getElementById('lb-public-wrapper').classList.remove('hidden');
        })
        .catch(function(){ document.getElementById('lb-public-skeleton').classList.add('hidden'); });

    function escHtml(s){ var d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
})();
</script>
