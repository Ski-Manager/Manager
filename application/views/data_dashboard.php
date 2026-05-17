<div class="w-full">
<?php
// ── Precompute display vars ──────────────────────────────────────────────
$kpi     = $kpi     ?? [];
$cash    = $kpi['cash']           ?? 0;
$rep     = $kpi['reputation']     ?? 0;
$pass    = $kpi['skipass_daily']  ?? 0;
$vis     = $kpi['total_visitors'] ?? 0;
$snow    = $kpi['snow_level']    ?? 0;
$oSlopes = $kpi['open_slopes']   ?? 0;
$tSlopes = $kpi['total_slopes']  ?? 0;
$oLifts  = $kpi['open_lifts']    ?? 0;
$tLifts  = $kpi['total_lifts']   ?? 0;
$rid     = $currentResortId ?? 0;

function fmtEuro($v) { return number_format((int)$v, 0, ',', ' ') . ' €'; }
function condBar($pct, $id = '') {
    $pct = max(0, min(100, (int)$pct));
    $col = $pct >= 70 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
    return '<div style="background:rgba(255,255,255,0.1);border-radius:4px;height:6px;min-width:60px;">
      <div style="background:'.$col.';height:6px;border-radius:4px;width:'.$pct.'%;"></div></div>
      <span style="font-size:.65rem;color:rgba(255,255,255,.65);">'.$pct.'%</span>';
}
$diffBadge = [
    1 => ['label'=>'Green',  'bg'=>'rgba(16,185,129,0.2)', 'color'=>'#34d399'],
    2 => ['label'=>'Blue',   'bg'=>'rgba(59,130,246,0.2)', 'color'=>'#60a5fa'],
    3 => ['label'=>'Red',    'bg'=>'rgba(239,68,68,0.2)',  'color'=>'#f87171'],
    4 => ['label'=>'Black',  'bg'=>'rgba(55,65,81,0.5)',   'color'=>'#e5e7eb'],
];
$siteLang = $this->session->userdata('site_lang') ?: 'english';
$langCol  = 'name_' . $siteLang;
?>

