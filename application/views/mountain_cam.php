<div class="w-full">
<?php
if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = ['mountain_cam_settings_saved','mountain_cam_invalid_settings','mountain_cam_save_error'];
    if (in_array($infoMessage, $msg_keys, TRUE)) echo $this->lang->line('building')[$infoMessage];
}
$quality_labels = [1=>'SD',2=>'HD',3=>'4K'];
$quality_label  = $quality_labels[$cam_quality] ?? 'SD';
$ci =& get_instance();
$site_lang = $ci->session->userdata('site_lang') ?: 'english';
$is_fr = ($site_lang === 'french');
?>

<!-- ════ HERO BANNER ════ -->
<div style="background:linear-gradient(135deg,#0b1a2e 0%,#132540 45%,#0f2035 100%);border-radius:14px;padding:2.5rem 2rem;margin-bottom:1.5rem;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.07);">
  <svg style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;" viewBox="0 0 1000 220" preserveAspectRatio="xMidYMid slice">
    <circle cx="55" cy="18" r="1.2" fill="white" opacity=".85"/><circle cx="130" cy="50" r="0.8" fill="white" opacity=".6"/>
    <circle cx="210" cy="16" r="1.3" fill="white" opacity=".8"/><circle cx="300" cy="58" r="0.9" fill="white" opacity=".5"/>
    <circle cx="390" cy="26" r="1.1" fill="white" opacity=".9"/><circle cx="480" cy="72" r="0.8" fill="white" opacity=".6"/>
    <circle cx="560" cy="19" r="1.4" fill="white" opacity=".8"/><circle cx="650" cy="54" r="0.9" fill="white" opacity=".7"/>
    <circle cx="740" cy="30" r="1.1" fill="white" opacity=".9"/><circle cx="840" cy="60" r="0.8" fill="white" opacity=".6"/>
    <circle cx="920" cy="19" r="1.3" fill="white" opacity=".8"/><circle cx="80" cy="138" r="0.9" fill="white" opacity=".55"/>
    <circle cx="200" cy="153" r="0.7" fill="white" opacity=".5"/><circle cx="370" cy="143" r="1.0" fill="white" opacity=".65"/>
    <circle cx="510" cy="158" r="0.8" fill="white" opacity=".55"/><circle cx="650" cy="146" r="1.0" fill="white" opacity=".65"/>
    <circle cx="780" cy="153" r="0.7" fill="white" opacity=".5"/><circle cx="900" cy="140" r="1.1" fill="white" opacity=".75"/>
    <polygon points="0,220 80,160 160,195 270,105 380,150 470,80 570,130 680,60 800,110 900,75 1000,100 1000,220" fill="rgba(15,35,65,0.55)"/>
    <g transform="translate(895,35)" fill="none" stroke="rgba(96,165,250,0.45)" stroke-width="2">
      <path d="M-18,-18 A25,25 0 0,1 18,-18" stroke-linecap="round"/>
      <path d="M-30,-30 A42,42 0 0,1 30,-30" stroke-linecap="round" opacity=".6"/>
      <path d="M-42,-42 A59,59 0 0,1 42,-42" stroke-linecap="round" opacity=".3"/>
    </g>
    <circle cx="895" cy="38" r="5" fill="rgba(96,165,250,0.8)"/>
  </svg>
  <div style="position:relative;z-index:1;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
      <div>
        <h3 style="color:white;font-size:1.75rem;font-weight:bold;text-shadow:0 0 20px rgba(96,165,250,0.5);margin:0;">
          📷 <?php echo $this->lang->line('building')['mountain_cam_title']; ?>
        </h3>
        <p style="color:rgba(255,255,255,0.6);margin:0.4rem 0 0;font-size:0.88rem;max-width:540px;">
          <?php echo $this->lang->line('building')['mountain_cam_page_intro']; ?>
        </p>
      </div>
      <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <?php if ($is_enabled==1): ?>
          <span class="badge badge-success" style="font-size:.95rem;padding:.45rem 1rem;">📡 <?php echo $this->lang->line('building')['mountain_cam_on']; ?></span>
        <?php else: ?>
          <span class="badge badge-neutral" style="font-size:.95rem;padding:.45rem 1rem;">⏸ <?php echo $this->lang->line('building')['mountain_cam_off']; ?></span>
        <?php endif; ?>
        <?php if ($resort_is_open): ?>
          <span class="badge badge-success" style="font-size:.85rem;padding:.4rem .9rem;">⛷ <?php echo $is_fr ? 'Station ouverte' : 'Resort Open'; ?></span>
        <?php else: ?>
          <span class="badge badge-error" style="font-size:.85rem;padding:.4rem .9rem;">🔒 <?php echo $is_fr ? 'Station fermée' : 'Resort Closed'; ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.85rem;">
      <?php
      $hs = [
        ['📷','#60a5fa',$num_cams.' cam'.($num_cams>1?'s':''),$this->lang->line('building')['mountain_cam_num_cams_label']],
        ['🔭','#34d399',$quality_label,$this->lang->line('building')['mountain_cam_quality_label']],
        ['💶','#fbbf24',number_format($actual_daily_cost,0,',',' ').' €',$this->lang->line('building')['mountain_cam_daily_cost_label']],
        ['⭐','#a78bfa','+'.MOUNTAIN_CAM_REP_BONUS_PER_DAY.' / day',$this->lang->line('building')['mountain_cam_rep_bonus_label']],
      ];
      foreach ($hs as $s): ?>
      <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:.9rem .8rem;">
        <div style="font-size:1.3rem;margin-bottom:.3rem;"><?php echo $s[0]; ?></div>
        <div style="font-size:1.1rem;font-weight:bold;color:<?php echo $s[1]; ?>;"><?php echo $s[2]; ?></div>
        <div style="font-size:.72rem;color:rgba(255,255,255,0.5);margin-top:.15rem;"><?php echo $s[3]; ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ════ LIVE FEEDS ════ -->
<div class="card bg-base-100 shadow-sm mb-4"><div class="card-body">
  <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <h4 class="h4" style="margin:0;">
      <?php if ($is_enabled): ?><span style="color:#ef4444;animation:mcam-blink 1.1s step-end infinite;">●</span>
      <?php else: ?><span style="color:#555;">●</span><?php endif; ?>
      <?php echo $is_fr ? 'Flux en direct' : 'Live Feeds'; ?>
    </h4>
    <?php if ($is_enabled): ?>
      <span class="badge badge-error" style="font-size:.68rem;">LIVE</span>
      <?php if ($stream_mode): ?><span class="badge" style="background:#1d4ed8;color:white;font-size:.68rem;">▶ STREAMING</span><?php endif; ?>
      <?php if ($night_vision): ?><span class="badge" style="background:#064e3b;color:#6ee7b7;font-size:.68rem;">◑ NIGHT VISION</span><?php endif; ?>
      <?php if ($weather_overlay): ?><span class="badge" style="background:#1e3a5f;color:#93c5fd;font-size:.68rem;">🌡 WEATHER</span><?php endif; ?>
      <?php if ($social_media): ?><span class="badge" style="background:#1e1b4b;color:#a5b4fc;font-size:.68rem;">📲 SOCIAL</span><?php endif; ?>
    <?php else: ?><span class="badge badge-neutral" style="font-size:.68rem;">OFFLINE</span><?php endif; ?>
  </div>
  <div id="mcam-grid" style="display:grid;gap:8px;">
    <?php for ($ci_idx=0;$ci_idx<$num_cams;$ci_idx++): ?>
    <div class="mcam-cell" data-cam-index="<?php echo $ci_idx; ?>"
         style="position:relative;cursor:pointer;border-radius:6px;overflow:hidden;background:#060606;border:1px solid rgba(255,255,255,0.1);"
         title="<?php echo $is_fr ? 'Cliquer pour agrandir' : 'Click to enlarge'; ?>">
      <div style="position:relative;padding-top:56.25%;">
        <canvas id="mcam-canvas-<?php echo $ci_idx; ?>" width="640" height="360"
                style="position:absolute;top:0;left:0;width:100%;height:100%;display:block;"></canvas>
      </div>
      <div class="mcam-hov" style="position:absolute;inset:0;background:rgba(0,0,0,0);transition:background .2s;display:flex;align-items:center;justify-content:center;pointer-events:none;">
        <span style="color:white;font-size:2.2rem;opacity:0;transition:opacity .2s;text-shadow:0 2px 8px rgba(0,0,0,0.8);">⛶</span>
      </div>
    </div>
    <?php endfor; ?>
  </div>
</div></div>

