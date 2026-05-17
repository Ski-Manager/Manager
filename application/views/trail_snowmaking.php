<div class="w-full">
<?php
// ── Flash / info message ─────────────────────────────────────────────────
if (isset($infoMessage) && $infoMessage != '') {
    $msg_key = $infoMessage;
    if ($msg_key === 'not_enough_money') {
        echo '<div class="alert alert-error mb-4">💸 ' . $this->lang->line('home')['not_enough_money'] . '</div>';
    } elseif (!empty($this->lang->line('building')[$msg_key])) {
        echo $this->lang->line('building')[$msg_key];
    }
}

// ── Precompute display vars ──────────────────────────────────────────────
$snow_cm          = (int)($current_snow_level       ?? 0);
$snow_pct         = (int)($snow_level_bar_percent   ?? 0);
$snow_max_cm      = (int)($snow_max                 ?? MAX_SNOW_LEVEL);
$water_level      = (int)($water_reservoir_level    ?? 0);
$has_reservoir    = !empty($water_reservoir_purchased);
$sm_staff         = (int)($snowmaker_count          ?? 0);
$sm_req           = (int)($snowmaker_required       ?? SNOWMAKING_MIN_STAFF);
$sm_mode          = $snowmaking_mode                ?? 'normal';
$snow_target      = (int)($cannon_target_snow       ?? 0);
$sched_mask       = (int)($snowmaking_schedule      ?? 127);
$is_above_frzg    = !empty($above_freezing);
$trail_count      = (int)($trail_equipment_count    ?? 0);
$trail_active     = (int)($trail_equipment_active   ?? 0);
$trail_proj       = (int)($trail_projected_output   ?? 0);
$trail_cost       = (int)($trail_daily_cost         ?? 0);

$mode_icons   = ['normal' => '⚙️', 'eco' => '🌿', 'boost' => '🚀'];
$mode_colors  = ['normal' => '#10b981', 'eco' => '#3b82f6', 'boost' => '#f59e0b'];
$mode_texts   = ['normal' => $this->lang->line('building')['snowmaking_mode_normal'],
                 'eco'    => $this->lang->line('building')['snowmaking_mode_eco'],
                 'boost'  => $this->lang->line('building')['snowmaking_mode_boost']];

$snow_bar_col = $snow_pct >= 60 ? '#10b981' : ($snow_pct >= 30 ? '#f59e0b' : '#ef4444');
$water_bar_col = $water_level >= 50 ? '#3b82f6' : ($water_level >= 20 ? '#f59e0b' : '#ef4444');
?>

