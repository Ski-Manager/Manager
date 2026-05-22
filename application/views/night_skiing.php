<div class="w-full">
<?php
// ── Flash messages ──────────────────────────────────────────────────────
if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = ['bad_action','night_skiing_enabled','night_skiing_disabled',
                 'night_skiing_settings_saved','night_skiing_settings_invalid','night_skiing_trail_saved'];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}

// ── Precompute display helpers ──────────────────────────────────────────
$current_ent        = $night_skiing_entertainment ?? 'none';
$current_ent_pct    = round(($entertainment_revenue[$current_ent] - 1) * 100);
$current_ent_cost   = $entertainment_costs[$current_ent];
$current_safety_lvl = (int)($night_skiing_safety_level ?? 1);
$current_safety_cost= $safety_costs[$current_safety_lvl];
$current_safety_rep = $safety_reputation_bonus[$current_safety_lvl];
$ns_status          = isset($night_skiing_status) ? (int)$night_skiing_status : 0;
$ns_trail_count_lit = (int)($ns_trail_count ?? 0);
$ns_tonight_count   = !empty($ns_tonight_events) ? count($ns_tonight_events) : 0;
?>

<!-- ===== DARK SKY BANNER ===== -->
<div style="background:linear-gradient(135deg,#04091f 0%,#0a0e27 50%,#0f1a3c 100%);border-radius:12px;padding:2.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.06);">
  <!-- Starfield + crescent moon SVG -->
  <svg style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0.7;" viewBox="0 0 1000 260" preserveAspectRatio="xMidYMid slice">
    <circle cx="55"  cy="20"  r="1.2" fill="white" opacity=".9"/>
    <circle cx="130" cy="55"  r="0.8" fill="white" opacity=".6"/>
    <circle cx="200" cy="18"  r="1.4" fill="white" opacity=".8"/>
    <circle cx="275" cy="65"  r="0.7" fill="white" opacity=".5"/>
    <circle cx="350" cy="30"  r="1.1" fill="white" opacity=".9"/>
    <circle cx="430" cy="85"  r="0.9" fill="white" opacity=".6"/>
    <circle cx="500" cy="22"  r="1.3" fill="white" opacity=".8"/>
    <circle cx="575" cy="60"  r="0.8" fill="white" opacity=".7"/>
    <circle cx="640" cy="35"  r="1.5" fill="white" opacity=".9"/>
    <circle cx="710" cy="80"  r="0.7" fill="white" opacity=".6"/>
    <circle cx="780" cy="28"  r="1.1" fill="white" opacity=".8"/>
    <circle cx="850" cy="65"  r="0.9" fill="white" opacity=".6"/>
    <circle cx="920" cy="22"  r="1.3" fill="white" opacity=".9"/>
    <circle cx="90"  cy="140" r="0.9" fill="white" opacity=".6"/>
    <circle cx="210" cy="155" r="0.7" fill="white" opacity=".5"/>
    <circle cx="370" cy="145" r="1.1" fill="white" opacity=".7"/>
    <circle cx="490" cy="165" r="0.8" fill="white" opacity=".6"/>
    <circle cx="620" cy="148" r="1.0" fill="white" opacity=".7"/>
    <circle cx="740" cy="160" r="0.7" fill="white" opacity=".5"/>
    <circle cx="870" cy="150" r="1.2" fill="white" opacity=".8"/>
    <!-- Crescent moon top-right -->
    <path d="M940 25 Q965 55 940 85 Q955 55 940 25" fill="white" opacity=".75"/>
    <!-- Mountain silhouette -->
    <polygon points="0,260 100,185 200,220 330,120 460,175 580,100 700,155 830,80 940,125 1000,95 1000,260" fill="rgba(30,58,95,0.3)"/>
  </svg>

  <div style="position:relative;z-index:1;">
    <!-- Title row -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
      <div>
        <h3 style="color:white;font-size:1.75rem;font-weight:bold;text-shadow:0 0 20px rgba(147,197,253,0.5);margin:0;">
          🎿 <?php echo $this->lang->line('building')['night_skiing_title']; ?>
        </h3>
        <p style="color:rgba(255,255,255,0.65);margin:0.4rem 0 0;font-size:0.88rem;">
          <?php echo $this->lang->line('building')['night_skiing_dynamic_demand_label']; ?>
        </p>
      </div>
      <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <?php if ($ns_status == 1): ?>
          <span class="badge badge-success" style="font-size:1rem;padding:.5rem 1.1rem;background:#10b981;">
            🌙 <?php echo $this->lang->line('building')['night_skiing_on']; ?>
          </span>
        <?php else: ?>
          <span class="badge badge-neutral" style="font-size:1rem;padding:.5rem 1.1rem;">
            💤 <?php echo $this->lang->line('building')['night_skiing_off']; ?>
          </span>
        <?php endif; ?>
        <?php if (isset($night_skiing_button)) echo $night_skiing_button; ?>
      </div>
    </div>

    <!-- Stat cards row -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;">
      <!-- Lit Trails -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">💡 Lit Trails</div>
        <div style="color:white;font-size:1.45rem;font-weight:bold;"><?php echo $ns_trail_count_lit; ?></div>
        <div style="color:rgba(255,255,255,0.4);font-size:.68rem;margin-top:.25rem;">
          <?php echo empty($trail_settings) ? 'No slopes yet' : count($trail_settings) . ' total slopes'; ?>
        </div>
      </div>

      <!-- Tonight's Hours -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">🕐 Tonight's Hours</div>
        <div style="color:white;font-size:1.25rem;font-weight:bold;">
          <?php echo sprintf('%02d:00', $night_skiing_start_hour ?? 18); ?> – <?php echo sprintf('%02d:00', $night_skiing_end_hour ?? 22); ?>
        </div>
        <div style="color:rgba(255,255,255,0.4);font-size:.68rem;margin-top:.25rem;">
          <?php
            $dur = ($night_skiing_end_hour ?? 22) - ($night_skiing_start_hour ?? 18);
            echo $dur . ' hours session';
          ?>
        </div>
      </div>

      <!-- Tonight's Events -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">🎪 Tonight's Events</div>
        <?php if ($ns_tonight_count > 0): ?>
          <div style="color:#34d399;font-size:1.45rem;font-weight:bold;"><?php echo $ns_tonight_count; ?></div>
          <div style="color:rgba(52,211,153,0.7);font-size:.68rem;margin-top:.25rem;">
            <?php echo implode(', ', $ns_tonight_event_labels ?? []); ?>
          </div>
        <?php else: ?>
          <div style="color:rgba(255,255,255,0.4);font-size:1.2rem;font-weight:bold;">None</div>
          <div style="color:rgba(255,255,255,0.3);font-size:.68rem;margin-top:.25rem;">Regular session</div>
        <?php endif; ?>
      </div>

      <!-- Entertainment -->
      <div style="background:rgba(255,255,255,0.07);border-radius:10px;padding:1rem;border:1px solid rgba(255,255,255,0.09);">
        <div style="color:rgba(255,255,255,0.55);font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">🎭 Entertainment</div>
        <div style="color:white;font-size:1.1rem;font-weight:bold;">
          <?php echo $this->lang->line('building')['night_skiing_ent_' . $current_ent]; ?>
        </div>
        <div style="color:rgba(255,255,255,0.4);font-size:.68rem;margin-top:.25rem;">
          <?php echo $current_ent_cost > 0 ? number_format($current_ent_cost,0,',',' ') . ' €/night · +' . $current_ent_pct . '% revenue' : 'No cost'; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== TONIGHT EVENT NOTICE ===== -->