<!-- ════ FULLSCREEN MODAL ════ -->
<div id="mcam-modal" style="display:none;opacity:0;transition:opacity .22s ease;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.88);align-items:center;justify-content:center;padding:3rem 5rem;">
  <div id="mcam-modal-inner" style="position:relative;max-width:1280px;width:100%;">
    <canvas id="mcam-modal-canvas" width="1280" height="720" style="width:100%;display:block;border-radius:8px;border:1px solid rgba(255,255,255,.12);"></canvas>
    <div id="mcam-modal-label" style="color:rgba(255,255,255,.8);font-family:monospace;font-size:.8rem;margin-top:.5rem;text-align:center;"></div>
    <button id="mcam-close" style="position:absolute;top:-2.2rem;right:0;background:none;border:none;color:white;font-size:1.6rem;cursor:pointer;line-height:1;opacity:.75;">✕</button>
    <button id="mcam-prev" style="position:absolute;top:50%;left:-54px;transform:translateY(-50%);background:rgba(255,255,255,.12);border:none;color:white;font-size:2rem;cursor:pointer;border-radius:6px;padding:.3rem .7rem;">‹</button>
    <button id="mcam-next" style="position:absolute;top:50%;right:-54px;transform:translateY(-50%);background:rgba(255,255,255,.12);border:none;color:white;font-size:2rem;cursor:pointer;border-radius:6px;padding:.3rem .7rem;">›</button>
  </div>
</div>

<!-- ════ SETTINGS + KEY FIGURES ════ -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
<div class="card bg-base-100 shadow-sm"><div class="card-body">
  <h4 class="h4"><?php echo $is_fr?'Paramètres':'Settings'; ?></h4>
  <form method="post" action="<?php echo base_url('mountain_cam_controller/save'); ?>">
    <input type="hidden" name="mountain_cam_form" value="1">
    <table class="table table-sm w-full"><tbody>
    <tr><th><?php echo $this->lang->line('building')['mountain_cam_enable_label']; ?></th><td>
      <input class="toggle" type="checkbox" id="is_enabled" name="is_enabled" value="1" <?php echo $is_enabled==1?'checked':''; ?>>
      <label for="is_enabled"><?php echo $this->lang->line('building')['mountain_cam_enable_label']; ?></label>
    </td></tr>
    <tr><th><?php echo $this->lang->line('building')['mountain_cam_num_cams_label']; ?></th><td>
      <input class="input border border-base-300 input-sm w-20" type="number" name="num_cams" value="<?php echo $num_cams; ?>" min="<?php echo $min_cams; ?>" max="<?php echo $max_cams; ?>">
    </td></tr>
    <tr><th><?php echo $this->lang->line('building')['mountain_cam_quality_label']; ?></th><td>
      <select class="select border border-base-300 select-sm" name="cam_quality">
        <?php foreach ($valid_qualities as $q): ?>
          <option value="<?php echo $q; ?>" <?php echo $cam_quality==$q?'selected':''; ?>>
            <?php echo ['','SD','HD','4K'][$q]??''; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </td></tr>
    <tr><th><?php echo $this->lang->line('building')['mountain_cam_stream_label']; ?></th><td>
      <input class="toggle" type="checkbox" id="stream_mode" name="stream_mode" value="1" <?php echo $stream_mode==1?'checked':''; ?>>
      <label for="stream_mode"><?php echo $this->lang->line('building')['mountain_cam_stream_label']; ?></label>
    </td></tr>
    <tr><th><?php echo $this->lang->line('building')['mountain_cam_social_label']; ?></th><td>
      <input class="toggle" type="checkbox" id="social_media" name="social_media" value="1" <?php echo $social_media==1?'checked':''; ?>>
      <label for="social_media"><?php echo $this->lang->line('building')['mountain_cam_social_label']; ?></label>
    </td></tr>
    <tr><th><?php echo $this->lang->line('building')['mountain_cam_night_vision_label']; ?></th><td>
      <input class="toggle" type="checkbox" id="night_vision" name="night_vision" value="1" <?php echo $night_vision==1?'checked':''; ?>>
      <label for="night_vision"><?php echo $this->lang->line('building')['mountain_cam_night_vision_label']; ?></label>
    </td></tr>
    <tr><th><?php echo $this->lang->line('building')['mountain_cam_weather_overlay_label']; ?></th><td>
      <input class="toggle" type="checkbox" id="weather_overlay" name="weather_overlay" value="1" <?php echo $weather_overlay==1?'checked':''; ?>>
      <label for="weather_overlay"><?php echo $this->lang->line('building')['mountain_cam_weather_overlay_label']; ?></label>
    </td></tr>
    </tbody></table>
    <div style="margin-top:1rem;"><button type="submit" class="btn btn-primary btn-sm"><?php echo $is_fr ? 'Enregistrer' : 'Save settings'; ?></button></div>
  </form>
</div></div>
<div class="card bg-base-100 shadow-sm"><div class="card-body">
  <h4 class="h4"><?php echo $is_fr?'Chiffres clés':'Key Figures'; ?></h4>
  <?php
  $on_badge  = '<span class="badge badge-success badge-sm">'.($is_fr?'Activé':'On').'</span>';
  $off_badge = '<span class="badge badge-neutral badge-sm">'.($is_fr?'Désactivé':'Off').'</span>';
  $rows=[
    [$this->lang->line('building')['mountain_cam_status_label'], $is_enabled==1?$on_badge:$off_badge],
    [$this->lang->line('building')['mountain_cam_num_cams_label'], $num_cams],
    [$this->lang->line('building')['mountain_cam_quality_label'], $quality_label],
    [$this->lang->line('building')['mountain_cam_daily_cost_label'], number_format($actual_daily_cost,0,',',' ').' €'],
    [$this->lang->line('building')['mountain_cam_rep_bonus_label'], '+'.MOUNTAIN_CAM_REP_BONUS_PER_DAY.' / '.($is_fr?'jour':'day')],
    [$is_fr?'Statut station':'Resort Status', $resort_is_open
      ? '<span class="badge badge-success badge-sm">'.($is_fr?'Ouverte':'Open').'</span>'
      : '<span class="badge badge-error badge-sm">'.($is_fr?'Fermée':'Closed').'</span>'],
  ];
  ?>
  <table class="table table-sm w-full"><tbody>
  <?php foreach($rows as $r): ?>
    <tr><th><?php echo $r[0]; ?></th><td><?php echo $r[1]; ?></td></tr>
  <?php endforeach; ?>
  </tbody></table>
</div></div>
</div>

<!-- ════ HOW IT WORKS ════ -->
<div class="card bg-base-100 shadow-sm mb-4"><div class="card-body">
  <h4 class="h4"><?php echo $is_fr?'Comment ça marche':'How It Works'; ?></h4>
  <p class="text-sm text-base-content/70">
    <?php echo $is_fr
      ? 'Les caméras de montagne diffusent en temps réel les pistes, les remontées mécaniques et les conditions d\'enneigement. Le statut d\'ouverture/fermeture de la station est automatiquement reflété sur les flux.'
      : 'Mountain cameras broadcast real-time views of slopes, lifts and snow conditions. The resort open/closed status is automatically reflected on the feeds — closed resort means stationary lifts, no skiers, and no snowmaking.'; ?>
  </p>
</div></div>

<style>
@keyframes mcam-blink{0%,100%{opacity:1}50%{opacity:0}}
</style>