<!-- ===== ANALYTICS BANNER ===== -->
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:12px;padding:2.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.06);">
  <!-- Grid background decoration -->
  <svg style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0.04;" viewBox="0 0 800 200" preserveAspectRatio="xMidYMid slice">
    <?php for ($x = 0; $x <= 800; $x += 40): ?><line x1="<?php echo $x; ?>" y1="0" x2="<?php echo $x; ?>" y2="200" stroke="white" stroke-width="1"/><?php endfor; ?>
    <?php for ($y = 0; $y <= 200; $y += 40): ?><line x1="0" y1="<?php echo $y; ?>" x2="800" y2="<?php echo $y; ?>" stroke="white" stroke-width="1"/><?php endfor; ?>
  </svg>
  <div style="position:relative;z-index:1;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;">
      <div>
        <h3 style="color:white;font-size:1.75rem;font-weight:bold;text-shadow:0 0 20px rgba(147,197,253,0.4);margin:0;">
          📊 <?php echo $this->lang->line('data_dashboard')['title']; ?>
        </h3>
        <p style="color:rgba(255,255,255,0.6);margin:0.4rem 0 0;font-size:0.88rem;">
          <?php echo $this->lang->line('data_dashboard')['intro']; ?>
        </p>
      </div>
      <div style="background:rgba(255,255,255,0.07);border-radius:8px;padding:.5rem 1rem;border:1px solid rgba(255,255,255,0.1);white-space:nowrap;">
        <div style="color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;"><?php echo $this->lang->line('data_dashboard')['last_updated']; ?></div>
        <div style="color:white;font-weight:bold;font-size:.9rem;"><?php echo gmdate('Y-m-d H:i'); ?> UTC</div>
      </div>
    </div>

    <!-- 6 KPI cards -->
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:.9rem;">
      <!-- Cash -->
      <div style="background:rgba(255,255,255,0.06);border-radius:10px;padding:.9rem;border:1px solid rgba(255,255,255,0.08);">
        <div style="color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;"><?php echo $this->lang->line('data_dashboard')['kpi_cash']; ?></div>
        <div style="color:#4ade80;font-size:1.1rem;font-weight:bold;"><?php echo fmtEuro($cash); ?></div>
      </div>
      <!-- Reputation -->
      <div style="background:rgba(255,255,255,0.06);border-radius:10px;padding:.9rem;border:1px solid rgba(255,255,255,0.08);">
        <div style="color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;"><?php echo $this->lang->line('data_dashboard')['kpi_reputation']; ?></div>
        <div style="color:#facc15;font-size:1.1rem;font-weight:bold;"><?php echo $rep; ?> / 100</div>
        <div style="background:rgba(255,255,255,.1);border-radius:3px;height:3px;margin-top:.35rem;"><div style="background:#facc15;height:3px;border-radius:3px;width:<?php echo $rep; ?>%;"></div></div>
      </div>
      <!-- Snow Level -->
      <div style="background:rgba(255,255,255,0.06);border-radius:10px;padding:.9rem;border:1px solid rgba(255,255,255,0.08);">
        <div style="color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;"><?php echo $this->lang->line('data_dashboard')['kpi_snow_level']; ?></div>
        <div style="color:#93c5fd;font-size:1.1rem;font-weight:bold;"><?php echo $snow; ?> cm</div>
      </div>
      <!-- Daily Visitors -->
      <div style="background:rgba(255,255,255,0.06);border-radius:10px;padding:.9rem;border:1px solid rgba(255,255,255,0.08);">
        <div style="color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;"><?php echo $this->lang->line('data_dashboard')['kpi_visitors']; ?></div>
        <div style="color:white;font-size:1.1rem;font-weight:bold;"><?php echo number_format($vis, 0, ',', ' '); ?></div>
        <div style="color:rgba(255,255,255,.65);font-size:.65rem;"><?php echo $this->lang->line('data_dashboard')['kpi_per_day']; ?></div>
      </div>
      <!-- Slopes -->
      <div style="background:rgba(255,255,255,0.06);border-radius:10px;padding:.9rem;border:1px solid rgba(255,255,255,0.08);">
        <div style="color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;"><?php echo $this->lang->line('data_dashboard')['kpi_slopes']; ?></div>
        <div style="color:white;font-size:1.1rem;font-weight:bold;"><?php echo $oSlopes; ?> <span style="opacity:.5;font-size:.8rem;">/ <?php echo $tSlopes; ?></span></div>
        <div style="color:#34d399;font-size:.65rem;"><?php echo $this->lang->line('data_dashboard')['kpi_open']; ?></div>
      </div>
      <!-- Lifts -->
      <div style="background:rgba(255,255,255,0.06);border-radius:10px;padding:.9rem;border:1px solid rgba(255,255,255,0.08);">
        <div style="color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;"><?php echo $this->lang->line('data_dashboard')['kpi_lifts']; ?></div>
        <div style="color:white;font-size:1.1rem;font-weight:bold;"><?php echo $oLifts; ?> <span style="opacity:.5;font-size:.8rem;">/ <?php echo $tLifts; ?></span></div>
        <div style="color:#34d399;font-size:.65rem;"><?php echo $this->lang->line('data_dashboard')['kpi_open']; ?></div>
      </div>
    </div>
  </div>
</div>

<?php if ($resort_built): ?>

<!-- ===== CHARTS GRID: Row 1 ===== -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

  <!-- Revenue Trend (14 days) -->
  <div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg">
    <div class="card-body">
      <h4 class="h4 text-white mb-1">📈 Revenue Trend <span style="font-size:.75rem;color:rgba(255,255,255,.65);font-weight:normal;">last 14 days</span></h4>
      <div style="height:240px;"><canvas id="chart_revenue_trend"></canvas></div>
    </div>
  </div>

  <!-- Profit Breakdown -->
  <div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg">
    <div class="card-body">
      <h4 class="h4 text-white mb-1">💰 Revenue by Source <span style="font-size:.75rem;color:rgba(255,255,255,.65);font-weight:normal;">yesterday</span></h4>
      <div style="height:240px;"><canvas id="chart_profit_breakdown"></canvas></div>
    </div>
  </div>
</div>