<?php if ($ns_tonight_count > 0): ?>
<div style="background:rgba(22,101,52,0.3);border:1px solid #22c55e;border-radius:10px;padding:.9rem 1.3rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;color:white;">
  <span style="font-size:1.5rem;">🎪</span>
  <div>
    <strong><?php echo $this->lang->line('building')['night_skiing_tonight_event_badge'] ?? "Tonight's event"; ?>:</strong>
    <?php
      $labels = [];
      foreach ($ns_tonight_events as $ev) {
          $labels[] = strtoupper(str_replace('_', ' ', (string)$ev->event_type));
      }
      echo htmlspecialchars(implode(' + ', $labels), ENT_QUOTES, 'UTF-8');
    ?>
    · Visitor bonus: +<?php echo (int)($ns_tonight_visitor_bonus_pct ?? 0); ?>%
    · Revenue ×<?php echo number_format($ns_tonight_revenue_multiplier ?? 1, 2); ?>
  </div>
</div>
<?php endif; ?>

<!-- ===== MAIN GRID: SETTINGS | LIVE PREVIEW ===== -->
<div style="display:grid;grid-template-columns:1fr 380px;gap:1.5rem;margin-bottom:1.5rem;" id="ns_main_grid">

  <!-- LEFT: Resort Settings -->
  <div>
    <div class="card bg-[#111827] border border-[#1e3a5f] shadow-lg">
      <div class="card-body">
        <h4 class="h4 text-white mb-4">⚙️ <?php echo $this->lang->line('building')['night_skiing_resort_settings']; ?></h4>
        <form id="ns_resort_settings_form" method="post" action="<?php echo base_url('night_skiing_controller/save_resort_settings'); ?>">

          <!-- Hours -->
          <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
            <div class="text-white text-sm font-semibold mb-2">🕐 <?php echo $this->lang->line('building')['night_skiing_hours_label'] ?? 'Operating Hours'; ?></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
              <div>
                <label for="night_start_hour" class="text-white text-xs mb-1 block"><?php echo $this->lang->line('building')['night_skiing_start_hour_label']; ?></label>
                <select name="night_start_hour" id="night_start_hour" class="select select-sm bg-[#1a2640] text-white w-full" onchange="nsRefreshAnalytics()">
                  <?php for ($h = $min_start_hour; $h <= $max_start_hour; $h++): ?>
                    <option value="<?php echo $h; ?>" <?php echo ($h == $night_skiing_start_hour) ? 'selected' : ''; ?>><?php echo sprintf('%02d:00', $h); ?></option>
                  <?php endfor; ?>
                </select>
              </div>
              <div>
                <label for="night_end_hour" class="text-white text-xs mb-1 block"><?php echo $this->lang->line('building')['night_skiing_end_hour_label']; ?></label>
                <select name="night_end_hour" id="night_end_hour" class="select select-sm bg-[#1a2640] text-white w-full" onchange="nsRefreshAnalytics()">
                  <?php for ($h = $min_end_hour; $h <= $max_end_hour; $h++): ?>
                    <option value="<?php echo $h; ?>" <?php echo ($h == $night_skiing_end_hour) ? 'selected' : ''; ?>><?php echo sprintf('%02d:00', $h); ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Ticket Price -->
          <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
            <label for="night_ticket_price" class="text-white text-sm font-semibold mb-1 block">🎫 <?php echo $this->lang->line('building')['night_skiing_ticket_price_label']; ?> (€)</label>
            <input type="number" name="night_ticket_price" id="night_ticket_price" class="input input-sm bg-[#1a2640] text-white w-full" min="0" max="500" value="<?php echo (int)$night_skiing_ticket_price; ?>" oninput="nsRefreshAnalytics()">
          </div>

          <!-- Entertainment -->
          <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
            <label for="night_entertainment" class="text-white text-sm font-semibold mb-2 block">🎭 <?php echo $this->lang->line('building')['night_skiing_entertainment_label']; ?></label>
            <select name="night_entertainment" id="night_entertainment" class="select select-sm bg-[#1a2640] text-white w-full" onchange="nsRefreshAnalytics()">
              <?php foreach ($entertainment_options as $ent): ?>
                <option value="<?php echo $ent; ?>" <?php echo ($ent === $night_skiing_entertainment) ? 'selected' : ''; ?>>
                  <?php echo $this->lang->line('building')['night_skiing_ent_' . $ent]; ?> (<?php echo $entertainment_costs[$ent]; ?> €)
                </option>
              <?php endforeach; ?>
            </select>
            <div class="text-xs text-white/50 mt-1"><?php echo $this->lang->line('building')['night_skiing_entertainment_help']; ?></div>
          </div>

          <!-- Safety Level -->
          <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
            <div class="text-white text-sm font-semibold mb-2">🦺 <?php echo $this->lang->line('building')['night_skiing_safety_label']; ?></div>
            <div style="display:flex;flex-direction:column;gap:.4rem;">
              <?php for ($lvl = $safety_min_level; $lvl <= $safety_max_level; $lvl++): ?>
                <label style="cursor:pointer;">
                  <input class="radio radio-sm" type="radio" name="night_safety_level" value="<?php echo $lvl; ?>" <?php echo ($lvl === $current_safety_lvl) ? 'checked' : ''; ?> onchange="nsRefreshAnalytics()">
                  <span class="text-white text-sm ml-2"><?php echo $this->lang->line('building')['night_skiing_safety_' . $lvl]; ?></span>
                  <span class="text-white/50 text-xs ml-1">(<?php echo $safety_costs[$lvl]; ?> € · +<?php echo $safety_reputation_bonus[$lvl]; ?> rep)</span>
                </label>
              <?php endfor; ?>
            </div>
            <div class="text-xs text-white/50 mt-2"><?php echo $this->lang->line('building')['night_skiing_safety_help']; ?></div>
          </div>

          <!-- Night Ski School -->
          <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
            <label class="flex items-center gap-2 cursor-pointer mb-2">
              <input class="toggle toggle-sm" type="checkbox" name="night_school_enabled" id="night_school_enabled" value="1" <?php echo ($night_skiing_school_enabled ? 'checked' : ''); ?> onchange="nsRefreshAnalytics()">
              <span class="text-white text-sm font-semibold">🏂 <?php echo $this->lang->line('building')['night_skiing_school_label']; ?></span>
            </label>
            <div style="margin-left:2rem;">
              <input type="number" name="night_school_price" id="night_school_price" class="input input-sm bg-[#1a2640] text-white w-full" min="0" max="<?php echo $school_max_price; ?>" value="<?php echo (int)$night_skiing_school_price; ?>" placeholder="Price (€)" oninput="nsRefreshAnalytics()">
            </div>
            <div class="text-xs text-white/50 mt-1 ml-8"><?php echo $this->lang->line('building')['night_skiing_school_help']; ?></div>
          </div>

          <!-- Photography Package -->
          <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
            <label class="flex items-center gap-2 cursor-pointer mb-2">
              <input class="toggle toggle-sm" type="checkbox" name="night_photo_enabled" id="night_photo_enabled" value="1" <?php echo ($night_skiing_photo_enabled ? 'checked' : ''); ?> onchange="nsRefreshAnalytics()">
              <span class="text-white text-sm font-semibold">📸 <?php echo $this->lang->line('building')['night_skiing_photo_label']; ?></span>
            </label>
            <div style="margin-left:2rem;">
              <input type="number" name="night_photo_price" id="night_photo_price" class="input input-sm bg-[#1a2640] text-white w-full" min="0" max="<?php echo $photo_max_price; ?>" value="<?php echo (int)$night_skiing_photo_price; ?>" placeholder="Price (€)" oninput="nsRefreshAnalytics()">
            </div>
            <div class="text-xs text-white/50 mt-1 ml-8"><?php echo $this->lang->line('building')['night_skiing_photo_help']; ?></div>
          </div>

          <!-- Torchlight Descent -->
          <div class="mb-3" style="display:flex;flex-direction:column;gap:.5rem;">
            <label class="flex items-center gap-2 cursor-pointer">
              <input class="toggle toggle-sm" type="checkbox" name="night_torchlight" id="night_torchlight" value="1" <?php echo ($night_skiing_torchlight ? 'checked' : ''); ?> onchange="nsRefreshAnalytics()">
              <span class="text-white text-sm font-semibold">🔦 <?php echo $this->lang->line('building')['night_skiing_torchlight_label']; ?></span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input class="toggle toggle-sm" type="checkbox" name="night_weather_suspend" id="night_weather_suspend" value="1" <?php echo ($night_skiing_weather_suspend ? 'checked' : ''); ?>>
              <span class="text-white text-sm font-semibold">🌩 <?php echo $this->lang->line('building')['night_skiing_weather_suspend_label']; ?></span>
            </label>
          </div>

          <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid #1e3a5f;">
            <button type="submit" class="btn btn-primary btn-sm w-full"><?php echo $this->lang->line('building')['night_skiing_save_settings']; ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- RIGHT: Live Preview + Analytics -->
  <div style="display:flex;flex-direction:column;gap:1rem;">

    <!-- Live Revenue Preview -->
    <div class="card border shadow-lg" style="background:linear-gradient(135deg,#1a2b4a 0%,#0f1a3c 100%);border-color:#2a4a7c;">
      <div class="card-body">
        <h4 class="h4 text-white mb-3">📊 <?php echo $this->lang->line('building')['night_skiing_live_preview_title']; ?></h4>
        <div style="font-size:.875rem;">
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.5rem;padding:.4rem .6rem;background:rgba(255,255,255,.04);border-radius:6px;">
            <span><?php echo $this->lang->line('building')['night_skiing_est_visitors']; ?>:</span>
            <span id="preview_visitors" class="font-bold text-white">0</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>Ticket revenue:</span><span id="preview_ticket_rev" class="font-semibold text-green-300">0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>School:</span><span id="preview_school" class="text-green-300">+0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>Photography:</span><span id="preview_photo" class="text-green-300">+0 €</span>
          </div>
          <div style="height:1px;background:#2a4a7c;margin:.5rem 0;"></div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>Electricity:</span><span id="preview_elec" class="text-red-300">-0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>Safety:</span><span id="preview_safety" class="text-red-300">-0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>Entertainment:</span><span id="preview_ent" class="text-red-300">-0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>Grooming:</span><span id="preview_grooming" class="text-red-300">-0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.75);margin-bottom:.35rem;padding:.3rem .6rem;">
            <span>Torchlight:</span><span id="preview_torch" class="text-red-300">-0 €</span>
          </div>
          <div style="height:1px;background:#2a4a7c;margin:.5rem 0;"></div>
          <div style="display:flex;justify-content:space-between;color:white;margin-bottom:.35rem;padding:.4rem .6rem;background:rgba(255,255,255,.04);border-radius:6px;">
            <span><?php echo $this->lang->line('building')['night_skiing_est_revenue']; ?>:</span>
            <span id="preview_total_rev" class="font-bold text-green-400">0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:white;margin-bottom:.35rem;padding:.4rem .6rem;background:rgba(255,255,255,.04);border-radius:6px;">
            <span><?php echo $this->lang->line('building')['night_skiing_est_costs']; ?>:</span>
            <span id="preview_total_cost" class="font-bold text-red-400">0 €</span>
          </div>
          <div style="display:flex;justify-content:space-between;color:white;font-size:1rem;font-weight:bold;padding:.5rem .6rem;border-top:1px solid #2a4a7c;margin-top:.25rem;">
            <span><?php echo $this->lang->line('building')['night_skiing_est_net']; ?>:</span>
            <span id="preview_net" class="text-green-400">0 €</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Demand Forecast -->
    <div class="card bg-[#020617] border border-[#1f2937] shadow-lg">
      <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
          <h4 class="h4 text-white text-sm mb-0"><?php echo $this->lang->line('building')['night_skiing_forecast_title'] ?? 'Demand Forecast'; ?></h4>
          <span id="ns_forecast_level_badge" class="badge badge-neutral text-xs">-</span>
        </div>
        <div style="display:flex;align-items:center;gap:1rem;">
          <div style="width:40%;"><canvas id="ns_forecast_gauge" height="110" aria-hidden="true"></canvas></div>
          <div style="flex:1;font-size:.8rem;color:rgba(255,255,255,.8);">
            <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
              <span><?php echo $this->lang->line('building')['night_skiing_est_visitors']; ?></span>
              <span id="ns_forecast_total" class="font-semibold">–</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
              <span>Demand</span>
              <span id="ns_forecast_level_text" class="font-semibold">–</span>
            </div>
            <p id="ns_forecast_explanation" class="text-white/50" style="font-size:.7rem;margin-top:.5rem;line-height:1.4;"></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Cost Breakdown -->
    <div class="card bg-[#020617] border border-[#1f2937] shadow-lg">
      <div class="card-body">
        <h4 class="h4 text-white text-sm mb-3"><?php echo $this->lang->line('building')['night_skiing_cost_breakdown_title'] ?? 'Cost Breakdown'; ?></h4>
        <div style="display:flex;align-items:center;gap:1rem;">
          <div style="width:40%;"><canvas id="ns_cost_chart" height="130" aria-hidden="true"></canvas></div>
          <div style="flex:1;font-size:.78rem;color:rgba(255,255,255,.8);">
            <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;"><span>⚡ Electricity</span><span id="ns_cost_electricity">0 €</span></div>
            <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;"><span>🦺 Safety</span><span id="ns_cost_safety">0 €</span></div>
            <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;"><span>🎿 Grooming</span><span id="ns_cost_grooming">0 €</span></div>
            <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;"><span>🎭 Entertainment</span><span id="ns_cost_entertainment">0 €</span></div>
            <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;"><span>🎪 Events</span><span id="ns_cost_events">0 €</span></div>
            <div style="display:flex;justify-content:space-between;padding-top:.3rem;border-top:1px solid #1e3a5f;font-weight:bold;">
              <span><?php echo $this->lang->line('building')['night_skiing_est_costs']; ?></span>
              <span id="ns_cost_total" class="text-red-400">0 €</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- ===== SLOPE LIGHTING GRID ===== -->