<!-- ===== MOUNTAIN/SNOW BANNER ===== -->
<div style="background:linear-gradient(135deg,#0d1b2a 0%,#0f2744 55%,#1a4a7a 100%);border-radius:12px;padding:2.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.06);">

  <!-- Background SVG: mountains + snow dots -->
  <svg style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0.18;" viewBox="0 0 1000 260" preserveAspectRatio="xMidYMid slice">
    <!-- Snow particles -->
    <circle cx="60"  cy="25"  r="2"   fill="white"/><circle cx="130" cy="55"  r="1.5" fill="white" opacity=".7"/>
    <circle cx="195" cy="20"  r="2.5" fill="white"/><circle cx="270" cy="70"  r="1"   fill="white" opacity=".6"/>
    <circle cx="340" cy="35"  r="2"   fill="white"/><circle cx="420" cy="85"  r="1.5" fill="white" opacity=".8"/>
    <circle cx="490" cy="18"  r="1.8" fill="white"/><circle cx="565" cy="60"  r="1"   fill="white" opacity=".6"/>
    <circle cx="630" cy="40"  r="2.2" fill="white"/><circle cx="710" cy="80"  r="1.5" fill="white" opacity=".7"/>
    <circle cx="785" cy="25"  r="2"   fill="white"/><circle cx="855" cy="65"  r="1"   fill="white" opacity=".6"/>
    <circle cx="920" cy="30"  r="2.5" fill="white"/>
    <circle cx="100" cy="150" r="1.5" fill="white" opacity=".5"/>
    <circle cx="300" cy="170" r="2"   fill="white" opacity=".6"/>
    <circle cx="500" cy="155" r="1.2" fill="white" opacity=".5"/>
    <circle cx="750" cy="165" r="1.8" fill="white" opacity=".6"/>
    <!-- Mountain silhouettes -->
    <polygon points="0,260 120,130 240,185 380,80 510,150 640,65 770,125 900,50 1000,90 1000,260" fill="rgba(147,197,253,0.12)"/>
    <polygon points="0,260 180,195 360,240 560,185 740,215 950,195 1000,200 1000,260" fill="rgba(255,255,255,0.07)"/>
  </svg>

  <div style="position:relative;z-index:1;">
    <!-- Title row -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
      <div>
        <h3 style="color:white;font-size:1.75rem;font-weight:bold;text-shadow:0 0 20px rgba(147,197,253,0.4);margin:0;">
          ❄️ <?php echo $this->lang->line('building')['trail_sm_title']; ?>
        </h3>
        <p style="color:rgba(255,255,255,0.65);margin:0.4rem 0 0;font-size:0.88rem;">
          <?php echo $this->lang->line('building')['trail_sm_intro']; ?>
        </p>
      </div>
      <div style="background:rgba(255,255,255,0.1);border-radius:8px;padding:0.6rem 1.1rem;border:1px solid rgba(255,255,255,0.15);text-align:center;">
        <div style="color:rgba(255,255,255,0.6);font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;">Mode</div>
        <div style="color:white;font-weight:bold;font-size:1.05rem;"><?php echo $mode_icons[$sm_mode]; ?> <?php echo $mode_texts[$sm_mode]; ?></div>
      </div>
    </div>

    <!-- Stats cards -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;">

      <!-- Snow Level -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">🏔 Resort Snow</div>
        <div style="color:white;font-size:1.45rem;font-weight:bold;"><?php echo $snow_cm; ?> <span style="font-size:.75rem;opacity:.65;">cm</span></div>
        <div style="background:rgba(255,255,255,0.12);border-radius:4px;height:4px;margin-top:.45rem;">
          <div style="background:<?php echo $snow_bar_col; ?>;height:4px;border-radius:4px;width:<?php echo $snow_pct; ?>%;"></div>
        </div>
        <div style="color:rgba(255,255,255,0.4);font-size:.68rem;margin-top:.25rem;"><?php echo $snow_pct; ?>% of <?php echo $snow_max_cm; ?> cm max</div>
      </div>

      <!-- Projected Tonight -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">🌨 Projected Tonight</div>
        <?php if ($is_above_frzg): ?>
          <div style="color:#f87171;font-size:1.2rem;font-weight:bold;">⚠️ 0 cm</div>
          <div style="color:rgba(248,113,113,0.75);font-size:.7rem;margin-top:.2rem;">Too warm</div>
        <?php elseif (!$has_reservoir || $water_level <= 0): ?>
          <div style="color:#f87171;font-size:1.2rem;font-weight:bold;">⚠️ 0 cm</div>
          <div style="color:rgba(248,113,113,0.75);font-size:.7rem;margin-top:.2rem;">No water</div>
        <?php elseif ($trail_active === 0): ?>
          <div style="color:rgba(255,255,255,0.4);font-size:1.1rem;font-weight:bold;">0 cm</div>
          <div style="color:rgba(255,255,255,0.3);font-size:.7rem;margin-top:.2rem;">No active equipment</div>
        <?php else: ?>
          <div style="color:#34d399;font-size:1.45rem;font-weight:bold;">+<?php echo $trail_proj; ?> <span style="font-size:.75rem;opacity:.65;">cm</span></div>
          <div style="color:rgba(255,255,255,0.4);font-size:.68rem;margin-top:.25rem;"><?php echo number_format($trail_cost,0,',',' '); ?> €/night</div>
        <?php endif; ?>
      </div>

      <!-- Water Reservoir -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">💧 Water Reservoir</div>
        <?php if (!$has_reservoir): ?>
          <div style="color:#fbbf24;font-size:1rem;font-weight:bold;">Not purchased</div>
          <div style="color:rgba(245,158,11,0.7);font-size:.7rem;margin-top:.2rem;">See requirements below</div>
        <?php else: ?>
          <div style="color:white;font-size:1.45rem;font-weight:bold;"><?php echo $water_level; ?><span style="font-size:.75rem;opacity:.65;">%</span></div>
          <div style="background:rgba(255,255,255,0.12);border-radius:4px;height:4px;margin-top:.45rem;">
            <div style="background:<?php echo $water_bar_col; ?>;height:4px;border-radius:4px;width:<?php echo $water_level; ?>%;"></div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Trail Equipment -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">🔧 Trail Equipment</div>
        <div style="color:white;font-size:1.45rem;font-weight:bold;"><?php echo $trail_active; ?> <span style="font-size:.75rem;opacity:.65;">active</span></div>
        <div style="color:rgba(255,255,255,0.4);font-size:.68rem;margin-top:.25rem;"><?php echo $trail_count; ?> installed total</div>
      </div>

    </div>
  </div>
</div>