<!-- ===== CHARTS GRID: Row 2 ===== -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

  <!-- Visitor Segmentation -->
  <div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg">
    <div class="card-body">
      <h4 class="h4 text-white mb-1">👥 Visitor Segmentation <span style="font-size:.75rem;color:rgba(255,255,255,.65);font-weight:normal;">by difficulty</span></h4>
      <div style="height:240px;"><canvas id="chart_visitor_segmentation"></canvas></div>
    </div>
  </div>

  <!-- Traffic / Condition Heatmap -->
  <div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg">
    <div class="card-body">
      <h4 class="h4 text-white mb-1">🔥 Traffic Heatmap <span style="font-size:.75rem;color:rgba(255,255,255,.65);font-weight:normal;">lifts & slopes</span></h4>
      <div style="height:240px;overflow-y:auto;"><canvas id="chart_traffic_heatmap"></canvas></div>
    </div>
  </div>
</div>

<!-- ===== CHARTS GRID: Row 3 ===== -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

  <!-- Accident Probability -->
  <div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg">
    <div class="card-body">
      <h4 class="h4 text-white mb-1">⚠️ Accident Risk <span style="font-size:.75rem;color:rgba(255,255,255,.65);font-weight:normal;">per slope (0–100)</span></h4>
      <div style="height:240px;overflow-y:auto;"><canvas id="chart_accident_probability"></canvas></div>
    </div>
  </div>

  <!-- Cost Trend -->
  <div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg">
    <div class="card-body">
      <h4 class="h4 text-white mb-1">💸 Cost Trend <span style="font-size:.75rem;color:rgba(255,255,255,.65);font-weight:normal;">last 14 days</span></h4>
      <div style="height:240px;"><canvas id="chart_cost_trend"></canvas></div>
    </div>
  </div>
</div>

<!-- ===== SLOPES TABLE ===== -->
<div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg mb-4">
  <div class="card-body">
    <h4 class="h4 text-white mb-3">🎿 Slopes Overview</h4>
    <?php if (empty($slopes_detail)): ?>
      <p style="color:rgba(255,255,255,.65);font-size:.85rem;">No slopes built yet.</p>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="table table-sm w-full" style="font-size:.82rem;">
        <thead>
          <tr style="color:rgba(255,255,255,.65);font-size:.72rem;text-transform:uppercase;border-bottom:1px solid rgba(255,255,255,.08);">
            <th style="padding:.5rem;">Slope</th>
            <th style="padding:.5rem;">Difficulty</th>
            <th style="padding:.5rem;min-width:100px;">Condition</th>
            <th style="padding:.5rem;">Daily Visitors</th>
            <th style="padding:.5rem;">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($slopes_detail as $s):
            $sName = !empty($s->custom_name) ? $s->custom_name : (isset($s->$langCol) ? $s->$langCol : $s->name_english);
            $diff  = (int)$s->id_difficulty;
            $db    = $diffBadge[$diff] ?? ['label'=>"D$diff",'bg'=>'rgba(255,255,255,.1)','color'=>'white'];
          ?>
          <tr style="border-bottom:1px solid rgba(255,255,255,.04);">
            <td style="padding:.5rem;color:white;font-weight:500;"><?php echo htmlspecialchars($sName); ?></td>
            <td style="padding:.5rem;">
              <span style="background:<?php echo $db['bg']; ?>;color:<?php echo $db['color']; ?>;border-radius:4px;padding:.15rem .5rem;font-size:.7rem;font-weight:bold;"><?php echo $db['label']; ?></span>
            </td>
            <td style="padding:.5rem;"><?php echo condBar((int)$s->slope_condition); ?></td>
            <td style="padding:.5rem;color:rgba(255,255,255,.75);"><?php echo number_format((int)$s->daily_visitors, 0, ',', ' '); ?></td>
            <td style="padding:.5rem;">
              <?php if ($s->id_status == 1): ?>
                <span class="badge badge-success badge-sm">Open</span>
              <?php else: ?>
                <span class="badge badge-neutral badge-sm">Closed</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ===== LIFTS TABLE ===== -->
