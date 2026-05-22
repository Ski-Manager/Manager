<!-- Leaflet (only loaded on resort_map_controller normally; include separately here) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" crossorigin="">
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" crossorigin=""></script>

<div class="w-full">
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body mb-3">

            <?php echo $this->session->flashdata('msg'); ?>

            <h2 class="h2">✂️ <?php echo $this->lang->line('navbar')['custom_trail'] ?? 'Cut Your Own Trail'; ?></h2>
            <p class="mb-4"><?php echo $this->lang->line('slope')['custom_trail_intro'] ?? 'Design a custom trail suited to your mountain. Choose the aspect (direction it faces) to affect how snow accumulates on it differently from the rest of your resort.'; ?></p>

            <!-- Map Drawing Interface -->
            <fieldset class="fieldset mb-4">
                <legend class="fieldset-legend">🗺️ Draw Your Trail on the Map</legend>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="badge badge-info gap-1">ℹ️ Click map to place waypoints</span>
                    <span class="badge badge-ghost">Min 2 points required</span>
                </div>
                <div id="trail-draw-map" style="height:420px;width:100%;border-radius:0.5rem;border:1px solid hsl(var(--bc)/0.2);cursor:crosshair;"></div>
                <div class="flex flex-wrap gap-2 mt-2">
                    <button type="button" id="btn-undo" class="btn btn-sm btn-warning">↩ Undo</button>
                    <button type="button" id="btn-clear" class="btn btn-sm btn-error btn-outline">🗑 Clear</button>
                    <span class="badge badge-lg badge-ghost" id="point-count-display">0 points</span>
                </div>
                <div id="map-path-error" class="alert alert-error mt-2 hidden">
                    <span>Please draw at least 2 points on the map before building.</span>
                </div>
            </fieldset>

            <?php echo form_open('slope_controller/build_custom_slope', ['id' => 'customTrailForm']); ?>
            <?php echo form_hidden('buildCustomForm', 'buildCustomForm'); ?>
            <input type="hidden" id="custom_trail_path" name="path" value="">

            <!-- Trail Name -->
            <fieldset class="fieldset mb-4">
                <legend class="fieldset-legend"><?php echo $this->lang->line('slope')['choose_name'] ?? 'Trail Name'; ?></legend>
                <input type="text" id="custom_name" name="custom_name" maxlength="45" required
                       class="input border border-base-300 w-full max-w-sm"
                       placeholder="<?php echo $this->lang->line('slope')['choose_name'] ?? 'My Custom Trail'; ?>" />
            </fieldset>

            <!-- Slope Type -->
            <fieldset class="fieldset mb-4">
                <legend class="fieldset-legend"><?php echo $this->lang->line('slope')['diff_type_column'] ?? 'Slope Type'; ?></legend>
                <div class="flex flex-wrap gap-2" id="slope_type_buttons">
                    <button type="button" class="btn btn-outline btn-sm type-btn" data-type="1">⛷ Downhill</button>
                    <button type="button" class="btn btn-outline btn-sm type-btn" data-type="2">🛹 Snowpark</button>
                    <button type="button" class="btn btn-outline btn-sm type-btn" data-type="3">🏁 Boardercross</button>
                    <button type="button" class="btn btn-outline btn-sm type-btn" data-type="4">🎿 Cross-Country</button>
                    <button type="button" class="btn btn-outline btn-sm type-btn" data-type="5">🛷 Luge</button>
                </div>
                <input type="hidden" id="slope_type" name="slope_type" value="1" />
            </fieldset>

            <!-- Difficulty -->
            <fieldset class="fieldset mb-4">
                <legend class="fieldset-legend"><?php echo $this->lang->line('slope')['difficulty'] ?? 'Difficulty'; ?></legend>
                <div class="flex flex-wrap gap-2" id="difficulty_buttons">
                    <button type="button" class="btn btn-outline btn-sm diff-btn" data-diff="1">🟢 Green</button>
                    <button type="button" class="btn btn-outline btn-sm diff-btn" data-diff="2">🔵 Blue</button>
                    <button type="button" class="btn btn-outline btn-sm diff-btn" data-diff="3">🔴 Red</button>
                    <button type="button" class="btn btn-outline btn-sm diff-btn" data-diff="4">⚫ Black</button>
                </div>
                <input type="hidden" id="difficulty" name="difficulty" value="1" />
            </fieldset>

            <!-- Length (auto-calculated from drawn path) -->
            <fieldset class="fieldset mb-4">
                <legend class="fieldset-legend"><?php echo $this->lang->line('slope')['length'] ?? 'Length'; ?></legend>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-bold text-primary" id="length_display">—</span>
                    <span class="opacity-70"><?php echo $this->lang->line('slope')['length_unit'] ?? 'm'; ?></span>
                    <span class="badge badge-ghost text-xs">Auto-calculated from drawn path</span>
                </div>
                <input type="hidden" id="length_hidden" name="length" value="1000" />
            </fieldset>

            <!-- Aspect (auto-detected from drawn path) -->
            <fieldset class="fieldset mb-4">
                <legend class="fieldset-legend"><?php echo $this->lang->line('slope')['aspect'] ?? 'Aspect (Facing Direction)'; ?></legend>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="aspect_cards">
                    <div class="aspect-card card bg-base-200 border-2 border-transparent p-3 text-center transition-all" data-aspect="north">
                        <div class="text-2xl mb-1">❄️</div>
                        <div class="font-bold">North</div>
                        <div class="text-xs opacity-70 mt-1">Retains snow best — 20% less melt</div>
                    </div>
                    <div class="aspect-card card bg-base-200 border-2 border-transparent p-3 text-center transition-all" data-aspect="south">
                        <div class="text-2xl mb-1">☀️</div>
                        <div class="font-bold">South</div>
                        <div class="text-xs opacity-70 mt-1">Loses snow fastest — 30% more melt</div>
                    </div>
                    <div class="aspect-card card bg-base-200 border-2 border-transparent p-3 text-center transition-all" data-aspect="east">
                        <div class="text-2xl mb-1">🌤️</div>
                        <div class="font-bold">East</div>
                        <div class="text-xs opacity-70 mt-1">Moderate snow retention</div>
                    </div>
                    <div class="aspect-card card bg-base-200 border-2 border-transparent p-3 text-center transition-all" data-aspect="west">
                        <div class="text-2xl mb-1">🌤️</div>
                        <div class="font-bold">West</div>
                        <div class="text-xs opacity-70 mt-1">Moderate snow retention</div>
                    </div>
                </div>
                <p class="text-xs opacity-60 mt-2">📐 Auto-detected from your drawn path direction. Draw at least 2 points to calculate.</p>
                <input type="hidden" id="aspect" name="aspect" value="north" />
            </fieldset>

            <!-- Cost / Time Estimates -->
            <div class="stats shadow mb-4">
                <div class="stat">
                    <div class="stat-title"><?php echo $this->lang->line('home')['cost'] ?? 'Estimated Cost'; ?></div>
                    <div class="stat-value text-lg" id="est_cost">—</div>
                </div>
                <div class="stat">
                    <div class="stat-title"><?php echo $this->lang->line('home')['building_time'] ?? 'Build Time'; ?></div>
                    <div class="stat-value text-lg" id="est_time">—</div>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa-solid fa-hammer mr-2"></i><?php echo $this->lang->line('building')['build'] ?? 'Build'; ?>
                </button>
            </div>

            <?php echo form_close(); ?>

        </div>
    </div>