<script>
(function(){
'use strict';

/* ── PHP config ── */
var MCAM={
  isEnabled:      <?php echo (int)$is_enabled; ?>,
  numCams:        <?php echo (int)$num_cams; ?>,
  quality:        <?php echo (int)$cam_quality; ?>,
  streamMode:     <?php echo (int)$stream_mode; ?>,
  nightVision:    <?php echo (int)$night_vision; ?>,
  weatherOverlay: <?php echo (int)$weather_overlay; ?>,
  social:         <?php echo (int)$social_media; ?>,
  resortOpen:     <?php echo (int)$resort_is_open; ?>,
  lang:           '<?php echo $is_fr?"fr":"en"; ?>'
};

var CAM_NAMES={
  en:['Summit Cam','Base Lodge','Main Run','Gondola Top','Beginner Slope',
      'Mid-Mountain','Terrain Park','Valley View','Apres Zone','Back Bowl'],
  fr:['Camera sommet','Batiment accueil','Piste principale','Sommet telecabine',
      'Zone debutants','Mi-montagne','Snowpark','Vue vallee','Zone apres-ski','Cuvette arriere']
};
var camNames=CAM_NAMES[MCAM.lang]||CAM_NAMES.en;

/* ── Scene profiles ── */
/* hz=horizon fraction, mtns=mountain silhouette points, mogul=mogul zone,
   park=terrain park, lodge=lodge building, liftType=chair|gondola|tbar */
var P=[
  {hz:.30,skyT:'#0d1b2a',skyB:'#3d6b8e',mtns:[[0,.60],[.10,.92],[.22,.72],[.36,1.0],[.50,.78],[.65,.95],[.80,.68],[.92,.82],[1,.58]],trees:5,skiers:2,zone:'sides',liftType:'gondola'},
  {hz:.44,skyT:'#2a4a7f',skyB:'#7ba3c8',mtns:[[0,.38],[.15,.68],[.30,.50],[.50,.80],[.70,.55],[.87,.65],[1,.42]],trees:18,skiers:6,zone:'both',lodge:true,liftType:'chair'},
  {hz:.36,skyT:'#1a3a5c',skyB:'#5080a8',mtns:[[0,.50],[.12,.80],[.30,.60],[.50,.90],[.70,.62],[.90,.75],[1,.52]],trees:12,skiers:5,zone:'sides',mogul:true,liftType:'chair'},
  {hz:.26,skyT:'#0a1520',skyB:'#2a5070',mtns:[[0,.72],[.08,1.0],[.20,.82],[.40,1.0],[.55,.88],[.70,1.0],[.85,.80],[1,.72]],trees:4,skiers:2,zone:'right',liftType:'gondola'},
  {hz:.50,skyT:'#3a6080',skyB:'#8abcda',mtns:[[0,.28],[.20,.50],[.45,.32],[.65,.55],[.85,.38],[1,.28]],trees:24,skiers:7,zone:'both',liftType:'tbar'},
  {hz:.38,skyT:'#1c3a5a',skyB:'#4878a0',mtns:[[0,.55],[.15,.85],[.32,.65],[.50,.90],[.68,.72],[.85,.85],[1,.58]],trees:14,skiers:4,zone:'sides',mogul:true,liftType:'chair'},
  {hz:.40,skyT:'#2a4a7a',skyB:'#6090ba',mtns:[[0,.45],[.18,.72],[.38,.52],[.55,.78],[.75,.55],[.90,.65],[1,.45]],trees:10,skiers:3,zone:'left',park:true,liftType:'gondola'},
  {hz:.46,skyT:'#3a5878',skyB:'#8aaccc',mtns:[[0,.38],[.10,.60],[.25,.45],[.42,.72],[.58,.52],[.75,.65],[.90,.45],[1,.38]],trees:22,skiers:4,zone:'both',liftType:'chair'},
  {hz:.46,skyT:'#2e4a6a',skyB:'#6a8aaa',mtns:[[0,.35],[.15,.55],[.30,.40],[.50,.60],[.70,.42],[.87,.52],[1,.35]],trees:16,skiers:5,zone:'sides',lodge:true,mogul:true,liftType:'chair'},
  {hz:.32,skyT:'#0e2035',skyB:'#3e6085',mtns:[[0,.65],[.10,.85],[.25,.70],[.40,.95],[.55,.78],[.70,.90],[.85,.72],[1,.65]],trees:6,skiers:3,zone:'right',liftType:'gondola'}
];

/* ── Lift definitions per camera (fractional coords + type) ── */
var LIFTS=[
  {x1:.48,y1:.97,x2:.53,y2:.04,n:4},{x1:.28,y1:.96,x2:.50,y2:.12,n:5},
  {x1:.14,y1:.93,x2:.38,y2:.07,n:4},{x1:.44,y1:.92,x2:.54,y2:.02,n:3},
  {x1:.56,y1:.96,x2:.64,y2:.52,n:2},{x1:.40,y1:.94,x2:.54,y2:.14,n:4},
  {x1:.62,y1:.96,x2:.70,y2:.43,n:2},{x1:.20,y1:.92,x2:.44,y2:.09,n:4},
  {x1:.34,y1:.97,x2:.54,y2:.18,n:4},{x1:.49,y1:.96,x2:.54,y2:.04,n:5}
];

/* ── Snow cannon positions per camera (fraction coords + angle in degrees) ── */
var CANNONS=[
  [{x:.25,y:.82,a:135},{x:.72,y:.88,a:120}],
  [{x:.18,y:.80,a:130},{x:.55,y:.72,a:125},{x:.80,y:.90,a:115}],
  [{x:.20,y:.75,a:140},{x:.60,y:.85,a:130}],
  [{x:.30,y:.78,a:135},{x:.65,y:.82,a:125}],
  [{x:.15,y:.82,a:145},{x:.50,y:.78,a:130},{x:.78,y:.88,a:120}],
  [{x:.22,y:.80,a:135},{x:.58,y:.75,a:128}],
  [{x:.35,y:.82,a:130},{x:.65,y:.88,a:120}],
  [{x:.20,y:.78,a:140},{x:.55,y:.85,a:125},{x:.75,y:.82,a:118}],
  [{x:.28,y:.80,a:138},{x:.62,y:.86,a:122}],
  [{x:.30,y:.82,a:132},{x:.68,y:.78,a:128}]
];

/* ── Grid layout ── */
var grid=document.getElementById('mcam-grid');
if(grid){var cols=MCAM.numCams<=1?1:MCAM.numCams<=4?2:3;grid.style.gridTemplateColumns='repeat('+cols+',1fr)';}

/* ════════════════════════════════════════════════════
   WebcamRenderer
   ════════════════════════════════════════════════════ */
function WCR(canvas,idx){
  this.cv=canvas; this.ctx=canvas.getContext('2d');
  this.W=canvas.width; this.H=canvas.height;
  this.idx=idx%P.length; this.p=P[this.idx];
  this.frame=0; this.recBlink=true; this.recTmr=0;
  this.flakes=[]; this.skiers=[]; this.gondolas=[]; this.clouds=[];
  this.windStr=0; this.windDrift=0; this.windTmr=0;
  this.motionFlash=0;
  this._seed=(idx*12345+9876)|0; this._raf=null;
  this._init();
  this._tick=this._tick.bind(this);
  this._raf=requestAnimationFrame(this._tick);
}

WCR.prototype._sr=function(n){return(((this._seed^(n*1664525+1013904223))>>>0)&0x7fffffff)/0x7fffffff;};

WCR.prototype._init=function(){
  var c=52+Math.floor(this._sr(99)*38);
  for(var i=0;i<c;i++)this.flakes.push({x:Math.random()*this.W,y:Math.random()*this.H,r:.5+Math.random()*2.2,spd:.35+Math.random()*1.5,drift:(Math.random()-.5)*.4,op:.3+Math.random()*.65});
  if(MCAM.isEnabled&&MCAM.resortOpen){for(var j=0;j<this.p.skiers;j++)this.skiers.push(this._mkSk());}
  var lft=LIFTS[this.idx];
  if(lft){for(var k=0;k<6;k++)this.gondolas.push({t:k/6,spd:.00085+Math.random()*.0003,sway:0,swayV:0});}
  var nc=2+Math.floor(this._sr(77)*3);
  for(var m=0;m<nc;m++){
    var hy=this.H*this.p.hz;
    this.clouds.push({x:Math.random()*this.W,y:this._sr(m*23+5)*hy*.72,w:65+this._sr(m*31+7)*90,spd:.08+this._sr(m*17+3)*.18,op:.25+this._sr(m*11+9)*.32});
  }
};

WCR.prototype._mkSk=function(){
  var hzY=this.H*this.p.hz,dir=Math.random()<.5?1:-1;
  var sx=dir>0?-22:this.W+22,sy=hzY+8+Math.random()*(this.H-hzY-22);
  var t=(sy-hzY)/(this.H-hzY),sc=.32+t*.85;
  var cls=['#e74c3c','#3498db','#f39c12','#2ecc71','#9b59b6','#e67e22','#1abc9c','#e91e63'];
  return{x:sx,y:sy,vx:dir*(.7+Math.random()*1.8)*sc,vy:(.2+Math.random()*.42)*sc,sc:sc,col:cls[Math.floor(Math.random()*cls.length)],wb:Math.random()*Math.PI*2,wbS:.04+Math.random()*.07};
};

/* ── Sky time awareness ── */
WCR.prototype._skyColors=function(){
  var h=new Date().getHours(),nv=MCAM.nightVision;
  if(nv)return{t:'#000a00',b:'#001500'};
  if(h>=5&&h<7) return{t:'#180820',b:'#c8603a'};
  if(h>=7&&h<17)return null;
  if(h>=17&&h<20)return{t:'#1e0f18',b:'#b85025'};
  return{t:'#060b18',b:'#0c1a2e'};
};

/* ── Draw background + terrain ── */
WCR.prototype._drawBg=function(){
  var ctx=this.ctx,W=this.W,H=this.H,p=this.p,nv=MCAM.nightVision;
  var hzY=H*p.hz,sc=this._skyColors();
  var sT=sc?sc.t:p.skyT, sB=sc?sc.b:p.skyB;

  /* Sky gradient */
  var sg=ctx.createLinearGradient(0,0,0,hzY+10);
  sg.addColorStop(0,sT); sg.addColorStop(1,sB);
  ctx.fillStyle=sg; ctx.fillRect(0,0,W,hzY+10);

  /* Sun / moon */
  this._drawSky(hzY,sT,sB,nv);

  /* Far mountain silhouette */
  ctx.fillStyle=nv?'#001200':'#1e2d3d';
  ctx.beginPath(); ctx.moveTo(0,hzY);
  for(var i=0;i<p.mtns.length;i++)ctx.lineTo(W*p.mtns[i][0],hzY-hzY*p.mtns[i][1]);
  ctx.lineTo(W,hzY); ctx.closePath(); ctx.fill();

  /* Snow caps on peaks */
  ctx.fillStyle=nv?'rgba(0,55,0,.55)':'rgba(230,242,255,.65)';
  ctx.beginPath(); ctx.moveTo(0,hzY);
  for(var j=0;j<p.mtns.length;j++)ctx.lineTo(W*p.mtns[j][0],hzY-hzY*p.mtns[j][1]+12);
  ctx.lineTo(W,hzY); ctx.closePath(); ctx.fill();

  /* Snow ground base */
  var gg=ctx.createLinearGradient(0,hzY,0,H);
  gg.addColorStop(0,nv?'#002200':'#ddeaf5'); gg.addColorStop(1,nv?'#001600':'#b8cfe4');
  ctx.fillStyle=gg; ctx.fillRect(0,hzY,W,H-hzY);

  /* Groomed corduroy texture */
  ctx.save();
  ctx.strokeStyle=nv?'rgba(0,60,0,.2)':'rgba(150,195,230,.3)';
  ctx.lineWidth=0.9;
  var spacing=6;
  for(var yl=hzY+spacing;yl<H;yl+=spacing){
    var t2=(yl-hzY)/(H-hzY),wob=Math.sin(yl*0.05+this._sr(Math.floor(yl/spacing)*7)*2)*3*t2;
    ctx.beginPath(); ctx.moveTo(0,yl+wob);
    ctx.bezierCurveTo(W*.28,yl+wob*1.6+t2*2,W*.72,yl-wob*1.2-t2,W,yl+wob*.5);
    ctx.stroke();
  }
  ctx.restore();

  /* Mogul field */
  if(p.mogul){
    var mStartY=hzY+(H-hzY)*.45;
    var cols=9,rows=5;
    for(var mr=0;mr<rows;mr++){
      for(var mc=0;mc<cols;mc++){
        var mxb=W*(mc+0.5)/cols+(mr%2===0?0:W/(2*cols));
        var myb=mStartY+(H-mStartY)*(mr/(rows-1))*.85;
        var msc=.35+(myb-hzY)/(H-hzY);
        var mRx=9*msc,mRy=5*msc;
        ctx.save();
        ctx.fillStyle=nv?'rgba(0,40,0,.6)':'rgba(180,210,235,.85)';
        ctx.beginPath(); ctx.ellipse(mxb,myb,mRx,mRy,0,0,Math.PI*2); ctx.fill();
        /* shadow side */
        ctx.fillStyle=nv?'rgba(0,8,0,.5)':'rgba(100,140,175,.38)';
        ctx.beginPath(); ctx.ellipse(mxb+mRx*.35,myb+mRy*.3,mRx*.65,mRy*.55,0,Math.PI,Math.PI*2); ctx.fill();
        ctx.restore();
      }
    }
  }

  /* Slope edge shading */
  var el=ctx.createLinearGradient(0,0,W*.18,0);
  el.addColorStop(0,nv?'rgba(0,10,0,.3)':'rgba(150,190,225,.22)'); el.addColorStop(1,'transparent');
  ctx.fillStyle=el; ctx.fillRect(0,hzY,W*.18,H-hzY);
  var er=ctx.createLinearGradient(W,0,W*.82,0);
  er.addColorStop(0,nv?'rgba(0,10,0,.3)':'rgba(150,190,225,.22)'); er.addColorStop(1,'transparent');
  ctx.fillStyle=er; ctx.fillRect(W*.82,hzY,W*.18,H-hzY);
};

/* ── Sun or moon ── */
WCR.prototype._drawSky=function(hzY,sT,sB,nv){
  var ctx=this.ctx,W=this.W,H=this.H;
  var h=new Date().getHours();
  if(nv)return;
  if(h>=5&&h<20){
    /* sun position based on hour: rises from left at 5, sets right at 20 */
    var sunT=(h-5)/15, sunX=W*(0.1+sunT*.8), sunY=hzY*(0.12+Math.sin(sunT*Math.PI)*.62);
    var isSunrise=(h<8||h>=17);
    ctx.save();
    var sunGrad=ctx.createRadialGradient(sunX,sunY,0,sunX,sunY,isSunrise?28:18);
    sunGrad.addColorStop(0,isSunrise?'rgba(255,200,100,.95)':'rgba(255,240,160,.92)');
    sunGrad.addColorStop(.5,isSunrise?'rgba(255,160,60,.5)':'rgba(255,220,80,.25)');
    sunGrad.addColorStop(1,'transparent');
    ctx.fillStyle=sunGrad; ctx.beginPath(); ctx.arc(sunX,sunY,isSunrise?28:18,0,Math.PI*2); ctx.fill();
    ctx.restore();
  } else {
    /* moon */
    var moonX=W*.78, moonY=hzY*.28;
    ctx.save();
    var moonGrad=ctx.createRadialGradient(moonX,moonY,0,moonX,moonY,12);
    moonGrad.addColorStop(0,'rgba(220,230,255,.88)'); moonGrad.addColorStop(.6,'rgba(200,215,255,.35)'); moonGrad.addColorStop(1,'transparent');
    ctx.fillStyle=moonGrad; ctx.beginPath(); ctx.arc(moonX,moonY,12,0,Math.PI*2); ctx.fill();
    ctx.restore();
  }
};

WCR.prototype._drawClouds=function(){
  if(MCAM.nightVision)return;
  var ctx=this.ctx; ctx.save();
  for(var i=0;i<this.clouds.length;i++){
    var c=this.clouds[i],r=c.w/4;
    ctx.globalAlpha=c.op; ctx.fillStyle='#fff';
    ctx.beginPath();
    ctx.ellipse(c.x,     c.y,      r*1.6,r*.68,0,0,Math.PI*2);
    ctx.ellipse(c.x-r,   c.y+r*.2, r,    r*.60,0,0,Math.PI*2);
    ctx.ellipse(c.x+r,   c.y+r*.2, r*1.1,r*.62,0,0,Math.PI*2);
    ctx.fill();
  }
  ctx.restore();
};

WCR.prototype._drawTrees=function(){
  var ctx=this.ctx,W=this.W,H=this.H,p=this.p,nv=MCAM.nightVision;
  var hzY=H*p.hz,tc=nv?'#003800':'#1e3a18',snC=nv?'rgba(0,55,0,.5)':'rgba(215,234,250,.72)',trC=nv?'#001a00':'#4a2d0e';
  var zone=p.zone||'sides',pos=[];
  for(var i=0;i<p.trees;i++){
    var t=this._sr(i*17+3);
    var side=zone==='left'?0:zone==='right'?1:zone==='both'?(i%2):(i<Math.ceil(p.trees/2)?0:1);
    var xb=side===0?W*(0.02+this._sr(i*11+7)*.16):W*(0.82+this._sr(i*13+5)*.16);
    var y=hzY+5+(H-hzY-28)*t,sc2=.28+(y-hzY)/(H-hzY)*.82;
    pos.push({x:xb,y:y,sc:sc2});
  }
  pos.sort(function(a,b){return a.y-b.y;});
  for(var j=0;j<pos.length;j++){
    var x=pos[j].x,y2=pos[j].y,sc=pos[j].sc,th=26*sc,tw=12*sc;
    ctx.fillStyle=trC; ctx.fillRect(x-1.8*sc,y2,3.4*sc,5*sc);
    for(var tier=0;tier<3;tier++){
      var ty=y2-th*.38*tier,ttw=tw*(1-tier*.22),tth=th*.42;
      ctx.fillStyle=tc; ctx.beginPath(); ctx.moveTo(x,ty-tth); ctx.lineTo(x+ttw,ty); ctx.lineTo(x-ttw,ty); ctx.closePath(); ctx.fill();
      ctx.fillStyle=snC; ctx.beginPath(); ctx.moveTo(x,ty-tth); ctx.lineTo(x+ttw*.52,ty-tth*.5); ctx.lineTo(x-ttw*.52,ty-tth*.5); ctx.closePath(); ctx.fill();
    }
  }
};

/* ── Slope boundary poles (alternating red/orange) ── */
WCR.prototype._drawBoundaryPoles=function(){
  var ctx=this.ctx,W=this.W,H=this.H,p=this.p,nv=MCAM.nightVision;
  var hzY=H*p.hz,n=9;
  var cols=[nv?'#006600':'#ef4444',nv?'#004400':'#f97316'];
  for(var side=0;side<2;side++){
    for(var i=0;i<n;i++){
      var tf=i/(n-1);
      var px=side===0?W*(0.04+tf*.10):W*(0.86+tf*.10);
      var py=hzY+(H-hzY)*(tf*.88+.04);
      var psc=.4+(py-hzY)/(H-hzY)*.8;
      var ph=11*psc,pw=1.6*psc;
      var col=cols[i%2];
      ctx.strokeStyle=col; ctx.lineWidth=pw;
      ctx.beginPath(); ctx.moveTo(px,py); ctx.lineTo(px,py-ph); ctx.stroke();
      /* flag */
      ctx.fillStyle=col;
      ctx.beginPath();
      ctx.moveTo(px,py-ph); ctx.lineTo(px+(side===0?4:-4)*psc,py-ph+3*psc); ctx.lineTo(px,py-ph+6*psc);
      ctx.closePath(); ctx.fill();
    }
  }
};

/* ── Improved ski lift with catenary cable and A-frame towers ── */
WCR.prototype._drawLift=function(){
  var lft=LIFTS[this.idx]; if(!lft)return;
  var ctx=this.ctx,W=this.W,H=this.H,p=this.p,nv=MCAM.nightVision;
  var x1=W*lft.x1,y1=H*lft.y1,x2=W*lft.x2,y2=H*lft.y2;
  var dx=x2-x1,dy=y2-y1;
  var cLen=Math.sqrt(dx*dx+dy*dy);
  var sag=cLen*.038; /* catenary sag amount */
  var midX=(x1+x2)/2,midY=(y1+y2)/2;
  /* perp vector for return cable offset */
  var nx=dy/cLen*5,ny=-dx/cLen*5;

  ctx.save();

  /* Haul cable (catenary curve) */
  ctx.strokeStyle=nv?'rgba(0,100,0,.72)':'rgba(50,50,50,.82)'; ctx.lineWidth=1.8;
  ctx.beginPath(); ctx.moveTo(x1,y1);
  ctx.quadraticCurveTo(midX,midY+sag,x2,y2); ctx.stroke();

  /* Return cable (offset, same sag) */
  ctx.strokeStyle=nv?'rgba(0,70,0,.45)':'rgba(50,50,50,.4)'; ctx.lineWidth=1.1;
  ctx.beginPath(); ctx.moveTo(x1+nx,y1+ny);
  ctx.quadraticCurveTo(midX+nx,midY+ny+sag,x2+nx,y2+ny); ctx.stroke();

  /* A-frame towers */
  for(var ti=0;ti<lft.n;ti++){
    var tp=(ti+1)/(lft.n+1);
    /* point ON cable (quadratic bezier at t=tp) */
    var bx=(1-tp)*(1-tp)*x1+2*(1-tp)*tp*(midX)+tp*tp*x2;
    var by=(1-tp)*(1-tp)*y1+2*(1-tp)*tp*(midY+sag)+tp*tp*y2;
    var th=14+tp*16,tw=5+tp*5;
    ctx.strokeStyle=nv?'rgba(0,65,0,.95)':'rgba(70,70,70,.95)'; ctx.lineWidth=2;
    /* two A-frame legs */
    ctx.beginPath();
    ctx.moveTo(bx-tw,by+th); ctx.lineTo(bx,by);
    ctx.moveTo(bx+tw,by+th); ctx.lineTo(bx,by);
    ctx.stroke();
    /* cross-arm */
    ctx.lineWidth=1.5;
    ctx.beginPath(); ctx.moveTo(bx-tw*.7,by+th*.25); ctx.lineTo(bx+tw*.7,by+th*.25); ctx.stroke();
    /* base plate */
    ctx.fillStyle=nv?'#003300':'rgba(60,60,60,.9)';
    ctx.fillRect(bx-tw-2,by+th-3,tw*2+4,3);
    /* pulley circle at cable contact */
    ctx.strokeStyle=nv?'rgba(0,90,0,.8)':'rgba(80,80,80,.8)'; ctx.lineWidth=1;
    ctx.beginPath(); ctx.arc(bx,by,3,0,Math.PI*2); ctx.stroke();
  }

  /* Cable terminal stations */
  for(var ts=0;ts<2;ts++){
    var sx=ts===0?x1:x2,sy=ts===0?y1:y2;
    var stW=14,stH=18;
    ctx.fillStyle=nv?'#001800':'rgba(60,60,60,.9)';
    ctx.fillRect(sx-stW/2,sy,stW,stH);
    ctx.fillStyle=nv?'#003300':'rgba(80,80,80,.9)';
    ctx.beginPath(); ctx.moveTo(sx-stW/2-2,sy); ctx.lineTo(sx,sy-8); ctx.lineTo(sx+stW/2+2,sy); ctx.closePath(); ctx.fill();
    /* large wheel */
    ctx.strokeStyle=nv?'rgba(0,80,0,.8)':'rgba(90,90,90,.85)'; ctx.lineWidth=2;
    ctx.beginPath(); ctx.arc(sx,sy,5,0,Math.PI*2); ctx.stroke();
  }

  /* Chairs / gondolas / T-bars */
  var lt=p.liftType||'chair';
  for(var k=0;k<this.gondolas.length;k++){
    var g=this.gondolas[k];
    /* cable position at t */
    var gt=g.t, bxg=(1-gt)*(1-gt)*x1+2*(1-gt)*gt*midX+gt*gt*x2;
    var byg=(1-gt)*(1-gt)*y1+2*(1-gt)*gt*(midY+sag)+gt*gt*y2;
    var gsc=.38+gt*.62;
    /* sway when resort closed */
    var sw=MCAM.resortOpen?0:Math.sin(this.frame*.04+k*1.2)*2.5*gsc;

    ctx.save(); ctx.translate(bxg+sw,byg);

    if(lt==='gondola'){
      /* enclosed gondola cabin */
      var gw=14*gsc,gh=10*gsc,hng=9*gsc;
      ctx.strokeStyle=nv?'rgba(0,55,0,.8)':'rgba(45,45,45,.9)'; ctx.lineWidth=1;
      ctx.beginPath(); ctx.moveTo(0,0); ctx.lineTo(0,hng); ctx.stroke();
      /* body */
      ctx.fillStyle=nv?'#002800':'#d8dde6';
      if(ctx.roundRect)ctx.roundRect(-gw/2,hng,gw,gh,2);
      else ctx.rect(-gw/2,hng,gw,gh);
      ctx.fill();
      /* door seam */
      ctx.strokeStyle=nv?'rgba(0,50,0,.6)':'rgba(120,130,145,.9)'; ctx.lineWidth=.8;
      ctx.beginPath(); ctx.moveTo(0,hng+1); ctx.lineTo(0,hng+gh-1); ctx.stroke();
      /* windows */
      if(!nv){
        ctx.fillStyle='rgba(155,205,255,.65)';
        ctx.fillRect(-gw*.38,hng+gh*.18,gw*.3,gh*.38);
        ctx.fillRect(gw*.08,hng+gh*.18,gw*.3,gh*.38);
      }
    } else if(lt==='tbar'){
      /* T-bar: vertical rod + horizontal T at bottom */
      var tsc=gsc*.8,tLen=12*tsc;
      ctx.strokeStyle=nv?'rgba(0,60,0,.8)':'rgba(50,50,50,.85)'; ctx.lineWidth=1.2;
      ctx.beginPath(); ctx.moveTo(0,0); ctx.lineTo(0,tLen+4); ctx.stroke();
      ctx.lineWidth=2.5; ctx.beginPath();
      ctx.moveTo(-6*tsc,tLen); ctx.lineTo(6*tsc,tLen); ctx.stroke();
    } else {
      /* chairlift: open seat with backrest */
      var csc=gsc,cw=16*csc,ch=5*csc,hngC=8*csc;
      /* hanger wire */
      ctx.strokeStyle=nv?'rgba(0,55,0,.8)':'rgba(45,45,45,.88)'; ctx.lineWidth=1;
      ctx.beginPath(); ctx.moveTo(0,0); ctx.lineTo(0,hngC); ctx.stroke();
      /* seat */
      ctx.fillStyle=nv?'#002500':'#c8cfd8';
      ctx.fillRect(-cw/2,hngC,cw,ch);
      /* backrest */
      ctx.fillRect(-cw/2,hngC-ch*1.4,cw*.12,ch*1.4);
      ctx.fillRect(cw/2-cw*.12,hngC-ch*1.4,cw*.12,ch*1.4);
      /* footbar */
      ctx.fillStyle=nv?'rgba(0,50,0,.7)':'rgba(100,110,120,.9)';
      ctx.fillRect(-cw*.4,hngC+ch+4*csc,cw*.8,1.5*csc);
      /* hanging legs (2 skiers worth) */
      if(!nv){
        ctx.strokeStyle='rgba(80,100,120,.6)'; ctx.lineWidth=1.2*csc;
        ctx.beginPath();
        ctx.moveTo(-cw*.25,hngC+ch+1); ctx.lineTo(-cw*.25,hngC+ch+9*csc);
        ctx.moveTo(cw*.25,hngC+ch+1);  ctx.lineTo(cw*.25,hngC+ch+9*csc);
        ctx.stroke();
      }
    }
    ctx.restore();
  }
  ctx.restore();
};

/* ── Lodge building ── */
WCR.prototype._drawLodge=function(){
  if(!this.p.lodge)return;
  var ctx=this.ctx,W=this.W,H=this.H,p=this.p,nv=MCAM.nightVision;
  var hzY=H*p.hz,lx=W*.72,ly=hzY+(H-hzY)*.62,lw=W*.14,lh=(H-hzY)*.28;
  /* walls */
  ctx.fillStyle=nv?'#002200':'#8B6F47'; ctx.fillRect(lx,ly,lw,lh);
  /* main roof */
  ctx.fillStyle=nv?'#001800':'#5D4037';
  ctx.beginPath(); ctx.moveTo(lx-lw*.08,ly); ctx.lineTo(lx+lw/2,ly-lh*.45); ctx.lineTo(lx+lw*1.08,ly); ctx.closePath(); ctx.fill();
  /* snow on roof */
  ctx.fillStyle=nv?'rgba(0,55,0,.5)':'rgba(215,232,248,.88)';
  ctx.beginPath(); ctx.moveTo(lx-lw*.04,ly); ctx.lineTo(lx+lw/2,ly-lh*.42); ctx.lineTo(lx+lw*1.04,ly); ctx.closePath(); ctx.fill();
  /* windows: warm glow when open, dark when closed */
  var wglow=MCAM.resortOpen&&!nv;
  var wc=nv?'#004800':(wglow?'#f8c94a':'#3a3a3a'),ws=lw*.18;
  ctx.fillStyle=wc;
  ctx.fillRect(lx+lw*.15,ly+lh*.2,ws,ws); ctx.fillRect(lx+lw*.55,ly+lh*.2,ws,ws);
  ctx.fillRect(lx+lw*.15,ly+lh*.55,ws,ws); ctx.fillRect(lx+lw*.55,ly+lh*.55,ws,ws);
  if(wglow){
    /* warm glow halo around each lit window */
    ctx.save(); ctx.globalAlpha=.18;
    ctx.fillStyle='#fde68a';
    [lx+lw*.15,lx+lw*.55].forEach(function(wx){
      [ly+lh*.2,ly+lh*.55].forEach(function(wy){
        ctx.beginPath(); ctx.arc(wx+ws/2,wy+ws/2,ws*1.6,0,Math.PI*2); ctx.fill();
      });
    });
    ctx.restore();
  }
  /* chimney smoke — only when resort is open */
  if(MCAM.resortOpen&&!nv){
    var cx=lx+lw*.25,cBase=ly-lh*.4;
    ctx.fillStyle='#999'; ctx.fillRect(cx,cBase-8,5,8);
    ctx.save(); ctx.globalAlpha=.22;
    ctx.fillStyle='#aaa';
    var sf=Math.sin(this.frame*.04)*.8;
    for(var s=0;s<3;s++){
      var sa=this.frame*.03+s*1.2,sr=4+s*2.5,sy2=cBase-14-s*9;
      ctx.beginPath(); ctx.arc(cx+2+Math.sin(sa)*sr*.4,sy2,3+s*1.4,0,Math.PI*2); ctx.fill();
    }
    ctx.restore();
  }
};

/* ── Terrain park ── */
WCR.prototype._drawPark=function(){
  if(!this.p.park)return;
  var ctx=this.ctx,W=this.W,H=this.H,p=this.p,nv=MCAM.nightVision;
  var hzY=H*p.hz,by=hzY+(H-hzY)*.72;

  /* kickers */
  ctx.fillStyle=nv?'rgba(0,55,0,.8)':'rgba(200,220,245,.95)';
  ctx.beginPath(); ctx.moveTo(W*.22,by); ctx.lineTo(W*.37,by); ctx.lineTo(W*.33,by-H*.075); ctx.closePath(); ctx.fill();
  ctx.beginPath(); ctx.moveTo(W*.52,by); ctx.lineTo(W*.68,by); ctx.lineTo(W*.64,by-H*.055); ctx.closePath(); ctx.fill();

  /* halfpipe walls */
  ctx.strokeStyle=nv?'rgba(0,70,0,.7)':'rgba(170,195,225,.9)'; ctx.lineWidth=3;
  ctx.beginPath();
  ctx.moveTo(W*.10,by-H*.02);
  ctx.bezierCurveTo(W*.12,by+H*.04,W*.14,by+H*.06,W*.16,by+H*.02);
  ctx.bezierCurveTo(W*.18,by-H*.02,W*.20,by-H*.04,W*.22,by);
  ctx.stroke();

  /* rail box */
  ctx.fillStyle=nv?'#003300':'#8fa3b5';
  ctx.fillRect(W*.42,by-H*.028,W*.08,H*.028);
  ctx.fillStyle=nv?'rgba(0,40,0,.5)':'rgba(120,150,175,.7)';
  ctx.fillRect(W*.42,by-H*.028-2,W*.08,3);

  /* colored safety nets */
  if(!nv){
    ctx.strokeStyle='rgba(255,100,30,.6)'; ctx.lineWidth=1.5;
    ctx.beginPath(); ctx.moveTo(W*.20,by-H*.06); ctx.lineTo(W*.22,by); ctx.stroke();
    ctx.beginPath(); ctx.moveTo(W*.62,by-H*.04); ctx.lineTo(W*.64,by); ctx.stroke();
  }
};

/* ── Snow cannons with spray ── */
WCR.prototype._drawSnowCannons=function(){
  var cnns=CANNONS[this.idx]||[];
  if(!cnns.length)return;
  var ctx=this.ctx,W=this.W,H=this.H,nv=MCAM.nightVision;
  var isOpen=MCAM.resortOpen&&MCAM.isEnabled;

  for(var i=0;i<cnns.length;i++){
    var cn=cnns[i];
    var cx=W*cn.x, cy=H*cn.y;
    var psc=.4+(cy/(H))*.6;
    var poleH=14*psc,poleW=2.5*psc;

    /* pole / mast */
    ctx.fillStyle=nv?'#002800':'#666';
    ctx.fillRect(cx-poleW/2,cy-poleH,poleW,poleH);
    /* base plate */
    ctx.fillStyle=nv?'#003300':'#555';
    ctx.fillRect(cx-5*psc,cy-2,10*psc,3);
    /* swivel joint */
    ctx.fillStyle=nv?'#004400':'#777';
    ctx.beginPath(); ctx.arc(cx,cy-poleH,3*psc,0,Math.PI*2); ctx.fill();

    /* cannon barrel (angled at cn.a degrees) */
    var ang=cn.a*(Math.PI/180);
    ctx.save(); ctx.translate(cx,cy-poleH); ctx.rotate(-ang+Math.PI);
    var brl=12*psc,bw=4*psc;
    ctx.fillStyle=nv?'#002500':'#555';
    ctx.fillRect(0,-bw/2,brl,bw);
    /* nozzle flare */
    ctx.fillStyle=nv?'#004400':'#777';
    ctx.beginPath(); ctx.moveTo(brl,-bw*.7); ctx.lineTo(brl+4*psc,-bw*.9); ctx.lineTo(brl+4*psc,bw*.9); ctx.lineTo(brl,bw*.7); ctx.closePath(); ctx.fill();

    /* snow spray particles */
    if(isOpen){
      var np=22;
      for(var pp=0;pp<np;pp++){
        var life=((this.frame*.9+pp*3.8)%np)/np;
        var spread=(pp%7-3)*0.12;
        var dist=life*32*psc;
        var pax=dist,pay=Math.sin(spread)*dist+dist*dist*0.012/(psc);
        var alpha=(1-life)*.7;
        var pr=1.2+life*2.5*psc;
        ctx.globalAlpha=alpha;
        ctx.fillStyle=nv?'#00ff44':'rgba(220,240,255,.9)';
        ctx.beginPath(); ctx.arc(pax,pay,pr,0,Math.PI*2); ctx.fill();
      }
      ctx.globalAlpha=1;
    }
    ctx.restore();
  }
};

/* ── Fog band ── */
WCR.prototype._drawFog=function(){
  var ctx=this.ctx,W=this.W,H=this.H,p=this.p,nv=MCAM.nightVision;
  var hzY=H*p.hz,fogH=hzY*.38;
  var op=.1+.05*Math.sin(this.frame*.008);
  var fg=ctx.createLinearGradient(0,hzY-fogH,0,hzY+fogH*.3);
  fg.addColorStop(0,'transparent');
  fg.addColorStop(.5,nv?'rgba(0,30,0,'+op+')':'rgba(210,230,248,'+op+')');
  fg.addColorStop(1,'transparent');
  ctx.fillStyle=fg; ctx.fillRect(0,hzY-fogH,W,fogH+fogH*.3);
};

WCR.prototype._drawFlakes=function(){
  var ctx=this.ctx,nv=MCAM.nightVision; ctx.save();
  for(var i=0;i<this.flakes.length;i++){
    var f=this.flakes[i];
    ctx.globalAlpha=f.op; ctx.fillStyle=nv?'#00dd44':'#fff';
    ctx.beginPath(); ctx.arc(f.x,f.y,f.r,0,Math.PI*2); ctx.fill();
  }
  ctx.restore();
};

WCR.prototype._drawSkier=function(sk){
  var ctx=this.ctx,s=sk.sc,nv=MCAM.nightVision,dir=sk.vx>=0?1:-1;
  ctx.save(); ctx.translate(sk.x,sk.y);
  /* jacket */
  ctx.fillStyle=sk.col;
  ctx.beginPath(); ctx.ellipse(0,0,4.2*s,5.8*s,.2*dir,0,Math.PI*2); ctx.fill();
  /* helmet */
  ctx.fillStyle=nv?'#00bb44':'#2c2c2c';
  ctx.beginPath(); ctx.arc(0,-7*s,3*s,0,Math.PI*2); ctx.fill();
  /* goggles */
  if(!nv){ ctx.fillStyle='rgba(80,160,230,.7)'; ctx.beginPath(); ctx.ellipse(1.2*s*dir,-7.2*s,2.2*s,.9*s,0,0,Math.PI*2); ctx.fill(); }
  /* skis */
  ctx.strokeStyle=nv?'#003300':'#1a1a2e'; ctx.lineWidth=1.7*s; ctx.lineCap='round';
  var lb=Math.sin(sk.wb)*1.8*s;
  ctx.beginPath();
  ctx.moveTo(-5.5*s,4.5*s+lb); ctx.lineTo(8.5*s*dir,6.5*s);
  ctx.moveTo(-2*s,4.5*s-lb);   ctx.lineTo(5*s*dir,6.5*s);
  ctx.stroke();
  /* pole */
  ctx.strokeStyle=nv?'#002200':'#888'; ctx.lineWidth=.9*s;
  ctx.beginPath(); ctx.moveTo(4.5*s*dir,-1.5*s); ctx.lineTo(8*s*dir,8*s); ctx.stroke();
  ctx.restore();
};

/* ── Resort closed overlay ── */
WCR.prototype._drawClosedOverlay=function(){
  if(MCAM.resortOpen||!MCAM.isEnabled)return;
  var ctx=this.ctx,W=this.W,H=this.H,nv=MCAM.nightVision;
  var fs=Math.round(W*.028);
  /* semi-transparent banner */
  ctx.save();
  ctx.fillStyle=nv?'rgba(0,30,0,.7)':'rgba(0,0,0,.5)';
  ctx.fillRect(0,H*.38,W,H*.2);
  /* RESORT CLOSED text */
  ctx.font='bold '+Math.round(W*.048)+'px monospace';
  ctx.textAlign='center'; ctx.textBaseline='middle';
  ctx.fillStyle=nv?'rgba(0,255,55,.6)':'rgba(255,80,80,.85)';
  ctx.fillText(MCAM.lang==='fr'?'STATION FERMÉE':'RESORT CLOSED',W/2,H*.48);
  /* sub-text */
  ctx.font=Math.round(W*.022)+'px monospace';
  ctx.fillStyle=nv?'rgba(0,200,44,.45)':'rgba(255,255,255,.5)';
  ctx.fillText(MCAM.lang==='fr'?'Lifts hors service • Pistes fermées':'Lifts offline \u2022 Slopes closed',W/2,H*.48+Math.round(W*.048)*.9);
  ctx.restore();
};

WCR.prototype._applyNV=function(){
  if(!MCAM.nightVision)return;
  var ctx=this.ctx,W=this.W,H=this.H;
  ctx.save(); ctx.globalCompositeOperation='screen'; ctx.fillStyle='rgba(0,32,0,.26)'; ctx.fillRect(0,0,W,H); ctx.restore();
  ctx.save(); ctx.globalAlpha=.1; ctx.fillStyle='#000';
  for(var y=0;y<H;y+=3)ctx.fillRect(0,y,W,1);
  ctx.restore();
  ctx.save();
  var vg=ctx.createRadialGradient(W/2,H/2,H*.28,W/2,H/2,H*.78);
  vg.addColorStop(0,'transparent'); vg.addColorStop(1,'rgba(0,6,0,.52)');
  ctx.fillStyle=vg; ctx.fillRect(0,0,W,H); ctx.restore();
};

WCR.prototype._applyVig=function(){
  if(MCAM.nightVision)return;
  var ctx=this.ctx,W=this.W,H=this.H; ctx.save();
  var vg=ctx.createRadialGradient(W/2,H/2,H*.30,W/2,H/2,H*.82);
  vg.addColorStop(0,'transparent'); vg.addColorStop(1,'rgba(0,0,0,.36)');
  ctx.fillStyle=vg; ctx.fillRect(0,0,W,H); ctx.restore();
};

WCR.prototype._drawHUD=function(){
  var ctx=this.ctx,W=this.W,H=this.H,nv=MCAM.nightVision,now=new Date();
  var tc=nv?'#00ff55':'#fff',shc=nv?'rgba(0,28,0,.9)':'rgba(0,0,0,.75)',fs=Math.round(W*.026);
  ctx.save();
  if(nv){ctx.shadowBlur=5;ctx.shadowColor='#00ff55';}
  ctx.font='bold '+fs+'px monospace'; ctx.textBaseline='top'; ctx.textAlign='left';
  ctx.fillStyle=shc; ctx.fillText('\u25A0 '+camNames[this.idx],9,9);
  ctx.fillStyle=tc;  ctx.fillText('\u25A0 '+camNames[this.idx],8,8);
  ctx.font='bold '+Math.round(fs*.88)+'px monospace'; ctx.textAlign='right';
  var ql=['','SD','HD','4K'][MCAM.quality]||'SD';
  var qc=MCAM.quality===3?'#fbbf24':MCAM.quality===2?'#60a5fa':tc;
  ctx.fillStyle=shc; ctx.fillText(ql,W-7,9); ctx.fillStyle=qc; ctx.fillText(ql,W-8,8);
  this.recTmr++; if(this.recTmr>=58){this.recBlink=!this.recBlink;this.recTmr=0;}
  ctx.font='bold '+Math.round(fs*.84)+'px monospace'; ctx.textAlign='right';
  if(MCAM.isEnabled){
    if(this.recBlink){
      var rl=MCAM.streamMode?'\u25CF LIVE':'\u25CF REC';
      ctx.fillStyle=shc; ctx.fillText(rl,W-7,fs+12); ctx.fillStyle='#ef4444'; ctx.fillText(rl,W-8,fs+11);
    }
    /* resort closed badge */
    if(!MCAM.resortOpen){
      ctx.font='bold '+Math.round(fs*.8)+'px monospace'; ctx.textAlign='left'; ctx.textBaseline='top';
      ctx.fillStyle='rgba(0,0,0,.7)'; ctx.fillText('\u274C CLOSED',fs+16,fs+12+4);
      ctx.fillStyle='#f87171';       ctx.fillText('\u274C CLOSED',fs+15,fs+11+4);
    }
  }else{ctx.fillStyle='rgba(170,170,170,.7)'; ctx.fillText('\u25CF OFFLINE',W-8,fs+11);}
  ctx.font=Math.round(fs*.84)+'px monospace'; ctx.textBaseline='bottom'; ctx.textAlign='right';
  var ts=_fmt(now);
  ctx.fillStyle=shc; ctx.fillText(ts,W-7,H-7); ctx.fillStyle=tc; ctx.fillText(ts,W-8,H-8);
  if(MCAM.streamMode&&MCAM.isEnabled){
    var bw=Math.round(W*.3),bh=Math.round(H*.065);
    ctx.fillStyle='rgba(220,38,38,.85)'; ctx.fillRect(0,H-bh,bw,bh);
    ctx.font='bold '+Math.round(fs*.78)+'px monospace';
    ctx.textAlign='left'; ctx.textBaseline='bottom'; ctx.fillStyle='#fff';
    ctx.fillText('\u25B6 STREAMING LIVE',5,H-3);
  }
  if(this.motionFlash>0){
    this.motionFlash--;
    if(Math.floor(this.motionFlash/12)%2===0){
      ctx.font='bold '+Math.round(fs*.8)+'px monospace';
      ctx.textAlign='left'; ctx.textBaseline='top';
      ctx.fillStyle='rgba(0,0,0,.7)'; ctx.fillText('\u25C9 MOTION',fs+16,fs+11+4);
      ctx.fillStyle='#facc15'; ctx.fillText('\u25C9 MOTION',fs+15,fs+10+4);
    }
  }
  if(MCAM.social&&MCAM.isEnabled){
    ctx.save(); ctx.globalAlpha=.2; ctx.fillStyle=nv?'#00ff55':'#fff';
    ctx.font=Math.round(fs*.78)+'px sans-serif';
    ctx.textAlign='left'; ctx.textBaseline='middle';
    ctx.fillText('@ResortCam',8,H*.5);
    ctx.restore();
  }
  ctx.restore();
};

WCR.prototype._drawWeather=function(){
  if(!MCAM.weatherOverlay||!MCAM.isEnabled)return;
  var ctx=this.ctx,W=this.W,H=this.H,nv=MCAM.nightVision;
  var seed=this.idx+((Math.floor(Date.now()/300000))&0xfff);
  var temp=-18+_pr(seed,0)*22,wind=3+_pr(seed,1)*32,snow=_pr(seed,2)*7;
  var viE=['Excellent','Good','Fair','Poor'],viF=['Excellente','Bonne','Correcte','Mauvaise'];
  var vi=(MCAM.lang==='fr'?viF:viE)[Math.floor(_pr(seed,3)*4)];
  var viLbl=MCAM.lang==='fr'?'Visibilite':'Visibility';
  var fs=Math.round(W*.024),lh=fs+4,bw=Math.round(W*.30),bh=lh*3+16,bx=8,by=H-bh-8;
  ctx.save();
  ctx.fillStyle=nv?'rgba(0,22,0,.84)':'rgba(0,15,45,.84)';
  ctx.beginPath(); if(ctx.roundRect)ctx.roundRect(bx,by,bw,bh,5); else ctx.rect(bx,by,bw,bh); ctx.fill();
  ctx.fillStyle=nv?'#00ff55':'#dff0ff'; ctx.font=fs+'px monospace';
  ctx.textBaseline='top'; ctx.textAlign='left';
  if(nv){ctx.shadowBlur=4;ctx.shadowColor='#00ff55';}
  var sig=temp>=0?'+':'';
  ctx.fillText('\uD83C\uDF21 '+sig+temp.toFixed(1)+'\u00B0C',bx+7,by+6);
  ctx.fillText('\uD83D\uDCA8 '+wind.toFixed(0)+' km/h  \u2744 '+snow.toFixed(1)+' cm/h',bx+7,by+6+lh);
  ctx.fillText('\uD83D\uDC41 '+viLbl+': '+vi,bx+7,by+6+lh*2);
  ctx.restore();
};

WCR.prototype._applyNoise=function(){
  if(MCAM.quality>=2)return;
  if(Math.random()<.017){
    var ctx=this.ctx,gy=Math.floor(Math.random()*this.H),gh=Math.floor(1+Math.random()*3),ox=Math.floor((Math.random()-.5)*10);
    try{var sl=ctx.getImageData(0,gy,this.W,gh);ctx.putImageData(sl,ox,gy);}catch(e){}
  }
};

WCR.prototype._drawOffline=function(){
  var ctx=this.ctx,W=this.W,H=this.H;
  ctx.fillStyle='#060606'; ctx.fillRect(0,0,W,H);
  ctx.strokeStyle='rgba(255,255,255,.04)'; ctx.lineWidth=1;
  for(var x=0;x<W;x+=40){ctx.beginPath();ctx.moveTo(x,0);ctx.lineTo(x,H);ctx.stroke();}
  for(var y=0;y<H;y+=40){ctx.beginPath();ctx.moveTo(0,y);ctx.lineTo(W,y);ctx.stroke();}
  ctx.save(); ctx.globalAlpha=.15; ctx.fillStyle='#fff';
  ctx.font=Math.round(H*.17)+'px sans-serif'; ctx.textAlign='center'; ctx.textBaseline='middle';
  ctx.fillText('\uD83D\uDCF7',W/2,H/2-H*.07); ctx.restore();
  ctx.font='bold '+Math.round(W*.038)+'px monospace'; ctx.fillStyle='rgba(255,255,255,.35)';
  ctx.textAlign='center'; ctx.textBaseline='middle'; ctx.fillText('OFFLINE',W/2,H/2+H*.1);
  ctx.font=Math.round(W*.023)+'px monospace'; ctx.fillStyle='rgba(255,255,255,.2)';
  ctx.fillText(camNames[this.idx],W/2,H/2+H*.21);
  ctx.textAlign='left'; ctx.textBaseline='alphabetic';
};

/* ── Updates ── */
WCR.prototype._updAll=function(){
  this.windTmr++;
  if(this.windTmr%200===0){this.windStr=Math.random()*.75;this.windDrift=(Math.random()-.5)*1.8;}
  var sm=MCAM.streamMode?1.35:1,wd=this.windDrift*this.windStr;
  for(var i=0;i<this.flakes.length;i++){
    var f=this.flakes[i];
    f.y+=f.spd*sm; f.x+=f.drift+wd;
    if(f.y>this.H+4){f.y=-4;f.x=Math.random()*this.W;}
    if(f.x<-4||f.x>this.W+4)f.x=Math.random()*this.W;
  }
  /* skiers only when resort open */
  if(MCAM.isEnabled&&MCAM.resortOpen){
    for(var j=0;j<this.skiers.length;j++){
      var sk=this.skiers[j]; sk.x+=sk.vx; sk.y+=sk.vy; sk.wb+=sk.wbS;
      if(Math.abs(sk.x-this.W*.5)<20&&Math.random()<.008)this.motionFlash=85;
      if(sk.x<-35||sk.x>this.W+35||sk.y>this.H+20)this.skiers[j]=this._mkSk();
    }
  }
  /* gondolas: move when resort open, sway gently when closed */
  for(var k=0;k<this.gondolas.length;k++){
    var g=this.gondolas[k];
    if(MCAM.resortOpen&&MCAM.isEnabled){
      g.t+=g.spd; if(g.t>1)g.t-=1;
    } else {
      /* gentle sway in wind when stationary */
      g.swayV+=(Math.random()-.5)*.01-g.sway*.02;
      g.sway+=g.swayV;
    }
  }
  for(var m=0;m<this.clouds.length;m++){var c=this.clouds[m];c.x-=c.spd;if(c.x+c.w<0)c.x=this.W+c.w;}
};

WCR.prototype._draw=function(){
  if(!MCAM.isEnabled){this._drawOffline();return;}
  this._drawBg();
  this._drawClouds();
  this._drawBoundaryPoles();
  this._drawLift();
  this._drawTrees();
  this._drawLodge();
  this._drawPark();
  this._drawSnowCannons();
  this._drawFog();
  this._drawFlakes();
  if(MCAM.resortOpen){for(var i=0;i<this.skiers.length;i++)this._drawSkier(this.skiers[i]);}
  this._drawClosedOverlay();
  this._applyNV();
  this._applyVig();
  this._drawHUD();
  this._drawWeather();
  this._applyNoise();
};

WCR.prototype._tick=function(){
  this.frame++; this._updAll(); this._draw();
  this._raf=requestAnimationFrame(this._tick);
};

WCR.prototype.destroy=function(){if(this._raf)cancelAnimationFrame(this._raf);};
WCR.prototype.pause=function(){if(this._raf){cancelAnimationFrame(this._raf);this._raf=null;}};
WCR.prototype.resume=function(){if(!this._raf){this._tick();}};

/* ── Helpers ── */
function _fmt(d){function p(n){return String(n).padStart(2,'0');}return p(d.getHours())+':'+p(d.getMinutes())+':'+p(d.getSeconds())+'  '+p(d.getDate())+'/'+p(d.getMonth()+1)+'/'+d.getFullYear();}
function _pr(seed,n){return(((seed*1664525+n*1013904223)>>>0)&0x7fffffff)/0x7fffffff;}

/* ════ Boot grid renderers ════ */
var gridR=[];
for(var i=0;i<MCAM.numCams;i++){var cv=document.getElementById('mcam-canvas-'+i);if(cv)gridR.push(new WCR(cv,i));}

/* Hover effect */
document.querySelectorAll('.mcam-cell').forEach(function(cell){
  cell.addEventListener('mouseenter',function(){var h=cell.querySelector('.mcam-hov');if(h){h.style.background='rgba(0,0,0,0.32)';h.querySelector('span').style.opacity='1';}});
  cell.addEventListener('mouseleave',function(){var h=cell.querySelector('.mcam-hov');if(h){h.style.background='rgba(0,0,0,0)';h.querySelector('span').style.opacity='0';}});
});

/* ════ Modal ════ */
var modal=document.getElementById('mcam-modal');
var mInner=document.getElementById('mcam-modal-inner');
var mCanvas=document.getElementById('mcam-modal-canvas');
var mLabel=document.getElementById('mcam-modal-label');
var mR=null, mIdx=0;

function _openModal(idx){
  if(mR){mR.destroy();mR=null;}
  mIdx=idx;
  for(var i=0;i<gridR.length;i++)gridR[i].pause();
  modal.style.display='flex';
  modal.offsetHeight;
  modal.style.opacity='1';
  document.body.style.overflow='hidden';
  mR=new WCR(mCanvas,idx);
  var stateTxt=MCAM.isEnabled?(MCAM.streamMode?'  \u25B6 LIVE STREAM':'  \u25CF RECORDING'):'  \u25CF OFFLINE';
  if(!MCAM.resortOpen)stateTxt+='  \u274C CLOSED';
  mLabel.textContent=camNames[idx%camNames.length]+stateTxt;
  document.getElementById('mcam-prev').style.opacity=idx>0?'1':'0.25';
  document.getElementById('mcam-prev').style.pointerEvents=idx>0?'auto':'none';
  document.getElementById('mcam-next').style.opacity=idx<MCAM.numCams-1?'1':'0.25';
  document.getElementById('mcam-next').style.pointerEvents=idx<MCAM.numCams-1?'auto':'none';
}

function _closeModal(){
  modal.style.opacity='0';
  setTimeout(function(){
    modal.style.display='none';
    document.body.style.overflow='';
    if(mR){mR.destroy();mR=null;}
    for(var i=0;i<gridR.length;i++)gridR[i].resume();
  },220);
}

modal.addEventListener('click',function(e){if(e.target===modal)_closeModal();});
mInner.addEventListener('click',function(e){e.stopPropagation();});
document.getElementById('mcam-close').addEventListener('click',_closeModal);
document.getElementById('mcam-prev').addEventListener('click',function(e){e.stopPropagation();if(mIdx>0)_openModal(mIdx-1);});
document.getElementById('mcam-next').addEventListener('click',function(e){e.stopPropagation();if(mIdx<MCAM.numCams-1)_openModal(mIdx+1);});

document.querySelectorAll('.mcam-cell').forEach(function(cell){
  cell.addEventListener('click',function(){_openModal(parseInt(cell.dataset.camIndex,10));});
});

document.addEventListener('keydown',function(e){
  if(modal.style.display!=='flex')return;
  if(e.key==='Escape'){_closeModal();}
  if(e.key==='ArrowLeft'&&mIdx>0)_openModal(mIdx-1);
  if(e.key==='ArrowRight'&&mIdx<MCAM.numCams-1)_openModal(mIdx+1);
});

}());
</script>

</div>