<div class="card bg-[#0f172a] border border-[#1e293b] shadow-lg mb-4">
  <div class="card-body">
    <h4 class="h4 text-white mb-3">🚡 Lifts Overview</h4>
    <?php if (empty($lifts_detail)): ?>
      <p style="color:rgba(255,255,255,.65);font-size:.85rem;">No lifts built yet.</p>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="table table-sm w-full" style="font-size:.82rem;">
        <thead>
          <tr style="color:rgba(255,255,255,.65);font-size:.72rem;text-transform:uppercase;border-bottom:1px solid rgba(255,255,255,.08);">
            <th style="padding:.5rem;">Lift</th>
            <th style="padding:.5rem;">Throughput</th>
            <th style="padding:.5rem;min-width:100px;">Condition</th>
            <th style="padding:.5rem;">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lifts_detail as $l):
            $lName = !empty($l->custom_name) ? $l->custom_name : (isset($l->$langCol) ? $l->$langCol : $l->name_english);
          ?>
          <tr style="border-bottom:1px solid rgba(255,255,255,.04);">
            <td style="padding:.5rem;color:white;font-weight:500;"><?php echo htmlspecialchars($lName); ?></td>
            <td style="padding:.5rem;color:rgba(255,255,255,.75);"><?php echo number_format((int)$l->throughput, 0, ',', ' '); ?>/h</td>
            <td style="padding:.5rem;"><?php echo condBar((int)$l->lift_condition); ?></td>
            <td style="padding:.5rem;">
              <?php if ($l->id_status == 1): ?>
                <span class="badge badge-success badge-sm">Open</span>
              <?php else: ?>
                <span class="badge badge-neutral badge-sm">Closed</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<input type="hidden" id="currentResortId" value="<?php echo $rid; ?>">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const RID = document.getElementById('currentResortId').value;
const BASE = (typeof Settings !== 'undefined' && Settings.base_url) ? Settings.base_url : '';

Chart.defaults.color = 'rgba(255,255,255,0.55)';

function fmtEuro(v) {
  const n = Math.round(Number(v) || 0);
  return (n < 0 ? '-' : '') + Math.abs(n).toLocaleString('fr-FR') + ' €';
}

function postAjax(endpoint, onSuccess) {
  const fd = new FormData();
  fd.append('currentResortID', RID);
  fetch(BASE + 'data_dashboard_controller/' + endpoint, {
    method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd
  }).then(r => r.json()).then(d => { if (d.success) onSuccess(d); }).catch(console.error);
}

// ── Revenue Trend (stacked bar) ──────────────────────────────────────────
postAjax('get_revenue_trend_chart', function(d) {
  new Chart(document.getElementById('chart_revenue_trend').getContext('2d'), {
    type: 'bar',
    data: {
      labels: d.dates.map(dt => dt.slice(5)),
      datasets: [
        { label: 'Ski Pass', data: d.skipass, backgroundColor: '#3b82f6', stack: 'rev' },
        { label: 'Other',    data: d.other,   backgroundColor: '#8b5cf6', stack: 'rev' },
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, color: 'rgba(255,255,255,.55)' } },
        tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + fmtEuro(ctx.parsed.y) } }
      },
      scales: {
        x: { stacked: true, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: 'rgba(255,255,255,.65)', maxRotation: 0 } },
        y: { stacked: true, beginAtZero: true, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: 'rgba(255,255,255,.65)', callback: v => fmtEuro(v) } }
      }
    }
  });
});

// ── Profit Breakdown (donut) ────────────────────────────────────────────
postAjax('get_profit_breakdown_chart', function(d) {
  new Chart(document.getElementById('chart_profit_breakdown').getContext('2d'), {
    type: 'doughnut',
    data: { labels: d.labels, datasets: [{ data: d.data, backgroundColor: d.colors, borderWidth: 1, borderColor: '#0f172a' }] },
    options: {
      responsive: true, maintainAspectRatio: false, cutout: '60%',
      plugins: {
        legend: { position: 'right', labels: { boxWidth: 10, color: 'rgba(255,255,255,.55)', font: { size: 11 } } },
        tooltip: { callbacks: { label: ctx => ctx.label + ': ' + fmtEuro(ctx.parsed) } }
      }
    }
  });
});