</div>

<script>
(function () {
    var prices       = <?php echo json_encode($slope_meter_price); ?>;
    var buildTimes   = <?php echo json_encode($slope_meter_building_time); ?>;
    var accel        = <?php echo (int) $accelerator_factor; ?>;
    var altitudeMult = <?php
        // Must match get_altitude_build_cost_multiplier() in mycustom_helper.php
        $alt_map = ['low' => 1.0, 'medium' => 1.15, 'high' => 1.30];
        echo isset($alt_map[$altitude]) ? $alt_map[$altitude] : 1.0;
    ?>;
    var baseUrl      = '<?php echo base_url(); ?>';

    var selectedType   = 1;
    var selectedDiff   = 1;
    var selectedAspect = 'north';

    function formatMoney(n) {
        return '€' + Math.round(n).toLocaleString();
    }

    function formatTime(seconds) {
        seconds = Math.round(seconds);
        var d = Math.floor(seconds / 86400);
        var h = Math.floor((seconds % 86400) / 3600);
        var m = Math.floor((seconds % 3600) / 60);
        var parts = [];
        if (d > 0) parts.push(d + 'd');
        if (h > 0) parts.push(h + 'h');
        if (m > 0) parts.push(m + 'min');
        return parts.length ? parts.join(' ') : '< 1min';
    }

    function updateEstimates(lengthM) {
        if (!lengthM || lengthM < 100) {
            document.getElementById('est_cost').textContent = '—';
            document.getElementById('est_time').textContent = '—';
            return;
        }
        var typeIdx   = selectedType - 1;
        var price     = parseFloat(prices[typeIdx] || prices[0]);
        var buildRate = parseFloat(buildTimes[typeIdx] || buildTimes[0]);
        var cost      = lengthM * price * altitudeMult;
        var timeSec   = lengthM * buildRate / accel;
        document.getElementById('est_cost').textContent = formatMoney(cost);
        document.getElementById('est_time').textContent = formatTime(timeSec);
    }

    // Type buttons
    document.querySelectorAll('.type-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.type-btn').forEach(function (b) {
                b.classList.remove('btn-primary'); b.classList.add('btn-outline');
            });
            btn.classList.remove('btn-outline'); btn.classList.add('btn-primary');
            selectedType = parseInt(btn.dataset.type, 10);
            document.getElementById('slope_type').value = selectedType;
            updateEstimates(currentLengthM);
        });
    });

    // Difficulty buttons
    document.querySelectorAll('.diff-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.diff-btn').forEach(function (b) {
                b.classList.remove('btn-primary'); b.classList.add('btn-outline');
            });
            btn.classList.remove('btn-outline'); btn.classList.add('btn-primary');
            selectedDiff = parseInt(btn.dataset.diff, 10);
            document.getElementById('difficulty').value = selectedDiff;
            redrawPolyline();
        });
    });

    // Initialise defaults
    document.querySelector('.type-btn[data-type="1"]').click();
    document.querySelector('.diff-btn[data-diff="1"]').click();
    updateEstimates(0);

    // ── Map drawing ──────────────────────────────────────────────────────────
    var MAP_SCALE   = 5;   // 1 pixel = 5 m
    var diffColors  = {1: '#2ecc71', 2: '#3498db', 3: '#e74c3c', 4: '#2c3e50'};
    var liftColor   = '#f39c12';

    var bounds = [[0, 0], [500, 1000]];
    var drawMap = L.map('trail-draw-map', {
        crs: L.CRS.Simple,
        minZoom: -2,
        maxZoom: 2
    });
    L.imageOverlay(baseUrl + 'img/images/map.jpg', bounds).addTo(drawMap);
    drawMap.fitBounds(bounds);

    var drawnPoints   = [];
    var markerLayer   = L.layerGroup().addTo(drawMap);
    var polylineLayer = null;
    var currentLengthM = 0;

    function getColor() { return diffColors[selectedDiff] || '#3498db'; }

    function redrawPolyline() {
        if (polylineLayer) { drawMap.removeLayer(polylineLayer); polylineLayer = null; }
        if (drawnPoints.length >= 2) {
            var latlngs = drawnPoints.map(function(p) { return [p.y, p.x]; });
            polylineLayer = L.polyline(latlngs, {
                color: getColor(), weight: 5, opacity: 1
            }).addTo(drawMap);
        }
    }

    function calcPathLength() {
        var total = 0;
        for (var i = 1; i < drawnPoints.length; i++) {
            var dx = drawnPoints[i].x - drawnPoints[i-1].x;
            var dy = drawnPoints[i].y - drawnPoints[i-1].y;
            total += Math.sqrt(dx*dx + dy*dy);
        }
        return Math.round(total * MAP_SCALE);
    }

    // Auto-detect aspect from path direction (first → last point)
    // In L.CRS.Simple: x = east/west axis, y = north/south axis (higher y = north/up)
    function calcAspect() {
        if (drawnPoints.length < 2) return 'north';
        var start = drawnPoints[0];
        var end   = drawnPoints[drawnPoints.length - 1];
        var dx = end.x - start.x;
        var dy = end.y - start.y;
        if (Math.abs(dy) >= Math.abs(dx)) {
            return dy > 0 ? 'north' : 'south';
        } else {
            return dx > 0 ? 'east' : 'west';
        }
    }

    function setAspect(asp) {
        selectedAspect = asp;
        document.getElementById('aspect').value = asp;
        document.querySelectorAll('.aspect-card').forEach(function(c) {
            var active = c.dataset.aspect === asp;
            c.classList.toggle('border-primary', active);
            c.classList.toggle('bg-primary', active);
            c.classList.toggle('text-primary-content', active);
            c.classList.toggle('border-transparent', !active);
        });
    }

    function serializePath() {
        return drawnPoints.map(function(p) {
            return '[' + Math.round(p.x) + ',' + Math.round(p.y) + ']';
        }).join(',');
    }

    function updateUI() {
        var count = drawnPoints.length;
        var countEl = document.getElementById('point-count-display');
        countEl.textContent = count + (count === 1 ? ' point' : ' points');
        countEl.className = 'badge badge-lg ' + (count >= 2 ? 'badge-success' : 'badge-ghost');

        if (count >= 2) {
            var metres = calcPathLength();
            metres = Math.max(100, Math.min(5000, metres));
            currentLengthM = metres;
            document.getElementById('length_display').textContent = metres + ' m';
            document.getElementById('length_hidden').value = metres;
            updateEstimates(metres);

            // Auto-detect aspect
            setAspect(calcAspect());
        } else {
            currentLengthM = 0;
            document.getElementById('length_display').textContent = '—';
            document.getElementById('length_hidden').value = 1000;
            updateEstimates(0);
        }

        document.getElementById('custom_trail_path').value = serializePath();
        redrawPolyline();
    }

    // Re-color polyline when difficulty changes
    document.querySelectorAll('.diff-btn').forEach(function(btn) {
        btn.addEventListener('click', redrawPolyline);
    });

    function addMarker(point) {
        L.circleMarker([point.y, point.x], {
            radius: 5, color: '#fff', fillColor: getColor(),
            fillOpacity: 1, weight: 2
        }).addTo(markerLayer);
    }

    drawMap.on('click', function(e) {
        var pt = {x: e.latlng.lng, y: e.latlng.lat};
        pt.x = Math.max(0, Math.min(1000, pt.x));
        pt.y = Math.max(0, Math.min(500, pt.y));
        drawnPoints.push(pt);
        addMarker(pt);
        updateUI();
    });

    document.getElementById('btn-undo').addEventListener('click', function() {
        if (!drawnPoints.length) return;
        drawnPoints.pop();
        markerLayer.clearLayers();
        drawnPoints.forEach(addMarker);
        updateUI();
    });

    document.getElementById('btn-clear').addEventListener('click', function() {
        drawnPoints = [];
        markerLayer.clearLayers();
        updateUI();
    });

    // Form submit validation
    document.getElementById('customTrailForm').addEventListener('submit', function(e) {
        var errEl = document.getElementById('map-path-error');
        if (drawnPoints.length < 2) {
            e.preventDefault();
            errEl.classList.remove('hidden');
            document.getElementById('trail-draw-map').scrollIntoView({behavior: 'smooth', block: 'center'});
            return false;
        }
        errEl.classList.add('hidden');
        document.getElementById('custom_trail_path').value = serializePath();
    });

    // ── Load existing slopes and lifts as background layers ──────────────────
    function parsePath(pathStr) {
        // Format: [x1,y1],[x2,y2],... → [[lat=y,lng=x], ...]
        var coords = [];
        var re = /\[(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)\]/g;
        var m;
        while ((m = re.exec(pathStr)) !== null) {
            coords.push([parseFloat(m[2]), parseFloat(m[1])]);  // [lat=y, lng=x]
        }
        return coords;
    }

    var slopeColorMap = {'Green':'#2ecc71','Blue':'#3498db','Red':'#e74c3c','Black':'#2c3e50'};

    fetch(baseUrl + 'resort_map_controller/get_slopes_map', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id_slope_type='
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.returned || !data.path) return;
        data.path.forEach(function(pathStr, i) {
            if (!pathStr) return;
            var coords = parsePath(pathStr);
            if (coords.length < 2) return;
            var style  = data.style ? data.style[i] : {};
            var color  = (style && style.color) ? (slopeColorMap[style.color] || style.color) : '#3498db';
            L.polyline(coords, {
                color: color, weight: 2, opacity: 0.55, interactive: false, dashArray: '4,3'
            }).addTo(drawMap);
        });
    })
    .catch(function() {});  // silently ignore if not logged in / no slopes

    fetch(baseUrl + 'resort_map_controller/get_lifts_map', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'lift_mode=false&id_lift_type=&id_grip_type=&capacity='
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.returned || !data.path) return;
        data.path.forEach(function(pathStr) {
            if (!pathStr) return;
            var coords = parsePath(pathStr);
            if (coords.length < 2) return;
            L.polyline(coords, {
                color: liftColor, weight: 2, opacity: 0.55, interactive: false, dashArray: '8,4'
            }).addTo(drawMap);
        });
    })
    .catch(function() {});
    // ── End map drawing ───────────────────────────────────────────────────────
}());
</script>