<!-- ===== TEMPERATURE WARNING ===== -->
<?php if ($is_above_frzg): ?>
<div style="background:rgba(127,29,29,0.5);border:1px solid #dc2626;border-radius:10px;padding:1rem 1.5rem;margin-bottom:1.5rem;color:white;display:flex;align-items:center;gap:.75rem;">
  <span style="font-size:1.5rem;">🌡️</span>
  <div>
    <strong>Above Freezing — Snowmaking Paused</strong><br>
    <span style="opacity:.8;font-size:.88rem;"><?php echo $this->lang->line('building')['trail_sm_temp_warning']; ?></span>
  </div>
</div>
<?php endif; ?>

<!-- ===== OPERATIONS GRID ===== -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">

  <!-- LEFT: Mode + Schedule + Target -->
  <div class="card bg-[#111827] border border-[#1e3a5f] shadow-lg">
    <div class="card-body">

      <!-- Mode -->
      <h5 class="text-white font-bold mb-3">⚙️ <?php echo $this->lang->line('building')['snowmaking_mode_title']; ?></h5>
      <form method="post" action="<?php echo base_url().'trail_snowmaking_controller/set_mode/'.$currentResortID; ?>" class="mb-4">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-bottom:.6rem;">
          <?php
          $mode_defs = [
            'normal' => ['out'=>'100%','cost'=>'100%'],
            'eco'    => ['out'=>round(SNOWMAKING_MODE_ECO_OUTPUT*100).'%',   'cost'=>round(SNOWMAKING_MODE_ECO_COST*100).'%'],
            'boost'  => ['out'=>round(SNOWMAKING_MODE_BOOST_OUTPUT*100).'%', 'cost'=>round(SNOWMAKING_MODE_BOOST_COST*100).'%'],
          ];
          foreach ($mode_defs as $mk => $mv):
            $active = ($sm_mode === $mk);
            $mc     = $mode_colors[$mk];
          ?>
          <label style="cursor:pointer;display:block;">
            <input type="radio" name="snowmaking_mode" value="<?php echo $mk; ?>" <?php echo $active?'checked':''; ?> style="display:none;" onchange="this.form.submit()">
            <div style="border-radius:8px;padding:.65rem .4rem;text-align:center;border:2px solid <?php echo $active?$mc:'rgba(255,255,255,0.1)'; ?>;background:<?php echo $active?'rgba(255,255,255,0.06)':'rgba(255,255,255,0.02)'; ?>;transition:all .15s;">
              <div style="font-size:1.2rem;"><?php echo $mode_icons[$mk]; ?></div>
              <div style="color:white;font-weight:bold;font-size:.8rem;margin:.15rem 0;"><?php echo $mode_texts[$mk]; ?></div>
              <div style="color:rgba(255,255,255,0.45);font-size:.65rem;">Out: <?php echo $mv['out']; ?><br>Cost: <?php echo $mv['cost']; ?></div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary btn-sm w-full"><?php echo $this->lang->line('building')['save_snowmaking_mode']; ?></button>
      </form>

      <div class="divider my-1" style="opacity:.3;"></div>

      <!-- Schedule -->
      <h5 class="text-white font-bold mb-2 mt-1">📅 <?php echo $this->lang->line('building')['snowmaking_schedule_title']; ?></h5>
      <?php $sched_days = $this->lang->line('building')['snowmaking_schedule_days']; ?>
      <form method="post" action="<?php echo base_url().'trail_snowmaking_controller/set_schedule/'.$currentResortID; ?>">
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:.3rem;margin-bottom:.6rem;">
          <?php for ($i = 0; $i < 7; $i++):
            $chk  = (bool)($sched_mask & (1 << $i));
            $abbr = mb_substr($sched_days[$i], 0, 2);
          ?>
          <label style="cursor:pointer;display:block;text-align:center;">
            <input type="checkbox" name="days[]" value="<?php echo $i; ?>" <?php echo $chk?'checked':''; ?> class="sched-cb" data-day="<?php echo $i; ?>" style="display:none;">
            <div class="sched-lbl" data-day="<?php echo $i; ?>"
              onclick="toggleSchedDay(this,<?php echo $i; ?>)"
              style="border-radius:6px;padding:.38rem .15rem;font-size:.72rem;font-weight:bold;border:1.5px solid <?php echo $chk?'#3b82f6':'rgba(255,255,255,0.15)'; ?>;background:<?php echo $chk?'rgba(59,130,246,0.2)':'rgba(255,255,255,0.02)'; ?>;color:<?php echo $chk?'#93c5fd':'rgba(255,255,255,0.4)'; ?>;cursor:pointer;transition:all .15s;user-select:none;">
              <?php echo htmlspecialchars($abbr); ?>
            </div>
          </label>
          <?php endfor; ?>
        </div>
        <button type="submit" class="btn btn-primary btn-sm w-full"><?php echo $this->lang->line('building')['save_snowmaking_schedule']; ?></button>
      </form>

      <div class="divider my-1" style="opacity:.3;"></div>

      <!-- Snow Target -->
      <h5 class="text-white font-bold mb-2 mt-1">🎯 <?php echo $this->lang->line('building')['snow_target_label']; ?></h5>
      <form method="post" action="<?php echo base_url().'trail_snowmaking_controller/set_snow_target/'.$currentResortID; ?>">
        <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
          <input type="number" name="target_snow" class="input input-sm bg-[#1a2640] text-white" style="width:90px;" min="0" max="<?php echo MAX_SNOW_LEVEL; ?>" value="<?php echo $snow_target; ?>">
          <span style="color:rgba(255,255,255,0.45);font-size:.82rem;">cm (0 = <?php echo $this->lang->line('building')['snow_target_disabled']; ?>)</span>
          <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('building')['save_snow_target']; ?></button>
        </div>
        <div style="color:rgba(255,255,255,0.35);font-size:.7rem;margin-top:.35rem;"><?php echo $this->lang->line('building')['snow_target_info']; ?></div>
      </form>
    </div>
  </div>

  <!-- RIGHT: Water + Staff + Municipal -->
  <div class="card bg-[#111827] border border-[#1e3a5f] shadow-lg">
    <div class="card-body">

      <h5 class="text-white font-bold mb-3">💧 <?php echo $this->lang->line('building')['snowmaking_requirements_title']; ?></h5>

      <?php if (!$has_reservoir): ?>
      <!-- Buy reservoir -->
      <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.3);border-radius:8px;padding:1rem;margin-bottom:1rem;">
        <div style="color:#fbbf24;font-weight:bold;margin-bottom:.4rem;">⚠️ <?php echo $this->lang->line('building')['water_reservoir_buy_title']; ?></div>
        <p style="color:rgba(255,255,255,0.65);font-size:.82rem;margin-bottom:.7rem;"><?php echo $this->lang->line('building')['water_reservoir_buy_desc']; ?></p>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
          <span style="color:white;font-weight:bold;"><?php echo number_format(WATER_RESERVOIR_COST,0,',',' '); ?> €</span>
          <a href="<?php echo base_url().'trail_snowmaking_controller/buy_water_reservoir/'.$currentResortID; ?>"
             onclick="return confirm('<?php echo addslashes($this->lang->line('building')['water_reservoir_buy_confirm']); ?>')">
            <button class="btn btn-warning btn-sm"><?php echo $this->lang->line('building')['water_reservoir_buy_btn']; ?></button>
          </a>
        </div>
      </div>
      <?php else: ?>
      <!-- Water level bar -->
      <div style="margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
          <span style="color:rgba(255,255,255,0.65);font-size:.82rem;"><?php echo $this->lang->line('building')['snowmaking_water_label']; ?></span>
          <span style="color:white;font-weight:bold;"><?php echo $water_level; ?>%</span>
        </div>
        <div style="background:rgba(255,255,255,0.1);border-radius:6px;height:12px;">
          <div style="background:<?php echo $water_bar_col; ?>;height:12px;border-radius:6px;width:<?php echo $water_level; ?>%;transition:width .3s;"></div>
        </div>
        <div style="color:rgba(255,255,255,0.35);font-size:.72rem;margin-top:.3rem;"><?php echo $this->lang->line('building')['snowmaking_water_refill_info']; ?></div>
        <?php if ($water_level <= 0): ?>
          <div style="color:#f87171;font-size:.78rem;margin-top:.3rem;">🚫 <?php echo strip_tags($this->lang->line('building')['snowmaking_water_empty']); ?></div>
        <?php elseif ($water_level < 20): ?>
          <div style="color:#fbbf24;font-size:.78rem;margin-top:.3rem;">⚠️ <?php echo strip_tags($this->lang->line('building')['snowmaking_water_low']); ?></div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Staff -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:.7rem .9rem;background:rgba(255,255,255,0.04);border-radius:8px;border:1px solid rgba(255,255,255,0.07);margin-bottom:.75rem;">
        <div>
          <div style="color:rgba(255,255,255,0.55);font-size:.72rem;"><?php echo $this->lang->line('building')['snowmaking_staff_label']; ?></div>
          <div style="color:white;font-weight:bold;font-size:.9rem;"><?php echo $sm_staff; ?> / <?php echo $sm_req; ?> required</div>
        </div>
        <?php if ($sm_staff >= $sm_req): ?>
        <span class="badge badge-success badge-sm">✓ OK</span>
        <?php else: ?>
        <a href="<?php echo base_url().'hire_staff_controller'; ?>" class="btn btn-xs btn-warning">Hire →</a>
        <?php endif; ?>
      </div>

      <!-- Municipal Refill -->
      <?php
      $resort_lvl      = (int)($resort_level ?? 1);
      $muni_unlocked   = !empty($municipal_refill_unlocked);
      $muni_available  = !empty($municipal_refill_available);
      $muni_cost_fmt   = number_format($municipal_refill_cost   ?? MUNICIPAL_WATER_REFILL_COST,   0, ',', ' ');
      $muni_amount     = (int)($municipal_refill_amount         ?? MUNICIPAL_WATER_REFILL_AMOUNT);
      $muni_threshold  = (int)($municipal_refill_max_reservoir  ?? MUNICIPAL_WATER_MAX_RESERVOIR_PCT);
      ?>
      <div style="padding:.7rem .9rem;background:rgba(255,255,255,0.02);border-radius:8px;border:1px solid rgba(255,255,255,0.07);">
        <div style="color:rgba(255,255,255,0.5);font-size:.7rem;margin-bottom:.4rem;"><?php echo $this->lang->line('building')['municipal_refill_title']; ?></div>
        <?php if (!$muni_unlocked): ?>
          <div style="color:rgba(255,255,255,0.35);font-size:.8rem;">🔒 <?php echo sprintf($this->lang->line('building')['municipal_refill_locked_msg'], MUNICIPAL_WATER_UNLOCK_LIFTS, $resort_lvl); ?></div>
        <?php elseif (!$has_reservoir): ?>
          <div style="color:rgba(255,255,255,0.35);font-size:.8rem;">Requires water reservoir.</div>
        <?php elseif (!$muni_available): ?>
          <div style="color:rgba(255,255,255,0.35);font-size:.8rem;">ℹ️ <?php echo sprintf($this->lang->line('building')['municipal_refill_not_needed_msg'], $muni_threshold); ?></div>
        <?php else: ?>
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
            <div style="font-size:.8rem;color:#fbbf24;">⚠️ +<?php echo $muni_amount; ?>% · <?php echo $muni_cost_fmt; ?> €<br><span style="color:rgba(255,200,50,0.6);font-size:.68rem;">Eco &amp; reputation penalty</span></div>
            <a href="<?php echo base_url().'trail_snowmaking_controller/municipal_refill/'.$currentResortID; ?>"
               onclick="return confirm('<?php echo htmlspecialchars(sprintf($this->lang->line('building')['municipal_refill_confirm'],$muni_cost_fmt),ENT_QUOTES,'UTF-8'); ?>')">
              <button class="btn btn-xs btn-error"><?php echo $this->lang->line('building')['municipal_refill_btn']; ?></button>
            </a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<!-- ===== TRAIL EQUIPMENT TABLE ===== -->