<div class="card bg-[#111827] border border-[#1e3a5f] shadow-lg mb-4">
  <div class="card-body">
    <h4 class="h4 text-white mb-1">💡 <?php echo $this->lang->line('building')['night_skiing_trail_cards_title']; ?></h4>
    <p class="text-white/60 text-sm mb-4"><?php echo $this->lang->line('building')['night_skiing_trail_cards_intro']; ?></p>

    <?php if (empty($trail_settings)): ?>
      <p class="text-white/50 text-sm"><?php echo $this->lang->line('building')['night_skiing_no_open_slopes']; ?></p>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:1rem;">
        <?php foreach ($trail_settings as $trail):
          $trail_ns_enabled   = isset($trail->night_skiing_enabled) ? (int)$trail->night_skiing_enabled : 0;
          $trail_light_type   = $trail->light_type   ?? 'led';
          $trail_brightness   = (int)($trail->brightness   ?? 3);
          $trail_pole_spacing = (int)($trail->pole_spacing ?? 25);
          $trail_display_name = !empty($trail->custom_name) ? htmlspecialchars($trail->custom_name) : ('#' . $trail->id_created_slopes);
          $trail_quality      = max(0, min(100, (int)($trail->quality ?? 100)));
          $daily_quality_loss = (int)round(NIGHT_SKIING_QUALITY_LOSS_BASE + ($trail_brightness - 1) * NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR);
          $q_color = $trail_quality >= 80 ? '#10b981' : ($trail_quality >= 60 ? '#f59e0b' : ($trail_quality >= 40 ? '#8b5cf6' : '#ef4444'));
          $light_color = ['led'=>'#3b82f6','halogen'=>'#f97316','metal_halide'=>'#fef3c7'][$trail_light_type] ?? '#3b82f6';
          $light_icon  = ['led'=>'💙','halogen'=>'🟠','metal_halide'=>'🌕'][$trail_light_type] ?? '💡';
        ?>
          <div style="background:linear-gradient(135deg,#1a2b4a 0%,#0f1a3c 100%);border:1px solid <?php echo $trail_ns_enabled?'#2563eb':'#1e3a5f'; ?>;border-radius:8px;padding:.9rem;transition:border-color .2s;">
            <!-- Name + type -->
            <div style="margin-bottom:.6rem;">
              <p style="color:white;font-weight:600;font-size:.88rem;margin:0;"><?php echo $trail_display_name; ?></p>
              <?php if (!empty($trail->slope_type_name)): ?>
                <p style="color:rgba(255,255,255,.45);font-size:.72rem;margin:.1rem 0 0;"><?php echo htmlspecialchars($trail->slope_type_name); ?></p>
              <?php endif; ?>
            </div>

            <!-- Lighting bar -->
            <div style="height:6px;border-radius:3px;margin-bottom:.6rem;background:linear-gradient(90deg,<?php echo $light_color; ?> <?php echo ($trail_brightness/5*100); ?>%,rgba(255,255,255,.08) <?php echo ($trail_brightness/5*100); ?>%)"></div>

            <!-- Status row -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;font-size:.73rem;">
              <div>
                <?php if ($trail_ns_enabled): ?>
                  <span class="badge badge-success badge-sm">ON</span>
                <?php else: ?>
                  <span class="badge badge-neutral badge-sm">OFF</span>
                <?php endif; ?>
              </div>
              <div style="color:rgba(255,255,255,.55);"><?php echo $light_icon; ?> ★<?php echo $trail_brightness; ?>/5</div>
            </div>

            <!-- Quality bar -->
            <div style="margin-bottom:.6rem;">
              <div style="display:flex;justify-content:space-between;font-size:.68rem;color:rgba(255,255,255,.5);margin-bottom:.2rem;">
                <span>Quality</span><span style="color:<?php echo $q_color; ?>;"><?php echo $trail_quality; ?>% (−<?php echo $daily_quality_loss; ?>/night)</span>
              </div>
              <div style="background:rgba(255,255,255,.1);border-radius:4px;height:5px;">
                <div style="background:<?php echo $q_color; ?>;height:5px;border-radius:4px;width:<?php echo $trail_quality; ?>%;"></div>
              </div>
            </div>

            <!-- Actions -->
            <div style="display:flex;gap:.4rem;align-items:center;">
              <input type="checkbox" class="toggle toggle-sm ns-trail-toggle"
                     data-slope-id="<?php echo $trail->id_created_slopes; ?>"
                     data-trail-id="<?php echo $trail->id_night_skiing_trail; ?>"
                     <?php echo ($trail_ns_enabled ? 'checked' : ''); ?>
                     onchange="nsRefreshAnalytics()">
              <button class="btn btn-xs btn-outline btn-primary flex-1"
                      onclick="openTrailModal(this)"
                      data-slope-id="<?php echo $trail->id_created_slopes; ?>"
                      data-slope-name="<?php echo $trail_display_name; ?>"
                      data-ns-enabled="<?php echo $trail_ns_enabled; ?>"
                      data-light-type="<?php echo $trail_light_type; ?>"
                      data-brightness="<?php echo $trail_brightness; ?>"
                      data-pole-spacing="<?php echo $trail_pole_spacing; ?>"
                      data-quality="<?php echo $trail_quality; ?>"
                      data-quality-loss="<?php echo $daily_quality_loss; ?>">
                ⚙ <?php echo $this->lang->line('building')['night_skiing_configure']; ?>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ===== EVENTS SECTION ===== -->
