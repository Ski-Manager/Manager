<div class="w-full">
    
    <?php

// General page title
echo $title;
echo $introAchievements;
?>
<script type="text/javascript">
var Settings = (typeof Settings === 'object' && Settings !== null) ? Settings : {};
Settings.achievement_claimed   = <?php echo json_encode($this->lang->line('achievements')['achievement_claimed']); ?>;
Settings.achievement_completed = <?php echo json_encode($this->lang->line('achievements')['achievement_completed']); ?>;
Settings.already_claimed       = <?php echo json_encode($this->lang->line('achievements')['already_claimed']); ?>;
Settings.not_completed         = <?php echo json_encode($this->lang->line('achievements')['not_completed']); ?>;
Settings.share_achievement     = <?php echo json_encode($this->lang->line('achievements')['share_achievement']); ?>;
Settings.share_achievement_text = <?php echo json_encode($this->lang->line('achievements')['share_achievement_text']); ?>;
</script>
<!-- START Achievements Block -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">            
    
        <div class="md:col-span-12"> 

            <?php echo $table_achievements; ?>
                           

        </div>
</div>
<!-- END Achievements block -->

 </div>