<div class="card bg-[#111827] border border-[#1e3a5f] shadow-lg" style="margin-bottom:1.5rem;">
  <div class="card-body">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
      <h4 class="h4 text-white" style="margin:0;">🔧 <?php echo $this->lang->line('building')['trail_sm_trails_title']; ?></h4>
      <div style="display:flex;gap:.5rem;">
        <button class="btn btn-sm btn-success btn-outline" onclick="toggleAll(1)">▶ <?php echo $this->lang->line('building')['trail_sm_start_all']; ?></button>
        <button class="btn btn-sm btn-ghost btn-outline"   onclick="toggleAll(0)">⏹ <?php echo $this->lang->line('building')['trail_sm_stop_all']; ?></button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="table table-sm w-full">
        <thead>
          <tr style="color:rgba(255,255,255,0.45);font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid rgba(255,255,255,0.08);">
            <th style="padding:.6rem;"><?php echo $this->lang->line('building')['trail_sm_col_trail']; ?></th>
            <th style="padding:.6rem;min-width:130px;"><?php echo $this->lang->line('building')['trail_sm_snow_level_title']; ?></th>
            <th style="padding:.6rem;"><?php echo $this->lang->line('building')['trail_sm_col_equip']; ?></th>
            <th style="padding:.6rem;"><?php echo $this->lang->line('building')['trail_sm_col_output']; ?></th>
            <th style="padding:.6rem;"><?php echo $this->lang->line('building')['trail_sm_col_status']; ?></th>
            <th style="padding:.6rem;"><?php echo $this->lang->line('building')['trail_sm_col_actions']; ?></th>
          </tr>
        </thead>
        <tbody id="trail_sm_tbody">
          <tr><td colspan="6" style="text-align:center;padding:2.5rem;color:rgba(255,255,255,0.4);">
            <span class="loading loading-spinner loading-md"></span><br>Loading trails...
          </td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ===== PURCHASE MODAL ===== -->