<div class="card bg-[#111827] border border-[#1e3a5f] shadow-lg mb-4">
  <div class="card-body">
    <h4 class="h4 text-white mb-1">🎪 <?php echo $this->lang->line('building')['night_skiing_events_title']; ?></h4>
    <p class="text-white/60 text-sm mb-4"><?php echo $this->lang->line('building')['night_skiing_events_intro']; ?></p>

    <!-- Event Type Cards (dynamically from config) -->
    <?php
    $event_icons = ['fireworks'=>'🎆','concert'=>'🎸','night_race'=>'🏁','dj_night'=>'🎶','race_night'=>'🏆','torchlight_parade'=>'🔦'];
    $ns_events_config = NIGHT_SKIING_EVENTS;
    $main_types   = ['fireworks','concert','night_race'];
    $legacy_types = array_diff(array_keys($ns_events_config), $main_types);
    $shown_types  = $main_types;
    foreach ($legacy_types as $lt) { if (isset($ns_events_config[$lt])) $shown_types[] = $lt; }
    ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:1.5rem;">
      <?php foreach ($shown_types as $ev_key):
        if (!isset($ns_events_config[$ev_key])) continue;
        $ev = $ns_events_config[$ev_key];
        $ev_icon = $event_icons[$ev_key] ?? '🎪';
        $ev_label = $this->lang->line('building')['night_skiing_event_' . $ev_key] ?? ucwords(str_replace('_',' ',$ev_key));
        $is_legacy = !in_array($ev_key, $main_types);
      ?>
        <div style="background:linear-gradient(135deg,#1a2b4a 0%,#0f1a3c 100%);border:1px solid <?php echo $is_legacy?'rgba(255,255,255,0.08)':'#1e3a5f'; ?>;border-radius:8px;padding:1rem;<?php echo $is_legacy?'opacity:.7;':'' ?>">
          <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem;">
            <span style="font-size:1.5rem;"><?php echo $ev_icon; ?></span>
            <div>
              <h5 style="color:white;font-weight:600;margin:0;font-size:.88rem;"><?php echo htmlspecialchars($ev_label); ?></h5>
              <?php if ($is_legacy): ?><span style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.5);font-size:.62rem;border-radius:3px;padding:.1rem .3rem;">Legacy</span><?php endif; ?>
            </div>
          </div>
          <div style="font-size:.75rem;color:#93c5fd;margin-bottom:.75rem;display:flex;flex-direction:column;gap:.2rem;">
            <div>💰 Cost: <strong style="color:white;"><?php echo number_format($ev['cost'],0,',',' '); ?> €</strong></div>
            <div>👥 Visitors: +<?php echo (int)$ev['visitor_bonus']; ?>%</div>
            <div>💵 Revenue: ×<?php echo number_format($ev['revenue_multiplier'],2); ?></div>
            <div>⭐ Rep: +<?php echo (int)$ev['reputation_bonus']; ?></div>
          </div>
          <button class="btn btn-sm btn-primary w-full" onclick="nsScheduleEventType('<?php echo $ev_key; ?>')">
            <?php echo $this->lang->line('building')['night_skiing_event_schedule']; ?>
          </button>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Schedule Form -->
    <div style="background:rgba(255,255,255,0.03);border:1px solid #1e3a5f;border-radius:8px;padding:1rem;margin-bottom:1.25rem;">
      <h5 style="color:white;font-weight:600;font-size:.9rem;margin-bottom:.75rem;">📅 <?php echo $this->lang->line('building')['night_skiing_schedule_event_btn']; ?></h5>
      <form id="ns_event_form" method="post" action="<?php echo base_url('night_skiing_controller/schedule_event'); ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;">
          <div>
            <label class="text-white text-xs font-semibold block mb-1"><?php echo $this->lang->line('building')['night_skiing_event_type_label']; ?></label>
            <select id="ns_event_type_select" name="event_type" class="select select-sm bg-[#1a2640] text-white w-full">
              <?php foreach ($shown_types as $ev_key):
                if (!isset($ns_events_config[$ev_key])) continue;
                $ev_label2 = $this->lang->line('building')['night_skiing_event_' . $ev_key] ?? ucwords(str_replace('_',' ',$ev_key));
              ?>
                <option value="<?php echo $ev_key; ?>"><?php echo htmlspecialchars($ev_label2); ?> (<?php echo number_format($ns_events_config[$ev_key]['cost'],0,',',' '); ?> €)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="text-white text-xs font-semibold block mb-1"><?php echo $this->lang->line('building')['night_skiing_event_date_label']; ?></label>
            <input type="date" name="scheduled_date" id="ns_event_date_input" class="input input-sm bg-[#1a2640] text-white w-full" min="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div>
            <button type="submit" class="btn btn-secondary btn-sm"><?php echo $this->lang->line('building')['night_skiing_schedule_event_btn']; ?></button>
          </div>
        </div>
      </form>
    </div>

    <!-- Upcoming Events List -->
    <div>
      <h5 style="color:white;font-weight:600;font-size:.9rem;margin-bottom:.6rem;"><?php echo $this->lang->line('building')['night_skiing_upcoming_events_title']; ?></h5>
      <div id="ns_upcoming_events_list" style="display:flex;flex-direction:column;gap:.4rem;">
        <p style="color:rgba(255,255,255,.4);font-size:.8rem;font-style:italic;">Loading events...</p>
      </div>
    </div>
  </div>