// ── Visitor Segmentation (donut) ────────────────────────────────────────
postAjax('get_visitor_segmentation_chart', function(d) {
  new Chart(document.getElementById('chart_visitor_segmentation').getContext('2d'), {
    type: 'doughnut',
    data: { labels: d.labels, datasets: [{ data: d.data, backgroundColor: d.colors, borderWidth: 1, borderColor: '#0f172a' }] },
    options: {
      responsive: true, maintainAspectRatio: false, cutout: '60%',
      plugins: {
        legend: { position: 'right', labels: { boxWidth: 10, color: 'rgba(255,255,255,.55)', font: { size: 11 } } },
        tooltip: { callbacks: { label: ctx => ctx.label + ': ' + (ctx.parsed || 0) + ' visitors' } }
      }
    }
  });
});

// ── Traffic Heatmap (horizontal bar) ────────────────────────────────────
postAjax('get_traffic_heatmap_chart', function(d) {
  const lifts  = d.rows.filter(r => r.type === 'lift');
  const slopes = d.rows.filter(r => r.type === 'slope');
  const all    = [...lifts, ...slopes];
  const dynH   = Math.max(200, all.length * 28 + 40);
  document.getElementById('chart_traffic_heatmap').parentElement.style.height = dynH + 'px';
  document.getElementById('chart_traffic_heatmap').style.height = dynH + 'px';

  new Chart(document.getElementById('chart_traffic_heatmap').getContext('2d'), {
    type: 'bar',
    data: {
      labels: all.map(r => r.label),
      datasets: [{
        label: 'Intensity',
        data: all.map(r => r.value),
        backgroundColor: all.map(r => r.value >= 75 ? '#ef4444' : r.value >= 40 ? '#f97316' : '#22c55e'),
        borderRadius: 3,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => 'Intensity: ' + ctx.parsed.x + '%' } } },
      scales: {
        x: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: 'rgba(255,255,255,.65)', callback: v => v + '%' } },
        y: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,.55)', font: { size: 11 } } }
      }
    }
  });
});

// ── Accident Probability (horizontal bar) ────────────────────────────────
postAjax('get_accident_probability_chart', function(d) {
  const dynH = Math.max(200, d.labels.length * 28 + 40);
  document.getElementById('chart_accident_probability').parentElement.style.height = dynH + 'px';
  document.getElementById('chart_accident_probability').style.height = dynH + 'px';

  new Chart(document.getElementById('chart_accident_probability').getContext('2d'), {
    type: 'bar',
    data: {
      labels: d.labels,
      datasets: [{
        label: 'Risk Score',
        data: d.data,
        backgroundColor: d.colors,
        borderRadius: 3,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => 'Risk: ' + ctx.parsed.x + '/100' } } },
      scales: {
        x: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: 'rgba(255,255,255,.65)', callback: v => v } },
        y: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,.55)', font: { size: 11 } } }
      }
    }
  });
});

// ── Cost Trend (stacked bar) ─────────────────────────────────────────────
postAjax('get_cost_trend_chart', function(d) {
  new Chart(document.getElementById('chart_cost_trend').getContext('2d'), {
    type: 'bar',
    data: {
      labels: d.dates.map(dt => dt.slice(5)),
      datasets: [
        { label: 'Upkeep',    data: d.upkeep,    backgroundColor: '#f59e0b', stack: 'cost' },
        { label: 'Salaries',  data: d.salaries,  backgroundColor: '#f97316', stack: 'cost' },
        { label: 'Expenses',  data: d.expenses,  backgroundColor: '#ef4444', stack: 'cost' },
        { label: 'Purchases', data: d.purchases, backgroundColor: '#8b5cf6', stack: 'cost' },
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, color: 'rgba(255,255,255,.55)' } },
        tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + fmtEuro(ctx.parsed.y) } }
      },
      scales: {
        x: { stacked: true, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: 'rgba(255,255,255,.65)', maxRotation: 0 } },
        y: { stacked: true, beginAtZero: true, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: 'rgba(255,255,255,.65)', callback: v => fmtEuro(v) } }
      }
    }
  });
});
</script>

<?php endif; ?>
</div>