<dialog id="purchase_modal" class="modal">
  <div class="modal-box" style="max-width:640px;background:#111827;border:1px solid #1e3a5f;padding:1.5rem;">
    <h3 style="color:white;font-size:1.15rem;font-weight:bold;margin-bottom:.25rem;">🔧 <?php echo $this->lang->line('building')['trail_sm_buy']; ?></h3>
    <p style="color:rgba(255,255,255,0.55);font-size:.85rem;margin-bottom:1.1rem;">Trail: <span id="modal_slope_name" style="color:white;font-weight:bold;"></span></p>
    <div id="equipment_options" style="display:flex;flex-direction:column;gap:.65rem;"></div>
    <div class="modal-action" style="margin-top:1.25rem;">
      <form method="dialog"><button class="btn btn-sm btn-ghost">Cancel</button></form>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<!-- ===== UPGRADE MODAL ===== -->
<dialog id="upgrade_modal" class="modal">
  <div class="modal-box" style="max-width:640px;background:#111827;border:1px solid #1e3a5f;padding:1.5rem;">
    <h3 style="color:white;font-size:1.15rem;font-weight:bold;margin-bottom:.25rem;">⬆️ <?php echo $this->lang->line('building')['trail_sm_upgrade_label']; ?></h3>
    <p style="color:rgba(255,255,255,0.55);font-size:.85rem;margin-bottom:.2rem;">Trail: <span id="upgrade_slope_name" style="color:white;font-weight:bold;"></span></p>
    <p style="color:rgba(255,255,255,0.55);font-size:.85rem;margin-bottom:1.1rem;">Current: <span id="upgrade_current_name" style="color:#93c5fd;font-weight:bold;"></span></p>
    <div id="upgrade_options" style="display:flex;flex-direction:column;gap:.65rem;"></div>
    <div class="modal-action" style="margin-top:1.25rem;">
      <form method="dialog"><button class="btn btn-sm btn-ghost">Cancel</button></form>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<script>