</div>

<!-- ===== REVENUE TRENDS ===== -->
<div class="card bg-[#111827] border border-[#1e3a5f] shadow-lg mb-4">
  <div class="card-body">
    <h4 class="h4 text-white mb-1">📈 <?php echo $this->lang->line('building')['night_skiing_revenue_trends_title'] ?? 'Revenue Trends'; ?></h4>
    <p class="text-white/60 text-sm mb-3"><?php echo $this->lang->line('building')['night_skiing_revenue_trends_help'] ?? 'Recent night-skiing revenue split between tickets, school lessons, photos and events.'; ?></p>
    <div style="height:220px;"><canvas id="ns_revenue_trend_chart" aria-label="Night skiing revenue trends" role="img"></canvas></div>
    <div id="ns_revenue_summary" class="mt-3 text-xs text-white/60"></div>
  </div>
</div>

<!-- ===== TRAIL CONFIGURATION MODAL ===== -->
<dialog id="trailModal" class="modal modal-middle" aria-labelledby="trailModalLabel">
  <div class="modal-box max-h-[90vh] overflow-y-auto" style="background:linear-gradient(135deg,#1a2b4a 0%,#0f1a3c 100%);border:1px solid #1e3a5f;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <h5 class="h5 font-bold text-lg text-white" id="trailModalLabel">
        <?php echo $this->lang->line('building')['night_skiing_configure_trail']; ?> <span id="trailModalName"></span>
      </h5>
      <button type="button" class="btn btn-sm btn-ghost btn-circle text-white" onclick="document.getElementById('trailModal').close()">✕</button>
    </div>

    <form method="post" action="<?php echo base_url('night_skiing_controller/save_trail_settings'); ?>" id="ns_trail_form">
      <input type="hidden" name="id_created_slope" id="modal_slope_id">

      <!-- Enable toggle -->
      <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
        <label class="flex items-center gap-2 cursor-pointer">
          <input class="toggle" type="checkbox" name="trail_night_skiing_enabled" id="ns_enabled_toggle" value="1">
          <span class="text-white font-semibold"><?php echo $this->lang->line('building')['night_skiing_trail_enabled']; ?></span>
        </label>
      </div>

      <!-- Light type -->
      <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
        <label for="modal_light_type" class="text-white font-semibold mb-2 block"><?php echo $this->lang->line('building')['night_skiing_light_type_label']; ?></label>
        <select name="trail_light_type" id="modal_light_type" class="select bg-[#1a2640] text-white w-full">
          <?php foreach ($light_type_options as $lt): ?>
            <option value="<?php echo $lt; ?>"><?php echo $this->lang->line('building')['night_skiing_light_' . $lt]; ?></option>
          <?php endforeach; ?>
        </select>
        <div class="text-sm text-white/50 mt-2"><?php echo $this->lang->line('building')['night_skiing_light_type_help']; ?></div>
      </div>

      <!-- Brightness -->
      <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
        <label class="text-white font-semibold mb-2 block">
          <?php echo $this->lang->line('building')['night_skiing_brightness_label']; ?>: <span id="brightness_display" class="text-yellow-400">3</span>/5
        </label>
        <input type="range" class="range" name="trail_brightness" id="modal_brightness" min="1" max="5" step="1" value="3"
               oninput="document.getElementById('brightness_display').textContent=this.value; updateTrailQuality(this.value);">
        <div class="text-sm text-white/50 mt-2"><?php echo $this->lang->line('building')['night_skiing_brightness_help']; ?></div>
        <div class="mt-2 text-sm text-gray-300">
          <?php echo $this->lang->line('building')['night_skiing_quality_impact']; ?>:
          <span id="quality_impact_display" class="font-bold text-red-400">-0.0</span> / day
        </div>
      </div>

      <!-- Pole spacing -->
      <div class="mb-4 pb-4" style="border-bottom:1px solid #1e3a5f;">
        <label class="text-white font-semibold mb-2 block"><?php echo $this->lang->line('building')['night_skiing_pole_spacing_label']; ?></label>
        <div style="display:flex;flex-direction:column;gap:.4rem;">
          <?php foreach ($pole_spacing_options as $ps): ?>
            <label class="flex items-center gap-2 cursor-pointer text-white text-sm">
              <input class="radio radio-sm" type="radio" name="trail_pole_spacing" id="ps_<?php echo $ps; ?>" value="<?php echo $ps; ?>">
              <?php echo $ps; ?> m – <?php echo $this->lang->line('building')['night_skiing_spacing_' . $ps]; ?>
            </label>
          <?php endforeach; ?>
        </div>
        <div class="text-sm text-white/50 mt-2"><?php echo $this->lang->line('building')['night_skiing_pole_spacing_help']; ?></div>
      </div>

      <!-- Impact Preview -->
      <div style="background:rgba(59,130,246,0.08);border:1px solid #1e3a5f;border-radius:6px;padding:1rem;margin-bottom:1rem;">
        <h6 class="text-blue-400 font-semibold text-sm mb-2">Impact Preview</h6>
        <div style="font-size:.875rem;color:#e5e7eb;display:flex;flex-direction:column;gap:.3rem;">
          <div style="display:flex;justify-content:space-between;"><span>Electricity cost:</span><span id="modal_cost_impact">+0 €</span></div>
          <div style="display:flex;justify-content:space-between;"><span>Revenue multiplier:</span><span id="modal_rev_impact">×1.0</span></div>
          <div style="display:flex;justify-content:space-between;"><span>Trail quality:</span><span id="modal_quality_loss">-0 / night</span></div>
        </div>
      </div>

      <div class="modal-action">
        <button type="button" class="btn btn-ghost text-white" onclick="document.getElementById('trailModal').close()"><?php echo $this->lang->line('building')['cancel']; ?></button>
        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('building')['night_skiing_save_settings']; ?></button>
      </div>
    </form>
  </div>
</dialog>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Page data injection ─────────────────────────────────────────────────
var NightSkiingPageData = {
  dailyVisitors: <?php echo intval($ns_daily_visitors ?? 100); ?>,
  skipassDaily:  <?php echo intval($ns_skipass_daily  ?? 100); ?>,
  skipassWeekly: <?php echo intval($ns_skipass_weekly ?? 600); ?>,
  enabledTrailCount: <?php echo intval($ns_trail_count ?? 0); ?>,
  ticketPrice:   <?php echo (int)($night_skiing_ticket_price ?? 0); ?>,
  entertainmentKey:   <?php echo json_encode($current_ent); ?>,
  entertainmentCosts: <?php echo json_encode($entertainment_costs, JSON_FORCE_OBJECT); ?>,
  safetyLevel:    <?php echo (int)$current_safety_lvl; ?>,
  safetyCosts:    <?php echo json_encode($safety_costs, JSON_FORCE_OBJECT); ?>,
  electricityBase: <?php echo (int)NIGHT_SKIING_ELECTRICITY_COST; ?>,
  electricityPerSlope: <?php echo (int)NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE; ?>,
  groomingPerTrail: <?php echo (int)NIGHT_SKIING_GROOMING_SURCHARGE_PER_TRAIL; ?>,
  torchlightEnabled: <?php echo !empty($night_skiing_torchlight) ? 'true' : 'false'; ?>,
  torchlightCost: <?php echo (int)NIGHT_SKIING_TORCHLIGHT_COST; ?>,
  torchlightVisitorBonus: <?php echo (float)NIGHT_SKIING_TORCHLIGHT_VISITOR_BONUS; ?>,
  schoolEnabled:  <?php echo !empty($night_skiing_school_enabled) ? 'true' : 'false'; ?>,
  schoolPrice:    <?php echo (int)($night_skiing_school_price ?? 0); ?>,
  photoEnabled:   <?php echo !empty($night_skiing_photo_enabled) ? 'true' : 'false'; ?>,
  photoPrice:     <?php echo (int)($night_skiing_photo_price ?? 0); ?>
};
var NightSkiingQualityConfig = {
  baseLoss: <?php echo (float)NIGHT_SKIING_QUALITY_LOSS_BASE; ?>,
  brightnessFactor: <?php echo (float)NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR; ?>
};
var NightSkiingEventsConfig  = <?php echo json_encode(NIGHT_SKIING_EVENTS); ?>;
var NightSkiingDemandConfig  = {
  visitorFraction: <?php echo (float)NIGHT_SKIING_VISITOR_FRACTION; ?>,
  dowFactorToday:  <?php $dowToday = (int)date('N'); echo (float)(NIGHT_SKIING_DOW_FACTOR[$dowToday] ?? 1.0); ?>,
  schoolVisitorFraction: <?php echo (float)NIGHT_SKIING_SCHOOL_VISITOR_FRACTION; ?>,
  photoVisitorFraction:  <?php echo (float)NIGHT_SKIING_PHOTO_VISITOR_FRACTION; ?>
};
var NightSkiingTonight = {
  date: <?php echo json_encode($ns_today_date ?? date('Y-m-d')); ?>,
  totalVisitorBonusPct: <?php echo isset($ns_tonight_visitor_bonus_pct) ? (float)$ns_tonight_visitor_bonus_pct : 0; ?>,
  revenueMultiplier: <?php echo isset($ns_tonight_revenue_multiplier) ? (float)$ns_tonight_revenue_multiplier : 1.0; ?>,
  cost: <?php echo isset($ns_tonight_cost) ? (int)$ns_tonight_cost : 0; ?>,
  labels: <?php echo json_encode($ns_tonight_event_labels ?? []); ?>
};

var nsForecastChart = null, nsCostChart = null, nsRevenueChart = null;

function nsFormatEuro(value) {
  var n = Math.round(Number(value) || 0);
  var sign = n < 0 ? '-' : '';
  return sign + Math.abs(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '\u202f') + ' €';
}

function nsGetIntValue(id, fallback) {
  var el = document.getElementById(id);
  if (!el) return fallback;
  var v = parseFloat(el.value);
  return isNaN(v) ? fallback : v;
}

function nsGetSelectedRadioValue(name, fallback) {
  var checked = document.querySelector('input[name="' + name + '"]:checked');
  if (!checked) return fallback;
  var v = parseInt(checked.value, 10);
  return isNaN(v) ? fallback : v;
}

function nsCountEnabledTrails() {
  var toggles = document.querySelectorAll('.ns-trail-toggle');
  if (!toggles.length) return NightSkiingPageData.enabledTrailCount || 0;
  var count = 0;
  toggles.forEach(function(t) { if (t.checked) count++; });
  return count;
}