const BASE_URL   = '<?php echo base_url(); ?>';
const EQ_TYPES   = <?php echo json_encode(SNOWMAKING_EQUIPMENT); ?>;
const MAX_SNOW   = <?php echo MAX_SNOW_LEVEL; ?>;
const EQ_ICONS   = { lance_gun: '🔫', fan_gun: '💨', snow_factory: '🏭' };

document.addEventListener('DOMContentLoaded', loadTrailData);

// ── Load & render ─────────────────────────────────────────────────────────
function loadTrailData() {
  fetch(BASE_URL + 'trail_snowmaking_controller/get_trail_data_ajax', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(d => { if (d.success) renderTable(d.slopes); else setMsg('Failed to load: ' + (d.message||'')); })
  .catch(() => setMsg('Network error.'));
}

function setMsg(msg) {
  document.getElementById('trail_sm_tbody').innerHTML = `<tr><td colspan="6" style="text-align:center;padding:2.5rem;color:rgba(255,255,255,0.4);">${msg}</td></tr>`;
}

function renderTable(slopes) {
  const tbody = document.getElementById('trail_sm_tbody');
  if (!slopes.length) { setMsg('<?php echo addslashes($this->lang->line('building')['trail_sm_no_slopes']); ?>'); return; }
  tbody.innerHTML = slopes.map(buildRow).join('');
}

function buildRow(s) {
  const pct = Math.min(100, Math.round((s.snow_level / MAX_SNOW) * 100));
  const bc  = pct >= 60 ? '#10b981' : (pct >= 30 ? '#f59e0b' : '#ef4444');
  const snowBar = `
    <div style="background:rgba(255,255,255,0.1);border-radius:6px;height:8px;min-width:100px;">
      <div style="background:${bc};height:8px;border-radius:6px;width:${pct}%;transition:width .3s;"></div>
    </div>
    <div style="color:rgba(255,255,255,0.4);font-size:.68rem;margin-top:2px;">${s.snow_level} cm · ${pct}%</div>
  `;

  let eqCell = `<span style="color:rgba(255,255,255,0.25);font-style:italic;font-size:.82rem;"><?php echo $this->lang->line('building')['trail_sm_none']; ?></span>`;
  let outCell = `<span style="color:rgba(255,255,255,0.25);">—</span>`;
  let stCell  = `<span class="badge badge-ghost badge-sm">—</span>`;
  let actCell = `<button class="btn btn-primary btn-xs" onclick="openPurchaseModal(${s.id_slope},'${esc(s.name)}')">+ <?php echo $this->lang->line('building')['trail_sm_buy']; ?></button>`;

  if (s.equipment) {
    const eq  = s.equipment;
    const ico = EQ_ICONS[eq.type] || '❄️';
    const on  = eq.is_active;
    eqCell = `<div style="display:flex;align-items:center;gap:.5rem;">
      <span style="font-size:1.25rem;">${ico}</span>
      <div>
        <div style="color:white;font-weight:bold;font-size:.82rem;">${esc(eq.name)}</div>
        <div style="color:rgba(255,255,255,0.35);font-size:.68rem;">${eq.daily_cost} €/day</div>
      </div>
    </div>`;
    outCell = `<span style="color:${on?'#34d399':'rgba(255,255,255,0.25)'};font-weight:bold;">${on?'+'+eq.snow_output:'—'} cm</span>`;
    stCell  = on ? `<span class="badge badge-success badge-sm"><?php echo $this->lang->line('building')['trail_sm_active']; ?></span>`
                 : `<span class="badge badge-neutral badge-sm"><?php echo $this->lang->line('building')['trail_sm_inactive']; ?></span>`;
    actCell = `<div style="display:flex;gap:.3rem;flex-wrap:wrap;">
      ${on
        ? `<button class="btn btn-ghost btn-xs" onclick="toggleEq(${eq.id},0)">⏹ <?php echo $this->lang->line('building')['trail_sm_stop']; ?></button>`
        : `<button class="btn btn-success btn-xs" onclick="toggleEq(${eq.id},1)">▶ <?php echo $this->lang->line('building')['trail_sm_start']; ?></button>`
      }
      <button class="btn btn-info btn-xs" onclick="openUpgradeModal(${eq.id},'${esc(s.name)}','${eq.type}','${esc(eq.name)}')">⬆ <?php echo $this->lang->line('building')['trail_sm_upgrade']; ?></button>
      <button class="btn btn-error btn-xs" onclick="removeEq(${eq.id})">✕</button>
    </div>`;
  }

  return `<tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
    <td style="color:white;font-weight:500;padding:.6rem;">${esc(s.name)}</td>
    <td style="padding:.6rem;">${snowBar}</td>
    <td style="padding:.6rem;">${eqCell}</td>
    <td style="padding:.6rem;">${outCell}</td>
    <td style="padding:.6rem;">${stCell}</td>
    <td style="padding:.6rem;">${actCell}</td>
  </tr>`;
}

// ── Purchase modal ────────────────────────────────────────────────────────
let _slopeId = null;
function openPurchaseModal(sid, sname) {
  _slopeId = sid;
  document.getElementById('modal_slope_name').textContent = sname;
  document.getElementById('equipment_options').innerHTML = Object.entries(EQ_TYPES).map(([k,t]) => `
    <div onclick="purchaseEq('${k}')" style="cursor:pointer;border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);display:flex;justify-content:space-between;align-items:center;gap:1rem;transition:background .15s;" onmouseover="this.style.background='rgba(59,130,246,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
      <div style="flex:1;">
        <div style="color:white;font-weight:bold;">${EQ_ICONS[k]||'❄️'} ${t.name}</div>
        <div style="color:rgba(255,255,255,0.55);font-size:.8rem;margin:.15rem 0;">${t.description}</div>
        <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.35rem;">
          <span style="background:rgba(59,130,246,0.2);color:#93c5fd;border-radius:4px;padding:.05rem .35rem;font-size:.68rem;">Output: ${t.snow_output} cm/night</span>
          <span style="background:rgba(16,185,129,0.2);color:#6ee7b7;border-radius:4px;padding:.05rem .35rem;font-size:.68rem;">Min temp: ${t.min_temp}°C</span>
          <span style="background:rgba(245,158,11,0.2);color:#fcd34d;border-radius:4px;padding:.05rem .35rem;font-size:.68rem;">${t.daily_cost} €/day</span>
        </div>
      </div>
      <div style="text-align:right;flex-shrink:0;">
        <div style="color:white;font-weight:bold;font-size:1.1rem;">${t.cost.toLocaleString()} €</div>
        <div style="color:rgba(255,255,255,0.35);font-size:.7rem;">one-time</div>
      </div>
    </div>
  `).join('');
  document.getElementById('purchase_modal').showModal();
}

function purchaseEq(type) {
  if (!confirm(`Install ${EQ_TYPES[type].name} for ${EQ_TYPES[type].cost.toLocaleString()} €?`)) return;
  document.getElementById('purchase_modal').close();
  const fd = new FormData(); fd.append('id_slope', _slopeId); fd.append('equipment_type', type);
  ajax('purchase_equipment_ajax', fd, loadTrailData);
}

// ── Upgrade modal ─────────────────────────────────────────────────────────
let _upgradeId = null, _upgradeOldType = null;
function openUpgradeModal(eqId, sname, curType, curName) {
  _upgradeId = eqId; _upgradeOldType = curType;
  document.getElementById('upgrade_slope_name').textContent = sname;
  document.getElementById('upgrade_current_name').textContent = curName;
  const oldCost = EQ_TYPES[curType]?.cost || 0;
  document.getElementById('upgrade_options').innerHTML = Object.entries(EQ_TYPES)
    .filter(([k]) => k !== curType)
    .map(([k,t]) => {
      const diff = t.cost - oldCost;
      const diffTxt = diff > 0 ? `+${diff.toLocaleString()} €` : (diff < 0 ? `${diff.toLocaleString()} € back` : 'Free');
      const diffClr = diff > 0 ? '#fcd34d' : (diff < 0 ? '#6ee7b7' : '#94a3b8');
      return `<div onclick="upgradeEq('${k}')" style="cursor:pointer;border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);display:flex;justify-content:space-between;align-items:center;gap:1rem;transition:background .15s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
        <div style="flex:1;">
          <div style="color:white;font-weight:bold;">${EQ_ICONS[k]||'❄️'} ${t.name}</div>
          <div style="color:rgba(255,255,255,0.55);font-size:.8rem;margin:.15rem 0;">${t.description}</div>
          <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.35rem;">
            <span style="background:rgba(59,130,246,0.2);color:#93c5fd;border-radius:4px;padding:.05rem .35rem;font-size:.68rem;">Output: ${t.snow_output} cm/night</span>
            <span style="background:rgba(245,158,11,0.2);color:#fcd34d;border-radius:4px;padding:.05rem .35rem;font-size:.68rem;">${t.daily_cost} €/day</span>
          </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
          <div style="color:${diffClr};font-weight:bold;font-size:1rem;">${diffTxt}</div>
        </div>
      </div>`;
    }).join('');
  document.getElementById('upgrade_modal').showModal();
}

function upgradeEq(type) {
  const diff = Math.max(0, (EQ_TYPES[type]?.cost||0) - (EQ_TYPES[_upgradeOldType]?.cost||0));
  const msg  = diff > 0 ? `Upgrade to ${EQ_TYPES[type].name} for ${diff.toLocaleString()} €?` : `Switch to ${EQ_TYPES[type].name}?`;
  if (!confirm(msg)) return;
  document.getElementById('upgrade_modal').close();
  const fd = new FormData(); fd.append('id_trail_snowmaking', _upgradeId); fd.append('equipment_type', type);
  ajax('upgrade_equipment_ajax', fd, loadTrailData);
}

// ── Toggle / Remove ───────────────────────────────────────────────────────
function toggleEq(id, state) {
  const fd = new FormData(); fd.append('id_trail_snowmaking', id); fd.append('is_active', state);
  ajax('toggle_equipment_ajax', fd, loadTrailData);
}

function toggleAll(state) {
  const fd = new FormData(); fd.append('is_active', state);
  ajax('toggle_all_equipment_ajax', fd, loadTrailData);
}

function removeEq(id) {
  if (!confirm('<?php echo addslashes($this->lang->line('building')['trail_sm_confirm_remove']); ?>')) return;
  const fd = new FormData(); fd.append('id_trail_snowmaking', id);
  ajax('remove_equipment_ajax', fd, loadTrailData);
}

// ── Shared helpers ────────────────────────────────────────────────────────
function ajax(endpoint, formData, onOk) {
  fetch(BASE_URL + 'trail_snowmaking_controller/' + endpoint, {
    method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData
  })
  .then(r => r.json())
  .then(d => { d.success ? onOk && onOk(d) : alert('Error: ' + (d.message||'unknown')); })
  .catch(() => alert('Network error.'));
}

function toggleSchedDay(el, day) {
  const cb = document.querySelector(`.sched-cb[data-day="${day}"]`);
  cb.checked = !cb.checked;
  const on = cb.checked;
  el.style.border = on ? '1.5px solid #3b82f6' : '1.5px solid rgba(255,255,255,0.15)';
  el.style.background = on ? 'rgba(59,130,246,0.2)' : 'rgba(255,255,255,0.02)';
  el.style.color = on ? '#93c5fd' : 'rgba(255,255,255,0.4)';
}

function esc(s) {
  return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}
</script>

</div>