function nsComputeState() {
  var page = NightSkiingPageData || {};
  var demand = NightSkiingDemandConfig || {};
  var tonight = NightSkiingTonight || {};

  var daily = page.dailyVisitors || 100;
  var visitorFraction = demand.visitorFraction || 0.25;
  var dowFactor = demand.dowFactorToday || 1.0;
  var baseNightVisitors = daily * visitorFraction * dowFactor;

  var ticketPrice = nsGetIntValue('night_ticket_price', page.ticketPrice || page.skipassDaily || 30);
  if (ticketPrice <= 0) ticketPrice = (page.skipassDaily || 30) * 0.6;

  var referencePrice = (page.skipassDaily || 30) * 0.6;
  if (!referencePrice) referencePrice = ticketPrice || 1;
  var priceRatio = ticketPrice / referencePrice;
  var priceFactor = 1 - 0.6 * (priceRatio - 1);
  if (priceFactor < 0.3) priceFactor = 0.3;
  if (priceFactor > 1.4) priceFactor = 1.4;

  var start = nsGetIntValue('night_start_hour', 18);
  var end   = nsGetIntValue('night_end_hour', 22);
  var duration = Math.max(0, end - start);
  var hoursFactor = 0.6 + 0.15 * duration;
  if (hoursFactor < 0.5) hoursFactor = 0.5;
  if (hoursFactor > 1.6) hoursFactor = 1.6;

  var eventVisitorBonus = (tonight.totalVisitorBonusPct || 0) / 100;
  var eventFactor = 1 + eventVisitorBonus;

  var torchCheckbox = document.getElementById('night_torchlight');
  var torchEnabled = torchCheckbox ? torchCheckbox.checked : !!page.torchlightEnabled;
  var torchFactor = torchEnabled ? (1 + (page.torchlightVisitorBonus || 0.15)) : 1;

  var visitors = Math.round(Math.max(0, baseNightVisitors * priceFactor * hoursFactor * eventFactor * torchFactor));
  var baseline = baseNightVisitors || (daily * visitorFraction) || 1;
  var demandScore = Math.min(1, Math.max(0, visitors / (baseline * 1.6)));

  var level = demandScore < 0.4 ? 'Low' : demandScore < 0.7 ? 'Medium' : demandScore < 0.9 ? 'High' : 'Very high';

  var enabledTrails = nsCountEnabledTrails();
  var electricity = (page.electricityBase || 0) + enabledTrails * (page.electricityPerSlope || 0);

  var safetyLevel = nsGetSelectedRadioValue('night_safety_level', page.safetyLevel || 1);
  var safetyCost  = Number((page.safetyCosts || {})[String(safetyLevel)]) || 0;
  var groomingCost = enabledTrails * (page.groomingPerTrail || 0);

  var entSelect = document.getElementById('night_entertainment');
  var entKey = entSelect ? entSelect.value : (page.entertainmentKey || 'none');
  var entCost = Number((page.entertainmentCosts || {})[entKey]) || 0;

  var torchCost = torchEnabled ? (page.torchlightCost || 0) : 0;
  var eventCost = tonight.cost || 0;

  var schoolCheckbox = document.getElementById('night_school_enabled');
  var schoolEnabled = schoolCheckbox ? schoolCheckbox.checked : !!page.schoolEnabled;
  var schoolPrice = nsGetIntValue('night_school_price', page.schoolPrice || 0);

  var photoCheckbox = document.getElementById('night_photo_enabled');
  var photoEnabled = photoCheckbox ? photoCheckbox.checked : !!page.photoEnabled;
  var photoPrice = nsGetIntValue('night_photo_price', page.photoPrice || 0);

  var schoolRevenue = schoolEnabled ? Math.round(visitors * (demand.schoolVisitorFraction || 0.08) * Math.max(0, schoolPrice)) : 0;
  var photoRevenue  = photoEnabled  ? Math.round(visitors * (demand.photoVisitorFraction  || 0.05) * Math.max(0, photoPrice))  : 0;

  var ticketRevenue = Math.round(visitors * Math.max(0, ticketPrice));
  var baseRevenue   = ticketRevenue + schoolRevenue + photoRevenue;
  var eventRevenue  = Math.round(baseRevenue * Math.max(0, (tonight.revenueMultiplier || 1) - 1));
  var totalRevenue  = baseRevenue + eventRevenue;
  var totalCosts    = electricity + safetyCost + groomingCost + entCost + torchCost + eventCost;

  return {
    visitors, demandScore, demandLevel: level, ticketPrice, enabledTrails,
    electricityCost: electricity, safetyCost, groomingCost, entertainmentCost: entCost,
    torchCost, eventCost, ticketRevenue, schoolRevenue, photoRevenue, eventRevenue,
    totalRevenue, totalCosts, net: totalRevenue - totalCosts,
    factors: { baseNightVisitors, priceFactor, hoursFactor, dowFactor, eventVisitorBonus, torchEnabled }
  };
}

function nsUpdateLivePreview(s) {
  var set = function(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; };
  s = s || {};
  set('preview_visitors',   s.visitors != null ? s.visitors : '0');
  set('preview_ticket_rev', nsFormatEuro(s.ticketRevenue));
  set('preview_school',     nsFormatEuro(s.schoolRevenue));
  set('preview_photo',      nsFormatEuro(s.photoRevenue));
  set('preview_elec',       nsFormatEuro(-s.electricityCost));
  set('preview_safety',     nsFormatEuro(-s.safetyCost));
  set('preview_ent',        nsFormatEuro(-s.entertainmentCost));
  set('preview_grooming',   nsFormatEuro(-s.groomingCost));
  set('preview_torch',      nsFormatEuro(-s.torchCost));
  set('preview_total_rev',  nsFormatEuro(s.totalRevenue));
  set('preview_total_cost', nsFormatEuro(s.totalCosts));
  var netEl = document.getElementById('preview_net');
  if (netEl) {
    netEl.textContent = nsFormatEuro(s.net);
    netEl.style.color = s.net >= 0 ? '#4ade80' : '#f87171';
  }
}

function updateTrailQuality(val) {
  var v = parseInt(val) || 3;
  var base = (NightSkiingQualityConfig && NightSkiingQualityConfig.baseLoss) || 1.5;
  var factor = (NightSkiingQualityConfig && NightSkiingQualityConfig.brightnessFactor) || 0.5;
  var loss = base + (v * factor);
  var el = document.getElementById('quality_impact_display');
  if (el) el.textContent = '-' + loss.toFixed(1);
}

function nsUpdateForecastUI(s) {
  s = s || {};
  var set = function(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; };
  set('ns_forecast_total', s.visitors != null ? s.visitors : '0');
  set('ns_forecast_level_text', s.demandLevel || '-');

  var badge = document.getElementById('ns_forecast_level_badge');
  if (badge) {
    badge.textContent = s.demandLevel || '-';
    badge.className = 'badge text-xs ' + (s.demandLevel === 'Very high' ? 'badge-success' : s.demandLevel === 'High' ? 'badge-info' : s.demandLevel === 'Medium' ? 'badge-warning' : 'badge-neutral');
  }

  var explEl = document.getElementById('ns_forecast_explanation');
  if (explEl) {
    var reasons = [];
    var f = s.factors || {};
    if (f.priceFactor > 1.05) reasons.push('attractive ticket price');
    else if (f.priceFactor < 0.95) reasons.push('higher ticket price reducing demand');
    if (f.hoursFactor > 1.0) reasons.push('longer opening hours');
    else if (f.hoursFactor < 1.0) reasons.push('short session');
    if (f.eventVisitorBonus > 0) reasons.push("tonight's special events");
    if (f.torchEnabled) reasons.push('torchlight descent');
    if (!reasons.length) reasons.push('baseline demand');
    explEl.textContent = 'Demand driven by ' + reasons.join(', ') + '.';
  }

  var gaugeEl = document.getElementById('ns_forecast_gauge');
  if (gaugeEl && typeof Chart !== 'undefined') {
    var filled = Math.round(Math.max(0, Math.min(1, s.demandScore || 0)) * 100);
    if (!nsForecastChart) {
      nsForecastChart = new Chart(gaugeEl.getContext('2d'), {
        type: 'doughnut',
        data: { labels: ['Demand','Rest'], datasets: [{ data: [filled, 100-filled], backgroundColor: ['#22c55e','#111827'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false } } }
      });
    } else {
      nsForecastChart.data.datasets[0].data = [filled, 100-filled];
      nsForecastChart.update();
    }
  }
}

function nsUpdateCostUI(s) {
  s = s || {};
  var set = function(id, val) { var el = document.getElementById(id); if (el) el.textContent = nsFormatEuro(val); };
  set('ns_cost_electricity',   s.electricityCost);
  set('ns_cost_safety',        s.safetyCost);
  set('ns_cost_grooming',      s.groomingCost);
  set('ns_cost_entertainment', s.entertainmentCost);
  set('ns_cost_events',        (s.eventCost||0) + (s.torchCost||0));
  set('ns_cost_total',         s.totalCosts);

  var canvas = document.getElementById('ns_cost_chart');
  if (canvas && typeof Chart !== 'undefined') {
    var data = [s.electricityCost||0, s.safetyCost||0, s.groomingCost||0, s.entertainmentCost||0, (s.eventCost||0)+(s.torchCost||0)].map(v => Math.max(0,v));
    if (!nsCostChart) {
      nsCostChart = new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: { labels: ['Electricity','Safety','Grooming','Entertainment','Events'], datasets: [{ data, backgroundColor: ['#38bdf8','#f97316','#a855f7','#22c55e','#facc15'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '55%', plugins: { legend: { display: false } } }
      });
    } else {
      nsCostChart.data.datasets[0].data = data;
      nsCostChart.update();
    }
  }
}

function nsInitRevenueTrends() {
  var canvas = document.getElementById('ns_revenue_trend_chart');
  if (!canvas || typeof Settings === 'undefined' || !Settings.base_url) return;
  var summaryEl = document.getElementById('ns_revenue_summary');
  if (summaryEl) summaryEl.textContent = 'Loading…';
  fetch(Settings.base_url + 'night_skiing_controller/get_revenue_trends', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(d => {
      if (!d.success) { if (summaryEl) summaryEl.textContent = 'Unable to load.'; return; }
      if (typeof Chart !== 'undefined') {
        nsRevenueChart = new Chart(canvas.getContext('2d'), {
          type: 'bar',
          data: {
            labels: d.labels || [],
            datasets: [
              { label: 'Tickets', data: d.tickets || [], backgroundColor: '#60a5fa', stack: 'rev' },
              { label: 'School',  data: d.school  || [], backgroundColor: '#34d399', stack: 'rev' },
              { label: 'Photos',  data: d.photos  || [], backgroundColor: '#a855f7', stack: 'rev' },
              { label: 'Events',  data: d.events  || [], backgroundColor: '#f97316', stack: 'rev' }
            ]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
              legend: { position: 'bottom', labels: { color: 'rgba(255,255,255,0.7)', boxWidth: 12 } },
              tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + nsFormatEuro(ctx.parsed.y) } }
            },
            scales: {
              x: { stacked: true, ticks: { color: 'rgba(255,255,255,0.5)' }, grid: { color: 'rgba(255,255,255,0.05)' } },
              y: { stacked: true, beginAtZero: true, ticks: { color: 'rgba(255,255,255,0.5)', callback: v => nsFormatEuro(v) }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
          }
        });
      }
      if (summaryEl) {
        var sum = arr => (arr||[]).reduce((a,b) => a+(Number(b)||0), 0);
        var totalAll = sum(d.tickets) + sum(d.school) + sum(d.photos) + sum(d.events);
        summaryEl.textContent = totalAll > 0
          ? 'Last 7 nights: Tickets ' + nsFormatEuro(sum(d.tickets)) + ', School ' + nsFormatEuro(sum(d.school)) + ', Photos ' + nsFormatEuro(sum(d.photos)) + ', Events ' + nsFormatEuro(sum(d.events)) + '.'
          : 'No recent night-skiing revenue recorded yet.';
      }
    })
    .catch(() => { if (summaryEl) summaryEl.textContent = 'Network error.'; });
}

function nsRefreshAnalytics() {
  var state = nsComputeState();
  nsUpdateLivePreview(state);
  nsUpdateForecastUI(state);
  nsUpdateCostUI(state);
}

function openTrailModal(btn) {
  document.getElementById('trailModalName').textContent = btn.getAttribute('data-slope-name');
  document.getElementById('modal_slope_id').value       = btn.getAttribute('data-slope-id');
  document.getElementById('ns_enabled_toggle').checked = parseInt(btn.getAttribute('data-ns-enabled')) === 1;
  document.getElementById('modal_light_type').value    = btn.getAttribute('data-light-type');
  var brightness = parseInt(btn.getAttribute('data-brightness'));
  document.getElementById('modal_brightness').value         = brightness;
  document.getElementById('brightness_display').textContent = brightness;
  var spacing = parseInt(btn.getAttribute('data-pole-spacing'));
  var psRadio = document.getElementById('ps_' + spacing);
  if (psRadio) psRadio.checked = true;
  updateTrailQuality(brightness);
  document.getElementById('trailModal').showModal();
}

// ── Event scheduling ─────────────────────────────────────────────────────
function nsScheduleEventType(type) {
  var sel = document.getElementById('ns_event_type_select');
  if (sel) sel.value = type;
  var dateInput = document.getElementById('ns_event_date_input');
  if (dateInput) dateInput.focus();
  dateInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ── Events list ───────────────────────────────────────────────────────────
function nsLoadUpcomingEvents() {
  if (typeof Settings === 'undefined' || !Settings.base_url) return;
  var listEl = document.getElementById('ns_upcoming_events_list');
  fetch(Settings.base_url + 'night_skiing_controller/get_upcoming_events', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
      if (!data.success || !data.events) { listEl.innerHTML = '<p style="color:rgba(255,100,100,.7);font-size:.8rem;">Failed to load events.</p>'; return; }
      if (!data.events.length) { listEl.innerHTML = '<p style="color:rgba(255,255,255,.35);font-size:.8rem;font-style:italic;"><?php echo $this->lang->line('building')['night_skiing_event_no_events']; ?></p>'; return; }
      listEl.innerHTML = data.events.map(e => `
        <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:6px;padding:.5rem .75rem;">
          <div>
            <div style="color:white;font-weight:600;font-size:.82rem;">${e.type_label}</div>
            <div style="color:rgba(255,255,255,.45);font-size:.72rem;">${e.date} · ${e.cost} €</div>
          </div>
          <button onclick="nsCancelEvent(${e.id})" class="btn btn-xs btn-error btn-outline">✕</button>
        </div>
      `).join('');
    })
    .catch(() => { listEl.innerHTML = '<p style="color:rgba(255,100,100,.7);font-size:.8rem;">Error loading events.</p>'; });
}

function nsCancelEvent(id) {
  if (!confirm('Cancel this event? No refund.')) return;
  fetch(Settings.base_url + 'night_skiing_controller/cancel_event/' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => { data.success ? nsLoadUpcomingEvents() : alert('Error: ' + data.message); });
}

// ── Form validation ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  // Settings form validation
  var settingsForm = document.getElementById('ns_resort_settings_form');
  if (settingsForm) {
    settingsForm.addEventListener('submit', function(e) {
      var start = parseInt(document.getElementById('night_start_hour').value);
      var end   = parseInt(document.getElementById('night_end_hour').value);
      if (start >= end) { alert('End hour must be after start hour.'); e.preventDefault(); return; }
      var price = parseInt(document.getElementById('night_ticket_price').value);
      if (price < 0 || price > 500) { alert('Ticket price must be between 0 and 500 €.'); e.preventDefault(); return; }
    });
  }

  // Event schedule form via AJAX
  var eventForm = document.getElementById('ns_event_form');
  if (eventForm) {
    eventForm.addEventListener('submit', function(e) {
      e.preventDefault();
      fetch(eventForm.action, { method: 'POST', body: new FormData(eventForm), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
          if (data.success) { nsLoadUpcomingEvents(); eventForm.reset(); }
          else alert('Error: ' + (data.message || 'Unknown error'));
        })
        .catch(() => alert('Network error submitting form.'));
    });
  }

  // Wire all change listeners for live preview
  ['night_ticket_price','night_start_hour','night_end_hour','night_entertainment',
   'night_school_enabled','night_school_price','night_torchlight','night_photo_enabled','night_photo_price']
    .forEach(function(id) {
      var el = document.getElementById(id);
      if (!el) return;
      el.addEventListener(el.type === 'number' ? 'input' : 'change', nsRefreshAnalytics);
    });
  document.querySelectorAll('input[name="night_safety_level"]').forEach(r => r.addEventListener('change', nsRefreshAnalytics));
  document.querySelectorAll('.ns-trail-toggle').forEach(t => t.addEventListener('change', nsRefreshAnalytics));

  nsRefreshAnalytics();
  setTimeout(nsInitRevenueTrends, 700);
  setTimeout(nsLoadUpcomingEvents, 600);
});
</script>

</div>
