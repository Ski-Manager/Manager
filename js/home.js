// Cache frequently accessed DOM elements for better performance
var $passwordConfirm = $("#password_confirm");
var $confirmResetSubmit = $("#confirm_reset_submit");
var $confirmDeleteSubmit = $("#confirm_delete_submit");

// Map layer variables used across multiple AJAX callbacks
var lift_path;
var slope_path;

if (typeof Settings === "undefined" || Settings === null) {
    var Settings = {};
}

if (typeof Settings.base_url !== "string") {
    Settings.base_url = "/";
}

function asset_url(path) {
    var baseUrl = Settings.base_url || "/";
    baseUrl = baseUrl.replace(/\/+$/, '');
    return baseUrl + '/' + String(path || '').replace(/^\/+/, '');
}

// Handling of password input when resetting or deleting an account
function togglePasswordConfirmActions(passwordLength) {
    var isEnabled = passwordLength > 3;

    $confirmResetSubmit.prop('disabled', !isEnabled);
    $confirmDeleteSubmit.prop('disabled', !isEnabled);
    $confirmResetSubmit.toggleClass('disabled', !isEnabled);
    $confirmDeleteSubmit.toggleClass('disabled', !isEnabled);
}

if ($confirmResetSubmit.length > 0 || $confirmDeleteSubmit.length > 0) {
    togglePasswordConfirmActions($passwordConfirm.val().length);
}

$passwordConfirm.on("keyup focus", function() {
    togglePasswordConfirmActions(this.value.length);
});

// Map
// Settings.base_url+'/img/images/map1.png
// initialisation

if ($('#mapid').length > 0) {
var map = L.map('mapid', {
        crs: L.CRS.Simple,
        minZoom: 0,
        maxZoom: 4
});


var yx = L.latLng;

var xy = function(x, y) {
    if (L.Util.isArray(x)) {    // When doing xy([x, y]);
            return yx(x[1], x[0]);
    }
    return yx(y, x);  // When doing xy(x, y);
};
var bounds = [xy(0, 500), xy(1000, 0)];      // Add boundaries: image size. For now we use twice the image size because current image it too small.
var mapImageUrl = Settings.map_image_url ? Settings.map_image_url : asset_url('img/images/map.jpg');
var image = L.imageOverlay(mapImageUrl, bounds).addTo(map);    // Set background map
var mapCenterX = (typeof Settings.map_center_x === 'number') ? Settings.map_center_x : 300;
var mapCenterY = (typeof Settings.map_center_y === 'number') ? Settings.map_center_y : 170;
var mapZoom    = (typeof Settings.map_zoom    === 'number') ? Settings.map_zoom    : 1;
map.setView(xy(mapCenterX, mapCenterY), mapZoom);
// end of initialisation
 
 
// customize markers from https://github.com/pointhi/leaflet-color-markers/blob/master/README.md
function create_marker_icon(colorName) {
    return new L.Icon({
      iconUrl: asset_url('img/icons/markers/marker-icon-' + colorName + '.png'),
      iconSize: [17, 29],
      iconAnchor: [8, 29],
      popupAnchor: [-8, -28],
    });
}
var blue = create_marker_icon('blue');
var red = create_marker_icon('red');
var green = create_marker_icon('green');
var orange = create_marker_icon('orange');
var yellow = create_marker_icon('yellow');
var violet = create_marker_icon('violet');
var grey = create_marker_icon('grey');
var black = create_marker_icon('black');
var turquoise = create_marker_icon('turquoise');
var pink = create_marker_icon('pink');
var lightgreen = create_marker_icon('lightgreen');
var lightpink = create_marker_icon('lightpink');
var darkblue = create_marker_icon('darkblue');
var darkgreen = create_marker_icon('darkgreen');
var lightred = create_marker_icon('lightred');
var brown = create_marker_icon('brown');
var brightyellow = create_marker_icon('brightyellow');
var color_array = [blue, red, green, orange, yellow, violet, grey, black, turquoise, pink, lightgreen, lightpink, darkblue, darkgreen, lightred, brown, brightyellow];

// Shows all markers on map. Used when adding more possible paths and creating "Areas".
function drawAllMarkers () {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/get_all_locations",
        success: function(result){
            if (result.returned === true){
                result.data.forEach(function(element) {
                    if (typeof color_array[element.area] !== 'undefined')   // to avoid running out of colors, we default to 0 (blue)
                        var styleIcon = color_array[element.area];
                    else
                        var styleIcon = color_array[0];
                    markers = L.marker([element.y_coordinates, element.x_coordinates], {icon: styleIcon } ).bindPopup('id_location: '+element.id_location+'<br>id_sector: '+element.id_sector+'<br>area: '+element.area).addTo(map);
                });
            }
        }
    });
}
    
    function isEven(n) {
        return n % 2 == 0;
    }
    
    
    function getHypotenuse(x1, y1, x2, y2) {
        // Determine line lengths
        var xlen = x2 - x1;
        var ylen = y2 - y1;

        // Determine hypotenuse length
        var hlen = Math.sqrt(Math.pow(xlen,2) + Math.pow(ylen,2));
        return hlen;
    }
    
    function convert_to_meters(hypotenuse_length) {
        var lift_length = hypotenuse_length * 17;
        return lift_length;
    }
/*
 * 
 * @param {type} x1             X coo (lng) of start point
 * @param {type} y1             Y coo (lat) of start point
 * @param {type} x2             X coo (lng) of end point
 * @param {type} y2             Y coo (lat) of end point
 * @param {type} distance       The variable identifying the length of the `shortened` line.
 * @returns {undefined}
 */
function getSegment(x1, y1, x2, y2, hypotenuse, hlen, offset_sign) {

    // Determine line lengths
    var xlen = x2 - x1;
    var ylen = y2 - y1;
    
    // Determine the ratio between they shortened value and the full hypotenuse.
    var ratio = hlen / hypotenuse;

    var smallerXLen = xlen * ratio;
    var smallerYLen = ylen * ratio;

    // The new X point is the starting x plus the smaller x length.
    var smallerX = x1 + smallerXLen;

    // Same goes for the new Y.
    var smallerY = y1 + smallerYLen;

    if (offset_sign == true)
        smallerX = smallerX-100;
    else
        smallerX = smallerX+50;
    return {y: smallerY, x: smallerX};    // returns coordinates of intermediate point and segment length
}

function drawSlope(slope_mode, id_slope_type_from_page = null) {
    
    //console.log(id_slope_type_from_page); // retrieves the id_slope_type_from_page from the click in the dropdown
    
    map.eachLayer(function (layer) {                // For each layer 
        if (typeof layer.feature !== 'undefined') {
            if (typeof layer.feature.properties !== 'undefined') {
                if (layer.feature.properties.type == 'lift' || layer.feature.properties.type == 'slope') {
                    map.removeLayer(layer);                     // Remove the layer (here: the lift, and slope.... to be done)
                }
            }
        }
    });
    $('#build_button').prop('disabled', true);  // When loading the page, the build button is disabled, at the same time the slopes are drew
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/get_slopes_map",
        data: "slope_mode="+slope_mode+"&id_slope_type="+id_slope_type_from_page,
        success: function(result){
            if (result.returned == true){
                for ( var i=0; i < result.path.length; i++ ) {
                    var style = result.style[i];
                    var length = result.length[i];
                    var segment = result.segment[i];
                    var id_slope = result.id_slope[i];
                    var id_status = result.id_status[i];
                    var class_slope_status = 'class_slope_status_'+id_status; // not used but may be re-used for slope type marker?
                    var custom_name = result.custom_name[i];
                    var slope_type_from_ajax = parseInt(result.slope_type[i], 10);
                    //console.log(slope_type);
                    if (slope_type_from_ajax == '1') {     // 1 = downhill
                        var geometry_type = 'LineString';
                        var path = JSON.parse("[" + result.path[i] + "]");
                    }
                    else if (slope_type_from_ajax == '2') {     // 2 = snowpark
                        var geometry_type = 'Polygon';
                        var path = JSON.parse("[[" + result.path[i] + "]]");    // Syntax for polygons/areas require to parse an extra [
                    }
                    else if (slope_type_from_ajax == '3') {     // 3 = boardercross
                        var geometry_type = 'LineString';
                        var path = JSON.parse("[" + result.path[i] + "]");
                    }
                    else if (slope_type_from_ajax == '4') {     // 4 = crosscountry
                        var geometry_type = 'LineString';
                        var path = JSON.parse("[" + result.path[i] + "]");
                    }
                    else if (slope_type_from_ajax == '5') {     // 5 = luge
                        var geometry_type = 'LineString';
                        var path = JSON.parse("[" + result.path[i] + "]");
                    }
                    else if (slope_type_from_ajax == '6') {     // 6 = terrain park
                        var geometry_type = 'Polygon';
                        var path = JSON.parse("[[" + result.path[i] + "]]");    // Syntax for polygons/areas require to parse an extra [
                    }
                    var area = {
                        "type": "FeatureCollection",
                        "features": [
                            {
                                "type": "Feature",
                                "geometry": {
                                    "type": geometry_type,  
                                    "coordinates": path
                                },
                                "properties": {
                                    "type": "slope",
                                    "length": length,
                                    "id_slope": id_slope,
                                    "id_slope_type_from_page": id_slope_type_from_page,
                                    "segment": segment
                                }
                            }
                        ]
                    };
                    //console.log(slope_meter_price);
                    //console.log(slope_meter_building_time);
                    //var slope_path = L.geoJson(path, style).addTo(map);
                    var previous_selected_layer_slope, previous_selected_target_slope
                    $i = 0;
                    // Visual layer (non-interactive) + near-invisible hit layer (1.5x weight for larger click area)
                    let visualSlope = new L.geoJson(area, {style: style, interactive: false});
                    var _sw = parseInt(style.weight);
                    var hitStyle = Object.assign({}, style, {
                        weight: _sw > 0 ? Math.max(Math.round(_sw * 1.5), 10) : 0,
                        opacity: 0.01,
                        fillOpacity: 0.01
                    });
                    slope_path = new L.geoJson(area, {
                            style: hitStyle,
                            onEachFeature: function (feature, layer) {
                                if (style.color !== 'purple') {    // If the slope is already built, we show a pop up
                                    layer.bindPopup('<p>Name: '+custom_name+'</p>');
                                    $i++;
                              }
                            }
                            //,distanceMarkers: {showAll: 1, offset: 500, cssClass: class_slope_status, iconSize: [10, 10] }
                        }).on('click', function (e) {
                            if (e.target.options.style.color == 'purple') {
                                if (previous_selected_layer_slope) {
                                    previous_selected_target_slope.resetStyle(previous_selected_layer_slope)
                                }
                                previous_selected_layer_slope = visualSlope.getLayers()[0]
                                previous_selected_target_slope = visualSlope
                                previous_selected_layer_slope.setStyle({
                                  'color' : 'purple',
                                    'opacity' : '1',
                                    'weight' : '8',
                                    'dashArray' : '5, 0'
                                })
                                var building_time = +e.layer.feature.properties.length * slope_meter_building_time[id_slope_type_from_page-1] /accelerator_factor; // building time is an array and we need to remove 1 to the index [0, 1, 2, 3] instead of IDs [1, 2, 3, 4]
                                // Will display time in 10:30:23 format
                                $('#location_length').html(e.layer.feature.properties.length+' '+Settings.meters); 
                                $('#estimated_building_time').html(secondsToHms(building_time)); 
                                $('#id_group_location').html(e.layer.feature.properties.segment); 
                                $('input[name="form_id_group_location"]').val(e.layer.feature.properties.segment);
                                $('input[name="form_difficulty"]').val(id_slope_difficulty);
                                $('input[name="form_id_slope"]').val(e.layer.feature.properties.id_slope);
                                $('input[name="form_id_slope_type_from_page"]').val(e.layer.feature.properties.id_slope_type_from_page);
                                var total_price = +slope_meter_price[id_slope_type_from_page-1] * parseFloat(e.layer.feature.properties.length).toFixed(0);   // adding "+" before variable to convert string to integer
                                // price per meter is an array and we need to remove 1 to the index [0, 1, 2, 3] instead of IDs [1, 2, 3, 4]
                                var total_price_float = parseFloat(total_price).toFixed(0);
                                var remainder = total_price_float.length % 3;
                                var total_price_display = (total_price_float.substr(0, remainder) + total_price_float.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();   // Friendly format with thousand space separator
                                total_price_display = total_price_display+' € ( = '+e.layer.feature.properties.length+'m x '+slope_meter_price[id_slope_type_from_page-1]+'€ )';
                            $('#total_price').html(total_price_display);
                                $('#build_button').prop('disabled', false);
                            }
                        });
                        
                    // console.log('id_slope: '+id_slope);
                    // console.log('id_status: '+id_status);
                    // console.log('id_slope_type_from_page: '+id_slope_type_from_page);
                    // console.log('slope_type_from_ajax: '+slope_type_from_ajax);
                    // console.log('slope_mode: '+slope_mode);
                    // console.log('-----');
                     
                    switch(slope_type_from_ajax) {
                        case 1: // downhill
                            if ( ( slope_mode == 'false' && id_status != '' ) || ( slope_mode == 'true' && (id_slope_type_from_page == 1  || id_status != '' ) )) {
                                //console.log('downhill');
                                slope_path.addTo(map);
                                visualSlope.addTo(map);
                            }
                            break;
                        case 2: // snowpark
                            if ( ( slope_mode == 'false' && id_status != '' ) || ( slope_mode == 'true' && (id_slope_type_from_page == 2  ||  id_status != '' ) )) { 
                                //console.log('snowpark');
                                slope_path.addTo(map);
                                visualSlope.addTo(map);
                            }
                            break;
                        case 3: // boardercross
                            if ( ( slope_mode == 'false' && id_status != '' ) || ( slope_mode == 'true' && (id_slope_type_from_page == 3  || id_status != '' ) )) {
                                //console.log('downhill');
                                slope_path.addTo(map);
                                visualSlope.addTo(map);
                            }
                            break;
                        case 4: // crosscountry
                            if ( ( slope_mode == 'false' && id_status != '' ) || ( slope_mode == 'true' && (id_slope_type_from_page == 4  || id_status != '' ) )) {
                                //console.log('downhill');
                                slope_path.addTo(map);
                                visualSlope.addTo(map);
                            }
                            break;
                        case 5: // luge
                            if ( ( slope_mode == 'false' && id_status != '' ) || ( slope_mode == 'true' && (id_slope_type_from_page == 5  || id_status != '' ) )) {
                                //console.log('downhill');
                                slope_path.addTo(map);
                                visualSlope.addTo(map);
                            }
                            break;
                        case 6: // terrain park
                            if ( ( slope_mode == 'false' && id_status != '' ) || ( slope_mode == 'true' && (id_slope_type_from_page == 6  || id_status != '' ) )) {
                                slope_path.addTo(map);
                                visualSlope.addTo(map);
                            }
                            break;
                        default:
                          // code block
                      }
                    
                    
                    
                     
                }
            }
            else {
               // console.log(result.returned);
            } 
        }
    });
}

function drawLift(lift_mode) {
    if (typeof lift_path !== 'undefined') {             // If lift_path (the lifts) exists
        map.eachLayer(function (layer) {                // For each layer 
            if (typeof layer.feature !== 'undefined') {
                if (typeof layer.feature.properties !== 'undefined') {
                    if (layer.feature.properties.type == 'lift' || layer.feature.properties.type == 'slope') {
                        map.removeLayer(layer);                     // Remove the layer (here: the lift, and slope.... to be done)
                    }
                }
            }
        });
    }
    if (lift_mode == 'true') {
        var data_post = "lift_mode="+lift_mode+"&id_lift_type="+id_lift_type+"&id_grip_type="+id_grip_type+"&capacity="+capacity;
    }
    else if (lift_mode == 'false') {    // If FALSE is specifically sent to lift_mode, we have clicked on one of the step of choosing the lift/slope. We need to remove all extra layers created
        var data_post = "lift_mode="+lift_mode;
    }
    else {
       var data_post = "lift_mode="+lift_mode;
    }

    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/get_lifts_map",
        data: data_post,
        success: function(result){
            if (result.returned == true){
                for ( var i=0; i < result.id_group.length; i++ ) {
                    //console.log([i]);
                    var start_location = result.start_location_coordinates[i];  // Retrieve Start coordinates (array) for specific lift
                    var end_location = result.end_location_coordinates[i];      // Retrieve End coordinates (array) for specific lift
                    if (typeof start_location !== 'undefined' && typeof end_location !== 'undefined') {
                        var id_group = result.id_group[i];             
                        var name = result.name[i];             
                        var style = result.style[i];                        
                        var id_lift_type = result.id_lift_type[i];
                        var lift_type = result.lift_type[i]; 
                        var class_lift_type = 'class_lift_'+id_lift_type;  
                        var base_cost = result.base_cost[i];                     
                        var meter_cost = result.meter_cost[i]; 
                        var path_info = {
                            "type": "FeatureCollection",
                            "features": [
                                {
                                    "type": "Feature",
                                    "geometry": {
                                        "type": "LineString",
                                        "coordinates": [start_location, end_location]
                                    },
                                    "properties": {
                                        "type": "lift",
                                        "id_group": id_group,
                                        "name": name,
                                        "lift_type": lift_type,
                                        "base_cost": base_cost,
                                        "meter_cost": meter_cost
                                    }
                                }
                            ]
                        };
                        // Variable to store selected previous click
                        var previous_selected_layer, previous_selected_target
                        $i = 0;
                        // Visual layer (non-interactive, with distance markers) + near-invisible hit layer (1.5x weight for larger click area)
                        let visualLift = new L.geoJson(path_info, {
                            style: style,
                            interactive: false,
                            distanceMarkers: {showAll: 1, offset: 500, cssClass: class_lift_type, iconSize: [50, 50] }
                        });
                        var _lw = parseInt(style.weight);
                        var liftHitStyle = Object.assign({}, style, {
                            weight: _lw > 0 ? Math.max(Math.round(_lw * 1.5), 10) : 0,
                            opacity: 0.01,
                            fillOpacity: 0.01
                        });
                        lift_path = new L.geoJson(path_info, {
                            style: liftHitStyle,
                            onEachFeature: function (feature, layer) {
                                if (style.color === 'black') {    // If the lift is already built, we show a pop up                              
                                  layer.bindPopup('<p>Name: '+feature.properties.name+'<br>Type: '+feature.properties.lift_type+'</p>');
                                  $i++;
                                }
                            }
                        }).on('click', function (e) {
                            if (e.target.options.style.color != 'black') {
                                if (previous_selected_layer) {
                                    previous_selected_target.resetStyle(previous_selected_layer)
                                }
                                previous_selected_layer = visualLift.getLayers()[0]
                                previous_selected_target = visualLift
                                previous_selected_layer.setStyle({
                                  'color' : 'purple',
                                    'opacity' : '1',
                                    'weight' : '8',
                                    'dashArray' : '5, 0'
                                })

                                $('#id_group_location').html(e.layer.feature.properties.id_group); 
                                // Get hypotenuse length by passing start longitude, start latitude, end longitude, end latitude
                                var hypotenuse_length = getHypotenuse (e.layer.feature.geometry.coordinates[0][0], e.layer.feature.geometry.coordinates[0][1], e.layer.feature.geometry.coordinates[1][0], e.layer.feature.geometry.coordinates[1][1]);
                                var lift_length_meters = parseFloat(convert_to_meters (hypotenuse_length)).toFixed(0);
                                var total_price = +e.layer.feature.properties.base_cost + +e.layer.feature.properties.meter_cost * parseFloat(lift_length_meters).toFixed(0);   // adding "+" before variavle to convert string to integer
                                $('#location_length').html(lift_length_meters+' '+Settings.meters);
                                var total_price_float = parseFloat(total_price).toFixed(0);
                                //console.log(e.layer.feature.properties.base_cost);
                                //console.log(e.layer.feature.properties.meter_cost);
                                //console.log(parseFloat(lift_length_meters).toFixed(0));
                                var remainder = total_price_float.length % 3;
                                var total_price_display = (total_price_float.substr(0, remainder) + total_price_float.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();   // Friendly format with thousand space separator
                                total_price_display = total_price_display+' € ( = '+e.layer.feature.properties.base_cost+'€ + '+parseFloat(lift_length_meters).toFixed(0)+'m x '+e.layer.feature.properties.meter_cost+'€ )';
                                
                                
                                $('#total_price').html(total_price_display); 
                                $('input[name="form_id_group_location"]').val(e.layer.feature.properties.id_group);
                                $('input[name="form_id_grip_type"]').val(id_grip_type);
                                $('input[name="form_capacity"]').val(capacity);
                                $('input[name="form_lift_length_meters"]').val(lift_length_meters);
                                $('#build_button').prop('disabled', false);
                            }
                        });
                        lift_path.addTo(map);
                        visualLift.addTo(map);
                    }
                }
            }
        }
    });
};



function drawSector() {
    var sector_layers = new Array();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/get_sectors_map",
        success: function(result){
            if (result.returned == true) {
                for ( var i=0; i < result.path.length; i++ ) {
                    if (result.path[i]) {
                        var coords = JSON.parse("[[" + result.path[i] + "]]");
                        var style  = result.style[i];
                        var area = {
                            "type": "Feature",
                            "geometry": {
                                "type": "Polygon",
                                "coordinates": coords
                            },
                            "properties": {
                                "type": "sector",
                                "id_sector": result.id_sector[i]
                            }
                        };
                        sector_layers.push(L.geoJson(area, style));
                    }
                }
            }
            var sector_layer_group = L.layerGroup(sector_layers).addTo(map);
            var overlays = {};
            overlays[Settings.show_sectors] = sector_layer_group;
            if (!$(".leaflet-control-layers-selector")[0]) {
                L.control.layers(undefined, overlays).addTo(map);
            }
            map.eachLayer(function(layer) {
                if (typeof layer.feature !== 'undefined' &&
                    typeof layer.feature.properties !== 'undefined' &&
                    layer.feature.properties.type === 'sector') {
                    layer.bringToBack();
                }
            });
        }
    });
}

function drawAllLifts() {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/get_all_lifts_map",
        success: function(result){
            if (result.returned == true){
                for ( var i=0; i < result.id_group.length; i++ ) {
                    //console.log([i]);
                    var start_location = result.start_location_coordinates[i];  // Retrieve Start coordinates (array) for specific lift
                    var end_location = result.end_location_coordinates[i];      // Retrieve End coordinates (array) for specific lift
                    if (typeof start_location !== 'undefined' && typeof end_location !== 'undefined') {    
                        var path_info = {
                            "type": "FeatureCollection",
                            "features": [
                                {
                                    "type": "Feature",
                                    "geometry": {
                                        "type": "LineString",
                                        "coordinates": [start_location, end_location]
                                    }
                                }
                            ]
                        };
                        lift_path = new L.geoJson(path_info, {
                            style:{          
                                'color' : 'black',
                                'weight' : '3',
                                'dashArray' : '5, 0'}
                        }).addTo(map);
                    }
                }
            }
        }
    });
}

function drawAllSlopes() {
    // Get all slopes, except snowparks
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/get_all_slopes_map",
        success: function(result){
            //console.log(result)
            for ( var i=0; i < result.path.length; i++ ) {
                var path = JSON.parse("[" + result.path[i] + "]");
                var weight = result.weight[i];
                //console.log(weight);
                var path = {
                    "type": "FeatureCollection",
                    "features": [
                        {
                            "type": "Feature",
                            "geometry": {
                                "type": "LineString",
                                "coordinates": path
                            }
                        }
                    ]
                };
                slope_path = new L.geoJson(path, {
                    style: {'color' : 'blue',
                    'smoothFactor' : '0',
                    'weight' : weight,
                    'dashArray' : '5, 0'}
                });
                slope_path.addTo(map); 
            }
        }
    });
    // Get all snowparks
    var sector_path = new Array() 
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/get_all_slopes_map",
        data: "slope_type=2",   // 2 = snowpark
        success: function(result){
            if (result.returned == true){
                for ( var i=0; i < result.path.length; i++ ) {
                    var path = JSON.parse("[[" + result.path[i] + "]]");
                    
                    var style = {           // Defines the style of area
                    'color' : 'red',
                    'smoothFactor' : '0',
                    'weight' : '1',
                    'opacity' : '0.8'};
                
                    //var id_sector = result.id_sector[i];
                    var area = {
                        "type": "Feature",
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": path
                        },
                        "properties": {
                            "type": "area"//,
                            //"id_sector": id_sector
                        }
                    };
                    
                       // console.log('area ALL');
                        //console.log(area);
                    sector_path[i] = L.geoJson(area, style);
                    var sectors = L.layerGroup([sector_path[i]]).addTo(map);
                }
            }
            
        }
    });
}

// do the actual ajax calls

//drawAllMarkers(); // Shows all markers on map. Used when adding more possible paths and creating "Areas".  
//drawAllLifts(true);            
//drawAllSlopes(true);            
drawLift('false');            
drawSlope('false');
drawSector('false');

// Stubs retained so any lingering references don't throw errors
function stopLiftDraw() {}
function stopSlopeDraw() {}

$('#build_button').on('click', function() {
    // Route to the correct pre-drawn build controller based on whether a slope or lift is selected
    var id_slope = $('input[name="form_id_slope"]').val();
    if (id_slope && id_slope !== '') {
        $('#buildForm').prop('action', Settings.base_url + 'slope_controller/build_slope');
    } else {
        $('#buildForm').prop('action', Settings.base_url + 'lift_controller/build_lift');
    }
    // Allow the form to submit normally
});


$('#build_slope').on("click", function(e){
    if ( $('#build_slope').hasClass( "disabled" ) ) {
        return false;
    }
    else {
        stopSlopeDraw();
        stopLiftDraw();
        $('#lift_table_info').fadeOut(100);
        $('#lift_types_button').html('');
        $('#grip_types_button').html('');
        $('#lift_capacity').html('');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url+"resort_map_controller/show_slope_types",
            success: function(result){
                if (result.returned == true){
                    $('#spacing').html('<br>');
                    $('#build_lift').switchClass('btn-info', 'btn-secondary');
                    $('#build_slope').switchClass('btn-secondary', 'btn-info');
                    $('#slope_type_button').html(result.data);
                    $('#id_group_location').html('');
                    $('#location_length').html('');
                    $('#estimated_building_time').html('');
                    $('#total_price').html('');
                    drawLift('false');
                    drawSlope('false');
    // Default slope type to 1 (downhill) and show difficulties immediately
    window.id_slope_type_from_page = 1;
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/show_slope_difficulty",
        data: "id_slope_type="+window.id_slope_type_from_page,
        success: function(result){
            if (result.returned == true){
                    $('#spacing').html('<br>');
                    $('#build_lift').switchClass('btn-info', 'btn-secondary');
                    $('button.slope_type').switchClass('btn-info', 'btn-secondary');
                    $('#build_slope').switchClass('btn-secondary', 'btn-info');
                    $('#type-'+window.id_slope_type_from_page).switchClass('btn-secondary', 'btn-info');
                    $('#slope_difficulty_button').html(result.data);
                    $('#id_group_location').html('');
                    $('#location_length').html('');
                    $('#estimated_building_time').html('');
                    $('#total_price').html('');
                    drawLift('false');
                    drawSlope('false');
            }
        }
    });
                }   // end if (result.returned == true) for show_slope_types
            }       // end success callback for show_slope_types
        });         // end outer $.ajax for show_slope_types
    }               // end else block
});                 // end $('#build_slope').on("click", ...)

// Handle slope type button click: refresh difficulty list for selected type
$(document).on('click', 'button.slope_type', function(){
    if ($(this).hasClass('disabled')) { return false; }
    window.id_slope_type_from_page = parseInt($(this).attr('id').replace("type-",""), 10);
    stopSlopeDraw();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/show_slope_difficulty",
        data: "id_slope_type="+window.id_slope_type_from_page,
        success: function(result){
            if (result.returned == true){
                $('button.slope_type').switchClass('btn-info', 'btn-secondary');
                $('#type-'+window.id_slope_type_from_page).switchClass('btn-secondary', 'btn-info');
                $('#slope_difficulty_button').html(result.data);
                $('#slope_table_info').html('');
                $('#id_group_location').html('');
                $('#location_length').html('');
                $('#estimated_building_time').html('');
                $('#total_price').html('');
                drawLift('false');
                drawSlope('false');
            }
        }
    });
});

$(document).on('click' , 'button.slope_difficulty', function(){
    id_slope_difficulty = $(this).attr('id').replace("difficulty-","");
    local_id_slope_type_from_page = window.id_slope_type_from_page || 1;
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/show_slope_info",
        data: "id_slope_difficulty="+id_slope_difficulty+"&id_slope_type="+local_id_slope_type_from_page,
        success: function(result){
            if (result.returned == true){
                $('button.slope_difficulty').switchClass('btn-info', 'btn-secondary');
                $('#slope_difficulty').switchClass('btn-secondary', 'btn-info');
                $('#difficulty-'+id_slope_difficulty).switchClass('btn-secondary', 'btn-info');
                $('#slope_table_info').fadeOut('fast', function() {
                    $('#spacing').html('');
                    $('#slope_table_info').html(result.table);
                    $('#slope_table_info').fadeIn(2000);
                });
                $('#slope_result_info').html(result.result_info);
                $('#id_group_location').html('');
                $('#location_length').html('');
                $('#total_price').html('');
                // Show predefined slopes available to build
                drawLift('false');
                drawSlope('true', local_id_slope_type_from_page);
            }
        }
    });    
});
    
$('#build_lift').on("click", function(e){
    if ( $('#build_lift').hasClass( "disabled" ) ) {
        return false;
    }
    else {
        stopSlopeDraw();
        stopLiftDraw();
        $('#slope_type_button').html('');
        $('#slope_difficulty_button').html('');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url+"resort_map_controller/show_lift_types",
            success: function(result){
                if (result.returned == true){
                    $('#lift_types_button').html(result.data);
                    $('#grip_types_button').html('');
                    $('#lift_capacity').html('');
                    $('#slope_difficulty_button').html('');
                    $('#build_lift').switchClass('btn-secondary', 'btn-info');
                    $('#build_slope').switchClass('btn-info', 'btn-secondary');
                    $('#spacing').html('<br>');
                    $('#lift_table_info').fadeOut('slow');
                    $('#id_group_location').html('');
                    $('#location_length').html('');
                    $('#estimated_building_time').html('');
                    $('#total_price').html('');
                    drawLift('false');
                    drawSlope('false');
                }
            }
        });
    }
});
$(document).on('click' , 'button.lift_type', function(){
    id_lift_type = $(this).attr('id'); 
    $('input[name="form_id_lift_type"]').val(id_lift_type);
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/show_grip_types",
        data: "id_lift_type="+id_lift_type,
        success: function(result){
            if (result.returned == true){
                $('#grip_types_button').html(result.data);
                $('#lift_capacity').html('');
                $('button.lift_type').switchClass('btn-info', 'btn-secondary');
                $('#'+id_lift_type).switchClass('btn-secondary', 'btn-info');
                $('input[name="form_id_lift_type"]').val(id_lift_type);
                $('#spacing').html('<br>');
                $('#lift_table_info').fadeOut('slow');
                $('#id_group_location').html('');
                $('#location_length').html('');
                $('#total_price').html('');
                $('#estimated_building_time').html('');
                drawLift('false');
                drawSlope('false');
                // Auto-select when only one grip type exists (skips an unnecessary click)
                var $gripBtns = $('#grip_types_button button.grip_type');
                if ($gripBtns.length === 1) {
                    $gripBtns.first().trigger('click');
                }
            }
        }
    });    
});
$(document).on('click' , 'button.grip_type', function(){
    id_lift_type = $(this).attr('id'); 
    id_grip_type = $(this).attr('data-id_grip_type'); 
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/show_capacity",
        data: "id_lift_type="+id_lift_type+"&id_grip_type="+id_grip_type,
        success: function(result){
            if (result.returned == true){
                $('#lift_capacity').html(result.data);
                $('button.grip_type').switchClass('btn-info', 'btn-secondary');
                $('[data-id_grip_type='+id_grip_type+']:not([data-capacity])').switchClass('btn-secondary', 'btn-info');
                $('#spacing').html('<br>');
                $('#lift_table_info').fadeOut('slow');
                $('#id_group_location').html('');
                $('#location_length').html('');
                $('#estimated_building_time').html('');
                $('#total_price').html('');
                drawLift('false');
                drawSlope('false');
            }
        }
    });    
});
$(document).on('click' , 'button.capacity', function(){
    id_lift_type = $(this).attr('id'); 
    id_grip_type = $(this).attr('data-id_grip_type'); 
    capacity = $(this).attr('data-capacity'); 
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"resort_map_controller/show_lift_info",
        data: "id_lift_type="+id_lift_type+"&id_grip_type="+id_grip_type+"&capacity="+capacity,
        success: function(result){
            if (result.returned == true){
                var building_time = result.data.building_time;
                $('button.capacity').switchClass('btn-info', 'btn-secondary');
                $('[data-capacity='+capacity+']').switchClass('btn-secondary', 'btn-info');
                $('#info_throughput').html(result.data.throughput);
                $('#base_cost').html(result.data.base_cost);
                $('#meter_cost').html(result.data.meter_cost);
                $('#estimated_building_time').html(secondsToHms(building_time));
                $('#lift_table_info').fadeOut('fast', function() {
                    $('#spacing').html('');
                    $('#lift_table_info').html(result.table);
                    $('#lift_table_info').fadeIn(2000);
                });
                $('#lift_result_info').html(result.result_info);
                $('#id_group_location').html('');
                $('#location_length').html('');
                $('#total_price').html('');
                // Show predefined lift locations available to build
                drawLift('true');
                drawSlope('false');
            }
        }
    });    
});



} // end of "if mapid exists"


$( "#order_report" ).click(function() {
   id_lift_type = $(this).attr('id'); 
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"reporting_controller/order_report",
        success: function(result){
            if (result.returned == true){
                $('#result_order').html(result.message);
            }
        }
    });    
});

function initializeTable(tbl, staff_type){
    return tbl.DataTable({
        "processing": true,
            "serverSide": false,
            "paging":   false,      // no paging in the table
            "ordering": false,      // no ordering, no link on header, no sorting
            "info":     false,      // no info on number of results in page
            "ajax": {
                "dataSrc": "Data",
                "url": Settings.base_url+"hire_staff_controller/getDataTable",
                "data": { string: staff_type },          // gets the value from the hidden search field and check the db with this string (i.e "mechanic")
                "type": "POST"
            },
            "columns": [
                { "data": "name_english" },
                { "data": "efficiency" },
                { "data": "salary" },
                { "data": "id_staff",       // The data to use in the link
                 "render": function(data,type,row,meta) {
                   var a = "<a href='"+Settings.base_url+"hire_staff_controller/hire_staff/"+row.id_staff+"/"+row.salary+"'><button class=\"btn btn-success\">"+Settings.hire_text+"</button>";
                   return a;
                 }
               }
            ]
    });
}

$(document).ready(function(){
    // To avoid CSS conflicts between Jquery and Bootstrap (guard for Bootstrap 5 which defers jQuery plugin setup)
    if ($.fn.button && $.fn.button.noConflict) {
        $.fn.bootstrapBtn = $.fn.button.noConflict();
    }
    var cancelLabel = Settings.cancel_fire_staff || Settings.cancel || 'Cancel';
    
    // ***************
    // START Editing assigned staff in Groomer_controller (from select menu)
    // ***************
    
    // To remember previous value in Select and change back if already assigned
    var previous;
    $('select[id*="groomer_assigned_sector_"]').on('focus', function () {
        // Store the current value on focus and on change
        previous = this.selectedIndex;
    });
    
    var domainName = location.protocol + '//' + location.host;
    $('select[id*="groomer_assigned_sector_"]').on("change", function(e){
                IdOfSelect = $(this).attr('id');                                    // gives "assigned_to_XX"
                //id_hired_staff = $(this).closest('tr').attr('data-id_hired_staff'); // The ID of the row, or ID of hired staff
                id_equipment = $(this).attr('data-id_purchased_equipments'); // The ID of the row, or ID of hired staff
                type = $(this).attr('data-type'); // Type of the item, used for "sectors" now
                var idOfSelectedOption = $("#"+IdOfSelect).val();                   // The value of the selected option, which id ID of the item (slope, lift, building...)
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: Settings.base_url+"groomer_controller/edit_assigned_item",
                    data: "id_equipment="+id_equipment+"&idSector="+idOfSelectedOption+"&type="+type,
                    success: function(result){
                        if (result.returned == true){
                            var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.updated+'"><i class="fa-solid fa-check"></i></div>';
                            $('#result_'+IdOfSelect).html(success_image); 
                                            $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                            $(refresh_achievements_sidebar);  // Calls refresh_achievements_sidebar after the achievement is completed
                        }
                        else {
                            var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.not_updated_already_assigned+'"><i class="fa-solid fa-xmark" style="color: rgb(212, 30, 30);"></i></div>';
                            $('#result_'+IdOfSelect).html(success_image); 
                                            $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                            document.getElementById(IdOfSelect).selectedIndex = previous;
                        } 
                    },
                    error: function(result){
                       var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.not_updated_already_assigned+'"><i class="fa-solid fa-xmark" style="color: rgb(212, 30, 30);"></i></div>';
                        $('#result_'+IdOfSelect).html(success_image); 
                                    $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                        document.getElementById(IdOfSelect).selectedIndex = previous;
                    }
                });
        });
    // ***************
    // END Editing assigned staff in Groomer_controller (from select menu)
    // ***************

    // ***************
    // START Rename groomer equipment
    // ***************
    $(document).on('click', '.rename-groomer-btn', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $(this).hide();
        $('#groomer_rename_form_' + id).show();
        $('#groomer_rename_input_' + id).focus().select();
    });

    $(document).on('click', '.groomer-rename-cancel', function(){
        var id = $(this).data('id');
        $('#groomer_rename_form_' + id).hide();
        $('.rename-groomer-btn[data-id="' + id + '"]').show();
    });

    $(document).on('click', '.groomer-rename-submit', function(){
        var id = $(this).data('id');
        var new_name = $('#groomer_rename_input_' + id).val().trim();
        if (new_name.length === 0) return;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url + "groomer_controller/rename_equipment",
            data: { id_equipment: id, new_name: new_name },
            success: function(result){
                if (result.returned === true) {
                    $('#groomer_name_display_' + id).text(result.new_name);
                    $('#groomer_rename_input_' + id).val(result.new_name);
                    $('.rename-groomer-btn[data-id="' + id + '"]').attr('data-name', result.new_name);
                    $('#groomer_rename_form_' + id).hide();
                    $('.rename-groomer-btn[data-id="' + id + '"]').show();
                }
            }
        });
    });

    $(document).on('keydown', '.groomer-rename-form input', function(e){
        var id = $(this).closest('.groomer-rename-form').attr('id').replace('groomer_rename_form_', '');
        if (e.key === 'Enter') {
            $('.groomer-rename-submit[data-id="' + id + '"]').trigger('click');
        } else if (e.key === 'Escape') {
            $('.groomer-rename-cancel[data-id="' + id + '"]').trigger('click');
        }
    });
    // ***************
    // END Rename groomer equipment
    // ***************

    // ***************
    // START Grooming intensity select
    // ***************
    $(document).on('change', '.groomer-intensity-select', function(){
        var id = $(this).data('id');
        var intensity = $(this).val();
        var $result = $('#intensity_result_' + id);
        $.ajax({
            type: 'POST',
            url: Settings.base_url + 'groomer_controller/set_intensity',
            data: { id_equipment: id, intensity: intensity },
            dataType: 'json',
            success: function(result){
                if (result.returned) {
                    $result.html(' <span class="text-success">&#10003;</span>');
                } else {
                    $result.html(' <span class="text-danger">&#10005;</span>');
                }
                setTimeout(function(){ $result.html(''); }, 2000);
            }
        });
    });
    // ***************
    // END Grooming intensity select
    // ***************

    // ***************
    // START Grooming active/standby select
    // ***************
    $(document).on('change', '.groomer-active-select', function(){
        var id = $(this).data('id');
        var active = $(this).val();
        var $result = $('#active_result_' + id);
        $.ajax({
            type: 'POST',
            url: Settings.base_url + 'groomer_controller/toggle_active',
            data: { id_equipment: id, active: active },
            dataType: 'json',
            success: function(result){
                if (result.returned) {
                    $result.html(' <span class="text-success">&#10003;</span>');
                } else {
                    $result.html(' <span class="text-danger">&#10005;</span>');
                }
                setTimeout(function(){ $result.html(''); }, 2000);
            }
        });
    });
    // ***************
    // END Grooming active/standby select
    // ***************

    // ***************
    // START Set-all grooming intensity
    // ***************
    $(document).on('click', '#groomer-set-all-intensity-btn', function(){
        var intensity = $('#groomer-set-all-intensity').val();
        var $result = $('#groomer-set-all-intensity-result');
        $.ajax({
            type: 'POST',
            url: Settings.base_url + 'groomer_controller/set_all_intensity',
            data: { intensity: intensity },
            dataType: 'json',
            success: function(result){
                if (result.returned) {
                    // Update all individual intensity dropdowns to match
                    $('.groomer-intensity-select').val(result.intensity);
                    $result.html('<span class="text-success">' + (typeof groomer_set_all_ok !== 'undefined' ? groomer_set_all_ok : '&#10003;') + '</span>');
                } else {
                    $result.html('<span class="text-danger">&#10005;</span>');
                }
                setTimeout(function(){ $result.html(''); }, 3000);
            }
        });
    });
    // ***************
    // END Set-all grooming intensity
    // ***************

    // ***************
    // START Editing assigned staff in skibus_controller (from select menu)
    // ***************
    
    // To remember previous value in Select and change back if already assigned
    var previous;
    $('select[id*="skibus_assigned_sector_"]').on('focus', function () {
        // Store the current value on focus and on change
        previous = this.selectedIndex;
    });
    
    var domainName = location.protocol + '//' + location.host;
    $('select[id*="skibus_assigned_sector_"]').on("change", function(e){
                IdOfSelect = $(this).attr('id');                                    // gives "assigned_to_XX"
                //id_hired_staff = $(this).closest('tr').attr('data-id_hired_staff'); // The ID of the row, or ID of hired staff
                id_equipment = $(this).attr('data-id_purchased_equipments'); // The ID of the row, or ID of hired staff
                type = $(this).attr('data-id_type'); // Type of the item, used for "sectors" now
                var idOfSelectedOption = $("#"+IdOfSelect).val();                   // The value of the selected option, which id ID of the item (slope, lift, building...)
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: Settings.base_url+"skibus_controller/edit_assigned_item",
                    data: "id_equipment="+id_equipment+"&idSector="+idOfSelectedOption+"&type="+type,
                    success: function(result){
                        if (result.returned == true){
                            var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.updated+'"><i class="fa-solid fa-check"></i></div>';
                            $('#result_'+IdOfSelect).html(success_image); 
                                            $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                            $(refresh_achievements_sidebar);  // Calls refresh_achievements_sidebar after the achievement is completed
                        }
                        else {
                            var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.not_updated_already_assigned+'"><i class="fa-solid fa-xmark" style="color: rgb(212, 30, 30);"></i></div>';
                            $('#result_'+IdOfSelect).html(success_image); 
                                            $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                            document.getElementById(IdOfSelect).selectedIndex = previous;
                        } 
                    }
                });
        });
    // ***************
    // END Editing assigned staff in skibus_controller (from select menu)
    // ***************

    // ***************
    // START Rename skibus equipment
    // ***************
    $(document).on('click', '.rename-skibus-btn', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $(this).hide();
        $('#skibus_rename_form_' + id).show();
        $('#skibus_rename_input_' + id).focus().select();
    });

    $(document).on('click', '.skibus-rename-cancel', function(){
        var id = $(this).data('id');
        $('#skibus_rename_form_' + id).hide();
        $('.rename-skibus-btn[data-id="' + id + '"]').show();
    });

    $(document).on('click', '.skibus-rename-submit', function(){
        var id = $(this).data('id');
        var new_name = $('#skibus_rename_input_' + id).val().trim();
        if (new_name.length === 0) return;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url + "skibus_controller/rename_equipment",
            data: { id_equipment: id, new_name: new_name },
            success: function(result){
                if (result.returned === true) {
                    $('#skibus_name_display_' + id).text(result.new_name);
                    $('#skibus_rename_input_' + id).val(result.new_name);
                    $('.rename-skibus-btn[data-id="' + id + '"]').attr('data-name', result.new_name);
                    $('#skibus_rename_form_' + id).hide();
                    $('.rename-skibus-btn[data-id="' + id + '"]').show();
                }
            }
        });
    });

    $(document).on('keydown', '.skibus-rename-form input', function(e){
        var id = $(this).closest('.skibus-rename-form').attr('id').replace('skibus_rename_form_', '');
        if (e.key === 'Enter') {
            $('.skibus-rename-submit[data-id="' + id + '"]').trigger('click');
        } else if (e.key === 'Escape') {
            $('.skibus-rename-cancel[data-id="' + id + '"]').trigger('click');
        }
    });
    // ***************
    // END Rename skibus equipment
    // ***************
    
    
    
    
    // ***************
    // START Editing assigned staff in overview_staff_controller (from select menu)
    // ***************
    
    // To remember previous value in Select and change back if already assigned
    var previous;
    $('select[id*="assigned_to_"]').on('focus', function () {
        // Store the current value on focus and on change
        previous = this.selectedIndex;
    });
    
    
    $('select[id*="assigned_to_"]').on("change", function(e){
                IdOfSelect = $(this).attr('id');                                    // gives "assigned_to_XX"
                id_hired_staff = $(this).closest('tr').attr('data-id_hired_staff'); // The ID of the row, or ID of hired staff
                type = $(this).closest('tr').attr('data-type'); // The ID of the row, or ID of hired staff
                position = $(this).closest('tr').attr('data-position');
                var idOfSelectedOption = $("#"+IdOfSelect).val();                   // The value of the selected option, which id ID of the item (slope, lift, building...)
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: Settings.base_url+"overview_staff_controller/edit_assigned_item",
                    data: "id_hired_staff="+id_hired_staff+"&idOfSelectedOption="+idOfSelectedOption+"&type="+type+"&position="+position,
                    success: function(result){
                        if (result.returned == true){
                            var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.updated+'"><i class="fa-solid fa-check"></i></div>';
                            $('#result_'+IdOfSelect).html(success_image); 
                                            $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                            $(refresh_achievements_sidebar);  // Calls refresh_achievements_sidebar after the achievement is completed
                        }
                        else {
                            var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.not_updated_already_assigned+'"><i class="fa-solid fa-xmark" style="color: rgb(212, 30, 30);"></i></div>';
                            $('#result_'+IdOfSelect).html(success_image); 
                                            $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                            document.getElementById(IdOfSelect).selectedIndex = previous;
                        } 
                    },
                    error: function(result){
                        var success_image = ' <div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'+Settings.not_updated_already_assigned+'"><i class="fa-solid fa-xmark" style="color: rgb(212, 30, 30);"></i></div>';
                        $('#result_'+IdOfSelect).html(success_image); 
                                    $('#result_'+IdOfSelect).fadeIn().delay(5000).fadeOut();  // Only display for 5 sec
                        document.getElementById(IdOfSelect).selectedIndex = previous;
                    }
                });
        });
    // ***************
    // END Editing assigned staff in overview_staff_controller (from select menu)
    // ***************
    
    // ***************
    // START Warning if firing staff
    // ***************
 
    if ($.fn.button) {
    $('a[class*="delete-dialog"]')  // Any "<a>" tag with a class containing *delete-dialog*
    .button({
        icons: { primary: 'ui-button-text-only'  },
        text: 'false'
    });
    }

    $(document).on('click' , 'a.delete-dialog', function(){
        // Get the values from the data attributes of the <tr>
        friendly_name = $(this).closest('tr').attr('data-friendly_name'); 
        id_hired_staff = $(this).closest('tr').attr('data-id_hired_staff'); 
        salary = $(this).closest('tr').attr('data-salary');
        currentResortId = $(this).closest('tr').attr('data-currentResortId');
        // Pass the variables into the dialog
        $("#dialog-confirm").data('del-friendly_name', friendly_name).dialog('open');
        $("#dialog-confirm").data('del-id_hired_staff', id_hired_staff).dialog('open');
        $("#dialog-confirm").data('del-salary', salary).dialog('open');
        $("#dialog-confirm").data('del-currentResortId', currentResortId).dialog('open');
        return false;
   });

    // Initiating the Fire/Cancel buttons
    var buttonsOpts = {}
    // Creating the Fire button
    buttonsOpts[Settings.fire_staff] = function() {
        // Getting variables from the dialog
        var friendly_name = $(this).data('del-friendly_name');
        var id_hired_staff = $(this).data('del-id_hired_staff');
        var salary = $(this).data('del-salary');
        var currentResortId = $(this).data('del-currentResortId');
        // If called, will fire the employee in fire_staff controller
        $.ajax({
            type: "POST",
            url: Settings.base_url+"overview_staff_controller/fire_staff",
            data: "currentResortId="+currentResortId+"&id_hired_staff="+id_hired_staff+"&salary="+salary+"&friendly_name="+friendly_name,
            success: function(result){
                smToast(Settings.staff_fired, 'success');
                $("table").find("tr[data-id_hired_staff='" + id_hired_staff + "']").hide();     // Hides the deleted row in the table
                update_cash_player();
            }
        });
        $(this).dialog('close');
    }
    // Creating the Cancel button
    buttonsOpts[cancelLabel] = function() {
        $(this).dialog('close');
    }

    // Parameters of the dialog
    if ($.fn.dialog) {
    $( "#dialog-confirm" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOpts
    });
    }

    // ***************
    // END Warning if firing staff
    // ***************

    // ***************
    // START Train staff button
    // ***************

    $(document).on('click', '.btn-train', function () {
        if (typeof TrainStaff === 'undefined') { return; }
        var $btn    = $(this);
        var staffId = $btn.data('id');
        var $result = $('#train-result-' + staffId);

        $btn.prop('disabled', true);
        $result.html('<span class="text-muted"><i class="fa-regular fa-hourglass-split"></i></span>');

        $.ajax({
            url:  TrainStaff.url,
            type: 'POST',
            data: { id_hired_staff: staffId },
            success: function (resp) {
                var data = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (data.success) {
                    var msg = TrainStaff.msgSuccess;
                    if (data.leveled_up) {
                        msg += ' 🎉 ' + TrainStaff.msgLevelUp + ' ' + data.new_skill_level + '!';
                        // Update star display in the skill_level cell
                        var stars = '';
                        for (var i = 0; i < data.new_skill_level; i++)  stars += '★';
                        for (var j = data.new_skill_level; j < TrainStaff.maxLevel; j++) stars += '☆';
                        $btn.closest('tr').find('.skill-stars').text(stars);
                    }
                    $result.html('<span class="text-success small">' + msg + '</span>');
                    // Update sidebar cash display if function exists
                    if (typeof update_sidebar_cash === 'function') {
                        update_sidebar_cash(data.new_cash);
                    }
                    // Disable button if now at max level, otherwise re-enable
                    if (data.new_skill_level >= TrainStaff.maxLevel) {
                        $btn.prop('disabled', true).attr('title', TrainStaff.msgMax);
                    } else {
                        $btn.prop('disabled', false);
                    }
                } else {
                    var errMsg = TrainStaff.msgError;
                    if (data.reason === 'max_level')            errMsg = TrainStaff.msgMax;
                    else if (data.reason === 'cooldown')        errMsg = TrainStaff.msgCooldown + (data.next_training ? ' (' + data.next_training + ')' : '');
                    else if (data.reason === 'not_enough_cash') errMsg = TrainStaff.msgCash;
                    $result.html('<span class="text-danger small">' + errMsg + '</span>');
                    $btn.prop('disabled', false);
                }
            },
            error: function () {
                $result.html('<span class="text-danger small">' + TrainStaff.msgError + '</span>');
                $btn.prop('disabled', false);
            }
        });
    });

    // ***************
    // END Train staff button
    // ***************


    // ***************
    // START Warning popup if sell equipment
    // ***************
    
    if ($.fn.button) {
    $('a[class*="sellequip-dialog"]')  // Any "<a>" tag with a class containing *delete-dialog*
    .button({
        icons: { primary: 'ui-button-text-only'  },
        text: 'false'
    });
    }
    
    $(document).on('click' , 'a.sellequip-dialog', function(){       // Any "<a>" tag with a class containing *sellequip-dialog*
        // Get the values from the data attributes of the <tr>
        equipment_type = $(this).closest('div').attr('data-equipment_type');                   // groomer or skibus
        currentResortId = $(this).closest('div').attr('data-currentResortId');
        type = $(this).closest('div').attr('data-type');                         // 1 for groomer or 2 for skibus
        i = $(this).closest('div').attr('data-i');                         // count?
        // The lines below define which dialog popup to open
       
        // Pass the variables into the related dialog
        $("#dialog-confirm-sellequip").data('del-equipment_type', equipment_type).dialog('open');
        $("#dialog-confirm-sellequip").data('del-currentResortId', currentResortId).dialog('open');
        $("#dialog-confirm-sellequip").data('del-type', type).dialog('open');
        $("#dialog-confirm-sellequip").data('del-i', i).dialog('open');
        return false;
   });
   
   // Initiating the Sell Equipment/Cancel buttons
    var buttonsOptsSellEquip = {};       // For the sell equipment button
    // Creating the Sell button
    buttonsOptsSellEquip[Settings.sell_item] = function() {
        // Getting variables from the dialog
        var equipment_type = $(this).data('del-equipment_type');
        var currentResortId = $(this).data('del-currentResortId');
        var type = $(this).data('del-type');
        var i = $(this).data('del-i');
        // If called, will sell the equipment in sell_equipment groomer/skibus_controller
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url+""+equipment_type+"_controller/sell_equipment",
            data: "currentResortId="+currentResortId+"&equipment_type="+equipment_type+"&type="+type+"&i="+i,
            success: function(result){
                if (result.returned == true) {
                    smToast(Settings.equipment_sold, 'success');
                    $('#quantity_'+i).html(result.left_level);
                    update_cash_player();
                }
                else {
                    smToast(Settings.equipment_not_sold, 'error');
                }
            }
        });
        $(this).dialog('close');
    }
    // Creating the Cancel button
    buttonsOptsSellEquip[cancelLabel] = function() {
        $(this).dialog('close');
    }
    
    // Parameters of the Sell dialog
    if ($.fn.dialog) {
    $( "#dialog-confirm-sellequip" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOptsSellEquip
    });
    }
    
    
    
    // ***************
    // START Warning popup if repair lift
    // ***************
    
    if ($.fn.button) {
    $('a[class*="repair_lift-dialog"]')  // Any "<a>" tag with a class containing *repair_lift-dialog*
    .button({
        icons: { primary: 'ui-button-text-only'  },
        text: 'false'
    });
    }
    
    $(document).on('click' , 'button.repair_lift-dialog', function(){       // Any "<a>" tag with a class containing *repair_lift-dialog*
        // Get the values from the data attributes of the <div>
        id_item = $(this).closest('a').attr('data-id_item');               
        id_group = $(this).closest('a').attr('data-id_group');                  
        currentResortId = $(this).closest('a').attr('data-currentResortId');
        // The lines below define which dialog popup to open
       
        // Pass the variables into the related dialog
        $("#dialog-confirm-repair_lift").data('del-id_item', id_item).dialog('open');
        $("#dialog-confirm-repair_lift").data('del-id_group', id_group).dialog('open');
        $("#dialog-confirm-repair_lift").data('del-currentResortId', currentResortId).dialog('open');
        return false;
   });
   
   // Initiating the repair lift/Cancel buttons
    var buttonsOpts_repair_lift = {};       // For the repair lift button
    // Creating the Sell button
    buttonsOpts_repair_lift[Settings.repair_lift] = function() {
        // Getting variables from the dialog
        var id_item = $(this).data('del-id_item');
        var id_group = $(this).data('del-id_group');
        var currentResortId = $(this).data('del-currentResortId');
        // If called, will repair the lift repair_lift lift_controller
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url+"lift_controller/repair_lift",
            data: "currentResortId="+currentResortId+"&id_item="+id_item+"&id_group="+id_group,
            success: function(result){
                if (result.returned == true) {
                    smToast(Settings.lift_repaired, 'success');
                    $('#status_field_button').html('');                   
                    $('#status_field').html(Settings.building_status_to_show_under_maintenance);                   
                    update_cash_player();
                }
                else {
                    if (result.status == 'not_enough_money')
                        smToast(Settings.not_enough_money, 'error');
                    else if (result.status == 'not_completed')
                        smToast(Settings.not_completed, 'warning');
                    else if (result.status == 'no_mechanics')
                        smToast(Settings.no_mechanics, 'error');
                }
            }
        });
        $(this).dialog('close');
    }
    // Creating the Cancel button
    buttonsOpts_repair_lift[cancelLabel] = function() {
        $(this).dialog('close');
    }
    
    // Parameters of the Repair dialog
    if ($.fn.dialog) {
    $( "#dialog-confirm-repair_lift" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOpts_repair_lift
    });
    }
    
    
    $(document).on("click", 'button.start_tournament_button', function(e){
        if ($(this).is(':disabled')) {
            return false;
        }
        id_tournament = $(this).closest('tr').attr('data-id_tournament'); 
       $.ajax({
                type: "POST",
                dataType: "json",
                url: Settings.base_url+"tournaments_controller/start_tournament",
                data: "id_tournament="+id_tournament,
                success: function(result){
                    if (result.started === true){
                        $('#start_button_column-'+id_tournament).html(result.start_button_cell);
                        $('#lastTournamentTable').html(result.lastTournamentTable);
                        $('button[class*="start_tournament_button"]').prop('disabled', true); // Disable all the start buttons
                        update_cash_player();
                    } 
                    else if (result.started === false){
                        smToast(Settings.ongoing_tournament, 'error');
                    } 
                }
            });
            return false;
   });
    
    
    // ***************
    // START Special Event button handler
    // ***************
    $(document).on("click", 'button.start_special_event_button', function(e){
        if ($(this).is(':disabled')) {
            return false;
        }
        var id_special_event = $(this).closest('tr').attr('data-id_special_event');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url + "special_events_controller/start_special_event",
            data: "id_special_event=" + id_special_event,
            success: function(result){
                if (result.started === true){
                    $('#start_event_button_column-' + id_special_event).html(result.start_button_cell);
                    $('#lastEventTable').html(result.lastEventTable);
                    $('button[class*="start_special_event_button"]').prop('disabled', true);
                    update_cash_player();
                } else if (result.started === false){
                    smToast(Settings.ongoing_special_event || Settings.ongoing_tournament, 'error');
                }
            }
        });
        return false;
    });


    // ***************
    // START Send friends invite
    // ***************
    $('input[id="submit_invite_friends"]').on("click", function(e){
        var name = $( "#name" ).val();
        var email = $( "#email" ).val();
        var friend1 = $( "#friend1" ).val();
        var friend2 = $( "#friend2" ).val();
        var friend3 = $( "#friend3" ).val();
        var invite_friends = $( 'input[name="invite_friends"]' ).val();
       $.ajax({
                type: "POST",
                dataType: "json",
                url: Settings.base_url+"genepis_controller/invite_friends",
                data: "name="+name+"&email="+email+"&friend1="+friend1+"&friend2="+friend2+"&friend3="+friend3+"&invite_friends="+invite_friends,
                success: function(result){
                    if (result.valid === true){
                        $('#result_invite').html(result.data);
                        $('#signup_error_name').html('');
                        $('#signup_error_email').html('');
                        $('#signup_error_friend1').html('');
                        $('#signup_error_friend2').html('');
                        $('#signup_error_friend3').html('');
                    } 
                    if (result.valid === false){
                        $('#result_invite').html(result.data);
                        if (result.errors) {
                            $('#signup_error_name').html(result.errors.signup_error_name);
                            $('#signup_error_email').html(result.errors.signup_error_email);
                            $('#signup_error_friend1').html(result.errors.signup_error_friend1);
                            $('#signup_error_friend2').html(result.errors.signup_error_friend2);
                            $('#signup_error_friend3').html(result.errors.signup_error_friend3);
                        }
                    } 
                }
            });
            return false;
   });


    // ***************
    // END Send friends invite
    // ***************
    
    
    
   
   
    // ***************
    // START Warning popup if sell/destroy item
    // ***************
    $(document).on('click' , 'a.sell-dialog', function(){       // Any "<a>" tag with a class containing *sell-dialog*
        // Get the values from the data attributes of the <tr>
        id_item = $(this).closest('tr').attr('data-id_item');                   // id-item is the generic ID of the slope or lift
        id_created_item = $(this).closest('tr').attr('data-id_created_item');                   // id_created_item is the id_created_item of the slope or lift
        currentResortId = $(this).closest('tr').attr('data-currentResortId');
        type = $(this).closest('tr').attr('data-type');                         // slope or lift
        friendly_name = $(this).closest('tr').attr('data-friendly_name');                         // friendly_name
        // The lines below define which dialog popup to open
        if (type == 'lift') 
            var action = 'sell';
        else if (type == 'slope')
            var action = 'destroy';
        // Pass the variables into the related dialog
        $("#dialog-confirm-"+action).data('del-id_item', id_item).dialog('open');
        $("#dialog-confirm-"+action).data('del-id_created_item', id_created_item).dialog('open');
        $("#dialog-confirm-"+action).data('del-currentResortId', currentResortId).dialog('open');
        $("#dialog-confirm-"+action).data('del-type', type).dialog('open');
        $("#dialog-confirm-"+action).data('del-friendly_name', type).dialog('open');
        return false;
   });

    // Initiating the Sell-Destroy/Cancel buttons
    var buttonsOptsSell = {};       // For the sell button (lift)
    var buttonsOptsDestroy = {};    // For the destroy button (slope)
    // Creating the Sell button
    buttonsOptsSell[Settings.sell_item] = function() {
        // Getting variables from the dialog
        var id_item = $(this).data('del-id_item');
        var id_created_item = $(this).data('del-id_created_item');
        var currentResortId = $(this).data('del-currentResortId');
        var type = $(this).data('del-type');
        // If called, will sell the lift or destroy the slope in sell_item controller
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url+"resort_controller/sell_item",
            data: "currentResortId="+currentResortId+"&id_item="+id_item+"&id_created_item="+id_created_item+"&type="+type+"&friendly_name="+friendly_name,
            success: function(result){
                if (result.returned === true) {
                    smToast(result.message, 'success');
                    $("table").find("tr[data-id_item='" + id_item + "']").html('');     // Hides the Status pictures
                    update_cash_player();
                }
                else 
                    smToast(result.message, 'error');
            }
        });
        $(this).dialog('close');
    }
    // Creating the Cancel button
    buttonsOptsSell[cancelLabel] = function() {
        $(this).dialog('close');
    }

    // Since the Sell and Destroy buttons are the same but they need a different name (in order to call the right popup), we set the destroy_item with the same parameters as sell_item. Same for the Cancel buttons.
    buttonsOptsDestroy[Settings.destroy_item] = buttonsOptsSell[Settings.sell_item];                
    buttonsOptsDestroy[cancelLabel] = buttonsOptsSell[cancelLabel];
    
    // Parameters of the Sell dialog
    if ($.fn.dialog) {
    $( "#dialog-confirm-sell" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOptsSell
    });
    // Parameters of the Destroy dialog
    $( "#dialog-confirm-destroy" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOptsDestroy
    });
    }

    // ***************
    // END Warning if sell/destroy item
    // ***************
    
    
    // ***************
    // START Warning popup if signup loan clicked
    // ***************
    $(document).on('click' , 'a.signup_loan-dialog', function(){       // Any "<a>" tag with a class containing *signup_loan-dialog*
        // Get the values from the data attributes of the <tr>
        id_bank = $(this).closest('td').attr('data-id_bank');                   // id_bank is the generic bank ID (1, 2, 3)
        bank_name = $(this).closest('td').attr('data-bank_name');                   // Bank name
        to_borrow = $("#to_borrow_SliderVal").html();                                      // Amount to borrow
        loan_duration = $("#loan_duration_SliderVal").html();                              // Loan duration in days
        id_bank_table = id_bank-1;                                              // The bank ID in the table is -1 compared to the DB IDs. Adjustment is made here
        daily_payment = [];                                               // Initiating monthly payment array        
        daily_payment[id_bank_table] = $("#daily_payment_"+id_bank_table).html();       // picking up monthly payment depending on which button was clicked
        daily_payment_clicked = Number( daily_payment[id_bank_table].replace(/[^0-9\.]+/g,""));      // Converting monthly payment amount into number (removing € and spaces)
        $('#dialog-confirm-signup_loan').html(Settings.confirm_signup_do_you_want+' '+bank_name+' '+Settings.for+' '+to_borrow+' € '+Settings.with_daily_payment+' '+daily_payment[id_bank_table]+' '+Settings.during+' '+loan_duration+' '+Settings.days+'?');
         
        // Pass the variables into the related dialog
        $("#dialog-confirm-signup_loan").data('del-id_bank', id_bank).dialog('open');
        $("#dialog-confirm-signup_loan").data('del-loan_duration', loan_duration).dialog('open');
        $("#dialog-confirm-signup_loan").data('del-bank_name', bank_name).dialog('open');
        $("#dialog-confirm-signup_loan").data('del-to_borrow', to_borrow).dialog('open');
        $("#dialog-confirm-signup_loan").data('del-daily_payment', daily_payment[id_bank_table]).dialog('open');
        return false;
   });

    // Initiating the signup_loan/Cancel buttons
    var buttonsOptsSignup_loan = {};       // For the signup_loan button 
    // Creating the signup_loan button
    var signUpLabel = Settings.sign_up || 'Sign up';
    buttonsOptsSignup_loan[signUpLabel] = function() {
        // Getting variables from the dialog
        var id_bank = $(this).data('del-id_bank');
        var loan_duration = $(this).data('del-loan_duration');
        var bank_name = $(this).data('del-bank_name');
        var to_borrow = $(this).data('del-to_borrow');
        var to_borrow_number = Number( to_borrow.replace(/[^0-9\.]+/g,""));      // Converting monthly payment amount into number (removing € and spaces)
        var daily_payment = $(this).data('del-daily_payment');
        // If called, will signup_loan the loan in signup_loan controller
        $.ajax({
            type: "POST",
            url: Settings.base_url+"bank_controller/signup_loan",
            data: "id_bank="+id_bank+"&loan_duration="+loan_duration+"&to_borrow="+to_borrow_number,
            dataType: "json",
            success: function(result){
                if (result.signed == true){
                    smToast(Settings.loan_signed_up, 'success');
                    $('#ongoing_loans_table').html(result.ongoing_loans_table);
                    update_cash_player();
                    update_genepis();
                }
                else if (result.signed == false){
                    smToast(result.message, 'error');
                }
                else {
                    smToast(Settings.loan_not_signed_up, 'error');
                }
            }
        });
        $(this).dialog('close');
    }
    // Creating the Cancel button
    buttonsOptsSignup_loan[cancelLabel] = function() {
        $(this).dialog('close');
    }
    
    // Parameters of the signup_loan dialog
    if ($.fn.dialog) {
    $( "#dialog-confirm-signup_loan" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOptsSignup_loan
    });
    }

    // ***************
    // END Warning if signup loan clicked
    // ***************
    
    
    
    
    // ***************
    // START Warning popup if payoff loan clicked
    // ***************
    $(document).on('click' , 'a.payoff_loan-dialog', function(){       // Any "<a>" tag with a class containing *payoff_loan-dialog*
        // Get the values from the data attributes of the <tr>
        id_loan = $(this).closest('td').attr('data-id_loan');                   // id_loan to payoff the correct loan
        left_to_pay = $(this).closest('td').attr('data-left_to_pay');                   // left_to_pay amount to be used in popup dialog
        left_to_pay_float = parseFloat(left_to_pay).toFixed(0);
        var remainder = left_to_pay_float.length % 3;
        left_to_pay_display = (left_to_pay.substr(0, remainder) + left_to_pay.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();   // Friendly format with thousand space separator   
        $('#dialog-confirm-payoff_loan').html(Settings.confirm_payoff_do_you_want+' '+left_to_pay_display+' € '+Settings.will_be_directly_taken);
         
        // Pass the variables into the related dialog
        $("#dialog-confirm-payoff_loan").data('del-id_loan', id_loan).dialog('open');
        $("#dialog-confirm-payoff_loan").data('del-left_to_pay', left_to_pay).dialog('open');
        return false;
   });

    // Initiating the payoff_loan/Cancel buttons
    var buttonsOptsPayoff_loan = {};       // For the payoff_loan button 
    // Creating the payoff_loan button
    var payoffLabel = Settings.payoff || 'Pay off';
    buttonsOptsPayoff_loan[payoffLabel] = function() {
        // Getting variables from the dialog
        var id_loan = $(this).data('del-id_loan');
        // If called, will payoff_loan the loan in payoff_loan controller
        $.ajax({
            type: "POST",
            url: Settings.base_url+"bank_controller/payoff_loan",
            data: "id_loan="+id_loan,
            dataType: "json",
            success: function(result){
                if (result.payed_off == true){
                    smToast(result.message, 'success');
                    $('#ongoing_loans_table').html(result.ongoing_loans_table);
                    update_cash_player();
                }
                else if (result.payed_off == false){
                    smToast(result.message, 'error');
                }
                else {
                    smToast(Settings.loan_not_payed_off, 'error');
                }
            }
        });
        $(this).dialog('close');
    }
    // Creating the Cancel button
    buttonsOptsPayoff_loan[cancelLabel] = function() {
        $(this).dialog('close');
    }
    
    // Parameters of the payoff_loan dialog
    if ($.fn.dialog) {
    $( "#dialog-confirm-payoff_loan" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOptsPayoff_loan
    });
    }
    
    // ***************
    // END Warning if payoff loan clicked
    // ***************



    // ***************
    // START Investment deposit dialog
    // ***************
    $(document).on('click', 'a.deposit_investment-dialog', function(){
        var inv_amount = parseInt($("#investment_amount_val").text().replace(/[^0-9]/g, ''), 10) || Settings.investment_min_deposit_raw;
        var fmt = inv_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        $('#dialog-confirm-deposit_investment').html(Settings.investment_confirm_deposit + ' ' + fmt + ' € ?');
        $("#dialog-confirm-deposit_investment").data('inv-amount', inv_amount).dialog('open');
        return false;
    });

    var buttonsOptsDeposit = {};
    var depositLabel = (typeof Settings.investment_deposit_label !== 'undefined') ? Settings.investment_deposit_label : 'Deposit';
    buttonsOptsDeposit[depositLabel] = function() {
        var amount = $(this).data('inv-amount');
        $.ajax({
            type: 'POST',
            url: Settings.base_url + 'bank_controller/deposit_investment',
            data: 'amount=' + amount,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    smToast(result.message, 'success');
                    $('#investment_balance_display').text(result.new_balance_fmt);
                    Settings.investment_balance = result.new_balance;
                    update_cash_player();
                } else {
                    smToast(result.message, 'error');
                }
            }
        });
        $(this).dialog('close');
    };
    buttonsOptsDeposit[cancelLabel] = function() { $(this).dialog('close'); };
    if ($.fn.dialog) {
    $('#dialog-confirm-deposit_investment').dialog({
        resizable: false, height: 250, modal: true, autoOpen: false,
        buttons: buttonsOptsDeposit
    });
    }
    // ***************
    // END Investment deposit dialog
    // ***************



    // ***************
    // START Investment withdraw dialog
    // ***************
    $(document).on('click', 'a.withdraw_investment-dialog', function(){
        var balance = Settings.investment_balance || 0;
        var fmt = balance.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        $('#dialog-confirm-withdraw_investment').html(Settings.investment_confirm_withdraw + ' ' + fmt + ' € ?');
        $("#dialog-confirm-withdraw_investment").data('inv-amount', balance).dialog('open');
        return false;
    });

    var buttonsOptsWithdraw = {};
    var withdrawLabel = (typeof Settings.investment_withdraw_label !== 'undefined') ? Settings.investment_withdraw_label : 'Withdraw';
    buttonsOptsWithdraw[withdrawLabel] = function() {
        var amount = $(this).data('inv-amount');
        $.ajax({
            type: 'POST',
            url: Settings.base_url + 'bank_controller/withdraw_investment',
            data: 'amount=' + amount,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    smToast(result.message, 'success');
                    $('#investment_balance_display').text(result.new_balance_fmt);
                    Settings.investment_balance = result.new_balance;
                    update_cash_player();
                } else {
                    smToast(result.message, 'error');
                }
            }
        });
        $(this).dialog('close');
    };
    buttonsOptsWithdraw[cancelLabel] = function() { $(this).dialog('close'); };
    if ($.fn.dialog) {
    $('#dialog-confirm-withdraw_investment').dialog({
        resizable: false, height: 250, modal: true, autoOpen: false,
        buttons: buttonsOptsWithdraw
    });
    }
    // ***************
    // END Investment withdraw dialog
    // ***************



    // ***************
    // START Warning popup if subscribe 14 day weather forecast clicked
    // ***************
    $(document).on('click' , 'a.signup_forecast-dialog', function(){    
        // Open the confirmation dialog without passing any variable
        $("#dialog-confirm_14day_forecast").dialog('open');
        return false;           // Do no validate the form      
   });

    // Initiating the signup_loan/Cancel buttons
    var buttonsOptsSignup_forecast = {};       // For the signup_loan button 
    // Creating the signup_loan button
    var subscribeLabel = Settings.subscribe || 'Subscribe';
    buttonsOptsSignup_forecast[subscribeLabel] = function() {      
        $.ajax({
            type: "POST",
            url: Settings.base_url+"weather_controller/subscribe_forecast",
            dataType: "json",
            success: function(result){
                if (result.subscribed === true){
                    smToast(result.message, 'success');
                    $.ajax({ 
                        type: "POST",
                        dataType: "json",
                        url: Settings.base_url+"weather_controller/build_forecast_table",
                        success: function(result){
                            table = result.table;
                            $('#forecast_table').html(table);
                        }
                    });
                    update_genepis();
                }
                else if (result.subscribed === false){
                    smToast(result.message, 'error');
                }
                else {
                    smToast(Settings.forecast_not_subscribed, 'error');
                }
            }
        });
        $(this).dialog('close');
    }
    // Creating the Cancel button
    buttonsOptsSignup_forecast[cancelLabel] = function() {
        $(this).dialog('close');
    }
    
    // Parameters of the signup_loan dialog
    if ($.fn.dialog) {
    $( "#dialog-confirm_14day_forecast" ).dialog({
        resizable: false,
        height:330,
        modal: true,
        autoOpen:false,
        buttons : buttonsOptsSignup_forecast
    });
    }

    // ***************
    // END Warning if subscribe 14 day weather forecast clicked
    // ***************
    
    
    
    
    
    
    
    
    
    
    
    
    // ***************
    // START Scrolling and Bootstrap Table tabs
    // ***************
    if ($('.hire-staff-table').length > 0) {
        $('[data-bs-toggle="tab"]').on( 'shown.bs.tab', function (e) {
            $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        } );
        
        $('.hire-staff-table').each(function() {
            initializeTable($(this), $(this).data('staff-type'));
        });
    }
    // ***************
    // END Scrolling and Bootstrap Table tabs
    // ***************

    // ***************
    // START Candidate Pool: contract selector + refresh pool
    // ***************
    // Update hire link when player changes contract duration selector
    $(document).on('change', '.contract-select', function () {
        var $card     = $(this).closest('.candidate-card');
        var $btn      = $card.find('.btn-hire-candidate');
        var minMonths = parseInt($btn.data('min-contract'), 10) || 3;
        var chosen    = parseInt($(this).val(), 10);
        if (chosen < minMonths) chosen = minMonths;
        var id        = $btn.data('id');
        $btn.attr('href', Settings.base_url + 'hire_staff_controller/hire_from_candidate/' + id + '/' + chosen);
    });

    // Refresh candidate pool for a position
    $(document).on('click', '.btn-refresh-pool', function () {
        var position = $(this).data('position');
        if (!confirm(Settings.refresh_confirm)) return;
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url:      Settings.base_url + 'hire_staff_controller/refresh_candidates',
            type:     'POST',
            dataType: 'json',
            data:     { position: position },
            success: function (res) {
                if (res.returned) {
                    // Reload page to show fresh candidate cards
                    location.reload();
                } else {
                    if (res.message === 'not_enough_money') {
                        smToast(Settings.not_enough_money, 'error');
                    } else if (res.message === 'invalid_position') {
                        smToast(res.message, 'error');
                    } else {
                        smToast(res.message, 'error');
                    }
                    $btn.prop('disabled', false);
                }
            },
            error: function () {
                $btn.prop('disabled', false);
            }
        });
    });
    // ***************
    // END Candidate Pool
    // ***************
    
    
    // ***************
    // START Scrolling and Bootstrap Table tabs - LOGS table
    // ***************

    /* Helper: remove the skeleton placeholder and reveal the table container.
       skeletonId  – ID of the skeleton div to remove (e.g. 'logs-skeleton')
       wrapperId   – ID or jQuery selector of the element whose d-none class is
                     removed (pass null when the target is the table itself)
       tableSelector – jQuery selector of the <table> whose d-none class is
                     removed (pass null when using wrapperId instead) */
    function revealTableAfterLoad(skeletonId, wrapperId, tableSelector) {
        $('#' + skeletonId).remove();
        if (wrapperId)      { $('#' + wrapperId).removeClass('d-none'); }
        if (tableSelector)  { $(tableSelector).removeClass('d-none'); }
    }
      
    if ($('#myTableLogs').length > 0) {
        $('[data-bs-toggle="tab"]').on( 'shown.bs.tab', function (e) {
            $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        } );
        $.ajax({
            url: Settings.base_url+"logs_controller/getDataTable",
            method: 'post',
            dataType: 'json',
            success: function (data) {
                data = data || [];
                revealTableAfterLoad('logs-skeleton', null, '#myTableLogs');
                var table = $('#myTableLogs').DataTable({
                    paging: true,
                    pageLength: 50,
                    searching: true,
                    data: data,
                    ordering: true,
                    order: [[ 0, "desc" ]],
                    columns: [
                        { "data": "datetime"},
                        { "data": "type"},
                        { "data": "data",
                            "render": function (data, type, full) {
                                if(full.unread == 1){
                                    return full.data + '<span class="align_right"><img src="'+asset_url('img/icons/new.png')+'" /></span>';
                                }
                                else {
                                    return full.data;
                                }
                            }}
                    ]
                });
                $('#myTableLogs_filter').switchClass( 'dataTables_filter', 'dataTables_filter_show' );
                $.ajax({
                    type: "POST",
                    url: Settings.base_url+"logs_controller/change_read_status"
                });
            }
        });
    }
    // ***************
    // END Scrolling and Bootstrap Table tabs - LOGS table
    // ***************
    
    
    
    // ***************
    // START Scrolling and Bootstrap Table tabs - LEADERBOARD table
    // ***************

    if ($('#myTableLeaderboard').length > 0) {
        var _lbApiRegistered = false;
        $('[data-bs-toggle="tab"]').on( 'shown.bs.tab', function (e) {
            $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        } );
        $.ajax({
            url: Settings.base_url+"leaderboard/data",
            method: 'post',
            dataType: 'json',
            success: function (values) {
                values = values || {};
                var leaderboardData = values.data || [];
                var leaderboardDisplayStart = parseInt(values.displayStart, 10);
                if (isNaN(leaderboardDisplayStart)) {
                    leaderboardDisplayStart = 0;
                }
                revealTableAfterLoad('lb-global-skeleton', 'lb-global-wrapper', null);
                if (!_lbApiRegistered) {
                    _lbApiRegistered = true;
                    jQuery.fn.dataTable.Api.register( 'page.jumpToData()', function ( data, column ) {
                        var pos = this.column(column, {order:'current'}).data().indexOf( data );
                        if ( pos >= 0 ) {
                            var page = Math.floor( pos / this.page.info().length );
                            this.page( page ).draw( false );
                        }
                        return this;
                    } );
                    $.fn.dataTable.defaults.column.orderSequence = ['desc', 'asc'];
                }
                var table = $('#myTableLeaderboard').DataTable({
                    paging: true,
                    pageLength: 25,
                    sort: true,
                    searching: true,
                    data: leaderboardData,
                    displayStart: leaderboardDisplayStart,
                    ordering: true,
                    language : {
                        decimal: ',',
                        thousands: ' '
                    },
                    order: [[ 3, "desc" ]],
                    columns: [
                        { "data": "ranking", "orderable": false },
                        { "data": "username", "width": "50" },
                        { "data": "resort_name" },
                        { "data": "reputation",
                            "render": function (data, type, full) {
                               if(type === 'display'){
                                    player_reputation_float = parseFloat(full.reputation).toFixed(0);
                                    var remainder = player_reputation_float.length % 3;
                                    player_reputation = (player_reputation_float.substr(0, remainder) + player_reputation_float.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
                                    return player_reputation;
                                }
                               else {
                                   return full.reputation;
                               }
                            }},
                        { "data": "prestige",
                            "render": function (data, type, full) {
                               if(type === 'display'){
                                    player_prestige_float = parseFloat(full.prestige).toFixed(0);
                                    var remainder = player_prestige_float.length % 3;
                                    player_prestige = (player_prestige_float.substr(0, remainder) + player_prestige_float.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
                                    return player_prestige;
                                }
                               else {
                                   return full.prestige;
                               }
                            }},
                        { "data": "cash",
                            "render": function (data, type, full) {
                               if(type === 'display'){
                                    player_cash_float = parseFloat(full.cash).toFixed(0);
                                    var remainder = player_cash_float.length % 3;
                                    player_cash = (player_cash_float.substr(0, remainder) + player_cash_float.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
                                    return player_cash + ' €';
                                }
                               else {
                                   return full.cash;
                               }
                            }},
                        { "data": "creation_time_resort",
                            "render": function (data, type, full) {
                               if(type === 'display'){
                                    return full.creation_time_resort + ' ' + Settings.days_ago;
                                }
                               else {
                                   return full.creation_time_resort;
                               }
                            }},
                        { "data": "lift_count" },
                        { "data": "slope_count" },
                        { "data": "staff_count" },
                        { "data": "tournament_count" },
                        { "data": "id_player", "visible": false}
                    ],
                    "createdRow": function ( row, data, index ) {
                        $currentUserId = $('#currentUserId').attr('value');
                        if ( data.id_player == $currentUserId ) {
                            $('td', row).addClass('bold');
                        }
                    }
                });
                table.on( 
                        'order.dt search.dt', function () {
                    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();
                $('#myTableLeaderboard thead').on('click', 'th', function () {   
                    table.page.jumpToData( $currentUserId, 11 );
                } );
                table.page.jumpToData( $currentUserId, 11 );
                $('#myTableLeaderboard_filter').switchClass( 'dataTables_filter', 'dataTables_filter_show' ); 
            }
        });
    }

    // ***************
    // By Region leaderboard table (loaded lazily on first tab show)
    // ***************
    if ($('#myTableLeaderboardRegion').length > 0) {
        var lbRegionLoaded = false;
        $('#lb-region-tab-radio').on('change', function () {
            if (lbRegionLoaded) return;
            lbRegionLoaded = true;
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            $.ajax({
                url: Settings.base_url + 'leaderboard/data/country',
                method: 'post',
                dataType: 'json',
                success: function (values) {
                    values = values || {};
                    if (!values.country) {
                        revealTableAfterLoad('lb-region-skeleton', null, null);
                        $('#lb-region-none').removeClass('d-none');
                        return;
                    }
                    revealTableAfterLoad('lb-region-skeleton', 'lb-region-wrapper', null);
                    $('#lb-region-intro').html('<p>' + Settings.lb_region_intro + ' <strong>' + values.country + '</strong></p>');
                    var leaderboardData = values.data || [];
                    var leaderboardDisplayStart = parseInt(values.displayStart, 10);
                    if (isNaN(leaderboardDisplayStart)) leaderboardDisplayStart = 0;
                    var tableRegion = $('#myTableLeaderboardRegion').DataTable({
                        paging: true,
                        pageLength: 25,
                        sort: true,
                        searching: true,
                        data: leaderboardData,
                        displayStart: leaderboardDisplayStart,
                        ordering: true,
                        language: { decimal: ',', thousands: ' ' },
                        order: [[3, 'desc']],
                        columns: [
                            { data: 'ranking', orderable: false },
                            { data: 'username', width: '50' },
                            { data: 'resort_name' },
                            { data: 'reputation', render: function (data, type, full) {
                                if (type === 'display') {
                                    var f = parseFloat(full.reputation).toFixed(0);
                                    var r = f.length % 3;
                                    return (f.substr(0, r) + f.substr(r).replace(/(\d{3})/g, ' $1')).trim();
                                }
                                return full.reputation;
                            }},
                            { data: 'prestige', render: function (data, type, full) {
                                if (type === 'display') {
                                    var f = parseFloat(full.prestige).toFixed(0);
                                    var r = f.length % 3;
                                    return (f.substr(0, r) + f.substr(r).replace(/(\d{3})/g, ' $1')).trim();
                                }
                                return full.prestige;
                            }},
                            { data: 'cash', render: function (data, type, full) {
                                if (type === 'display') {
                                    var f = parseFloat(full.cash).toFixed(0);
                                    var r = f.length % 3;
                                    return (f.substr(0, r) + f.substr(r).replace(/(\d{3})/g, ' $1')).trim() + ' €';
                                }
                                return full.cash;
                            }},
                            { data: 'creation_time_resort', render: function (data, type, full) {
                                if (type === 'display') return full.creation_time_resort + ' ' + Settings.days_ago;
                                return full.creation_time_resort;
                            }},
                            { data: 'lift_count' },
                            { data: 'slope_count' },
                            { data: 'staff_count' },
                            { data: 'tournament_count' },
                            { data: 'id_player', visible: false }
                        ],
                        createdRow: function (row, data) {
                            var $cuid = $('#currentUserId').attr('value');
                            if (data.id_player == $cuid) $('td', row).addClass('bold');
                        }
                    });
                    tableRegion.on('order.dt search.dt', function () {
                        tableRegion.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                            cell.innerHTML = i + 1;
                        });
                    }).draw();
                    tableRegion.page.jumpToData($('#currentUserId').attr('value'), 11);
                    $('#myTableLeaderboardRegion_filter').switchClass('dataTables_filter', 'dataTables_filter_show');
                }
            });
        });
    }

    // ***************
    // By Slopes leaderboard table (loaded lazily on first tab show)
    // ***************
    if ($('#myTableLeaderboardSlope').length > 0) {
        var lbSlopeLoaded = false;
        $('#lb-slope-tab-radio').on('change', function () {
            if (lbSlopeLoaded) return;
            lbSlopeLoaded = true;
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            $.ajax({
                url: Settings.base_url + 'leaderboard/data/slope',
                method: 'post',
                dataType: 'json',
                success: function (values) {
                    values = values || {};
                    var leaderboardData = values.data || [];
                    var leaderboardDisplayStart = parseInt(values.displayStart, 10);
                    if (isNaN(leaderboardDisplayStart)) leaderboardDisplayStart = 0;
                    revealTableAfterLoad('lb-slope-skeleton', 'lb-slope-wrapper', null);
                    var tableSlope = $('#myTableLeaderboardSlope').DataTable({
                        paging: true,
                        pageLength: 25,
                        sort: true,
                        searching: true,
                        data: leaderboardData,
                        displayStart: leaderboardDisplayStart,
                        ordering: true,
                        language: { decimal: ',', thousands: ' ' },
                        order: [[3, 'desc'], [5, 'desc']],
                        columns: [
                            { data: 'ranking', orderable: false },
                            { data: 'username', width: '50' },
                            { data: 'resort_name' },
                            { data: 'slope_count' },
                            { data: 'lift_count' },
                            { data: 'reputation', render: function (data, type, full) {
                                if (type === 'display') {
                                    var f = parseFloat(full.reputation).toFixed(0);
                                    var r = f.length % 3;
                                    return (f.substr(0, r) + f.substr(r).replace(/(\d{3})/g, ' $1')).trim();
                                }
                                return full.reputation;
                            }},
                            { data: 'prestige', render: function (data, type, full) {
                                if (type === 'display') {
                                    var f = parseFloat(full.prestige).toFixed(0);
                                    var r = f.length % 3;
                                    return (f.substr(0, r) + f.substr(r).replace(/(\d{3})/g, ' $1')).trim();
                                }
                                return full.prestige;
                            }},
                            { data: 'cash', render: function (data, type, full) {
                                if (type === 'display') {
                                    var f = parseFloat(full.cash).toFixed(0);
                                    var r = f.length % 3;
                                    return (f.substr(0, r) + f.substr(r).replace(/(\d{3})/g, ' $1')).trim() + ' €';
                                }
                                return full.cash;
                            }},
                            { data: 'creation_time_resort', render: function (data, type, full) {
                                if (type === 'display') return full.creation_time_resort + ' ' + Settings.days_ago;
                                return full.creation_time_resort;
                            }},
                            { data: 'staff_count' },
                            { data: 'tournament_count' },
                            { data: 'id_player', visible: false }
                        ],
                        createdRow: function (row, data) {
                            var $cuid = $('#currentUserId').attr('value');
                            if (data.id_player == $cuid) $('td', row).addClass('bold');
                        }
                    });
                    tableSlope.on('order.dt search.dt', function () {
                        tableSlope.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                            cell.innerHTML = i + 1;
                        });
                    }).draw();
                    tableSlope.page.jumpToData($('#currentUserId').attr('value'), 11);
                    $('#myTableLeaderboardSlope_filter').switchClass('dataTables_filter', 'dataTables_filter_show');
                }
            });
        });
    }

    // ***************
    // END Scrolling and Bootstrap Table tabs - LEADERBOARD table
    // ***************
    
    
    
    // ***************
    // START JQUERY UPDATE SKIPASS PRICE
    // ***************

    if ($('#save_skipass_price').length > 0) {
        $('#save_skipass_price').on('click', function () {
            var dailyPrice  = $('#selectDays').val();
            var weeklyPrice = $('#selectWeek').val();
            $.ajax({
                type: 'POST',
                url:  Settings.base_url + 'building_access_controller/save_skipass_prices',
                data: 'daily_price=' + dailyPrice + '&weekly_price=' + weeklyPrice,
                dataType: 'json',
                success: function (result) {
                    smToast(result.message, result.status === 'success' ? 'success' : 'error');
                },
                error: function () {
                    smToast('Save failed. Please try again.', 'error');
                }
            });
        });
    }

    // ***************
    // END JQUERY UPDATE SKIPASS PRICE
    // ***************

    // ***************
    // DYNAMIC PRICING: VIP pass + family discount + group discount save button
    // ***************
    if ($('#save_dynamic_pricing').length > 0) {
        $('#save_dynamic_pricing').on('click', function () {
            var vipPassPrice      = parseInt($('#vip_pass_price').val(),      10) || 0;
            var familyDiscountPct = parseInt($('#family_discount_pct').val(), 10) || 0;
            var groupDiscountPct  = parseInt($('#group_discount_pct').val(),  10) || 0;
            $.ajax({
                type: 'POST',
                url:  Settings.base_url + 'building_access_controller/save_dynamic_pricing',
                data: 'vip_pass_price=' + vipPassPrice + '&family_discount_pct=' + familyDiscountPct + '&group_discount_pct=' + groupDiscountPct,
                dataType: 'json',
                success: function (result) {
                    smToast(result.message, result.status === 'success' ? 'success' : 'error');
                },
                error: function () {
                    smToast('Save failed. Please try again.', 'error');
                }
            });
        });
    }
    // ***************
    // END DYNAMIC PRICING
    // ***************

    // ***************
    // PARKING FEE: save button
    // ***************
    if ($('#save_parking_fee').length > 0) {
        $('#save_parking_fee').on('click', function () {
            var parkingFee = parseInt($('#parking_fee_input').val(), 10) || 0;
            $.ajax({
                type: 'POST',
                url:  Settings.base_url + 'building_access_controller/update_parking_fee',
                data: 'parking_fee=' + parkingFee,
                dataType: 'json',
                success: function (result) {
                    smToast(result.message, result.status === 'success' ? 'success' : 'error');
                },
                error: function () {
                    smToast('Save failed. Please try again.', 'error');
                }
            });
        });
    }
    // ***************
    // END PARKING FEE
    // ***************
    
    // create one global variable for when we need to access data globally
    window.com_domain = window.com_domain || {};
    // if one click in field, select all. For textarea, the type didn't work so I use the ID instead
    $(':text,#resort_description').click(function(){
        current_input_val = $(this).val();
        $(this).select();
    }).focusout(function(){
        if ($(this).val() == '') {
            $(this).val(current_input_val);
        }
    });
    
    $(':password').focusin(function() {
        if ($(this).attr('placeholder') !== undefined) {
            $(this).removeAttr('placeholder')
        }
    });
    
    $(':password.password').focusout(function() {
        $(this).attr('placeholder', 'Password');
    });
    
    $(':password.password_confirm').focusout(function() {
        $(this).attr('placeholder', 'Confirm Password');
    });
    
    if ($('#chars').length > 0) {
        var elem = $("#chars");
        if ($('#resort_description').length > 0) {
            $("#resort_description").limiter(500, elem);
        }
        if ($('#lift_choose_name').length > 0) {
            $("#lift_choose_name").limiter(35, elem);
        }
        if ($('#slope_choose_name').length > 0) {
            $("#slope_choose_name").limiter(35, elem);
        }
        if ($('#username').length > 0) {
            $("#username").limiter(25, elem);
        }
        if ($('#email').length > 0) {
            $("#email").limiter(45, elem);
        }
        if ($('#password').length > 0) {
            $("#password").limiter(25, elem);
        }
        if ($('#password_confirm').length > 0) {
            $("#password_confirm").limiter(25, elem);
        }
        if ($('#country').length > 0) {
            $("#country").limiter(45, elem);
        }
        if ($('#age').length > 0) {
            $("#age").limiter(3, elem);
        }
    }
    
    
    


// Claim reward button click (event delegation so it works for any matching button in the DOM)
    $(document).on("click", '.claim-achievement-btn', function(e){
        var currentID = $(this).data('achievement-id');                     // gives the achievement ID
        var $clickedBtn = $(this);                                          // capture button reference for sidebar feedback
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url+"achievements_controller/claim_reward",
            data: { achievementID: currentID },
            success: function(result){
                if (result.returned == true){
                    smToast(Settings.achievement_claimed, 'success');
                    var achievementName   = $('button.claim-achievement-btn[data-achievement-id="'+currentID+'"]').data('achievement-name')   || '';
                    var achievementRarity = $('button.claim-achievement-btn[data-achievement-id="'+currentID+'"]').data('achievement-rarity') || '';
                    var $shareBtn = '';
                    if (typeof Settings.share_achievement === 'string' && Settings.share_achievement) {
                        $shareBtn = '<button type="button" class="btn btn-sm btn-outline-secondary share-achievement-btn mt-1"'
                            +' data-achievement-name="'+$('<div>').text(achievementName).html()+'"'
                            +' data-achievement-rarity="'+$('<div>').text(achievementRarity).html()+'">'
                            +'<i class="fa-solid fa-share-nodes"></i> '+Settings.share_achievement+'</button>';
                    }
                    var $status = '<div class="small_text text-center tooltip tooltip-bottom" data-tip="'+Settings.achievement_completed+'"><img height="20" width="20" src="'+Settings.base_url+'img/icons/unlocked.png'+'"/> '+result.unlocked_date+'<br>'+result.unlocked_time+'</div>'+$shareBtn;
                    $('#status-'+currentID).html($status);
                    // Update sidebar button immediately if claim was triggered from the sidebar
                    var $sidebarSpan = $clickedBtn.closest('span[id^="button-"]');
                    if ($sidebarSpan.length) {
                        $sidebarSpan.html('<div><img width="23" height="23" src="'+Settings.base_url+'img/icons/claim-grey.png"></div>');
                        var currentBadge = parseInt($('#achievements_to_claim').text()) || 0;
                        if (currentBadge > 1) {
                            $('#achievements_to_claim').text(currentBadge - 1);
                        } else {
                            $('#achievements_to_claim').closest('.button_notif').hide();
                        }
                    }
                    update_cash_player();
                    update_reputation();
                    update_genepis();
                }
                else if (result.returned == 'already_claimed'){
                    smToast(Settings.already_claimed, 'error');
                }
                else if (result.returned == 'not_completed'){
                    smToast(Settings.not_completed, 'error');
                }
                else {
                    smToast(Settings.already_claimed, 'error');
                }
                $(refresh_achievements_sidebar);  // Calls refresh_achievements_sidebar after the achievement is completed       
            },
            error: function(){
                smToast(Settings.already_claimed, 'error');
            }
        });
    });
    

 
$(refresh_achievements_sidebar);  // Calls refresh_achievements_sidebar when the page is loaded              

// Social sharing for achievements
$(document).on("click", ".share-achievement-btn", function() {
    var name   = $(this).data("achievement-name")   || '';
    var rarity = $(this).data("achievement-rarity") || '';
    var text   = (Settings.share_achievement_text || 'I just unlocked the "%name%" (%rarity%) achievement on Ski-Manager! 🎿 #SkiManager')
                    .replace('%name%', name).replace('%rarity%', rarity);
    var url = window.location.href;
    if (navigator.share) {
        navigator.share({ title: 'Ski-Manager', text: text, url: url }).catch(function(err) {
            if (err && err.name !== 'AbortError') {
                window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(text + ' ' + url), '_blank', 'noopener,noreferrer');
            }
        });
    } else {
        window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(text + ' ' + url), '_blank', 'noopener,noreferrer');
    }
});

// Social sharing for resort
$(document).on("click", ".share-resort-btn", function() {
    var name    = $(this).data("resort-name")    || '';
    var country = $(this).data("resort-country") || '';
    var text    = (Settings.share_resort_text || 'Check out my ski resort "%name%" in %country% on Ski-Manager! 🎿 #SkiManager')
                    .replace('%name%', name).replace('%country%', country);
    var url = window.location.href;
    if (navigator.share) {
        navigator.share({ title: 'Ski-Manager – ' + name, text: text, url: url }).catch(function(err) {
            if (err && err.name !== 'AbortError') {
                window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(text + ' ' + url), '_blank', 'noopener,noreferrer');
            }
        });
    } else {
        window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(text + ' ' + url), '_blank', 'noopener,noreferrer');
    }
});
                
       
    // Only allow alphanumeric, accents, dash, spaces, underscores in field
    $("#username").on("keyup focus", function() {
        $.validator.addMethod("loginRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9\-\s\_À-ÿ]+$/i.test(value);
        }, Settings.invalid_username);

        $("#signup_form").validate({
            onkeyup: function(element) {$(element).valid()},
            rules: {
                "username": {
                    required: true,
                    loginRegex: true
                }
            },
            messages: {
                "username": {
                    required: Settings.missing_username,
                    loginRegex: Settings.invalid_username
                }
            },
            errorElement : 'span',
            errorLabelContainer: '.errorTxt',
            errorClass: 'danger'
        });
    });
    
    // Only allow alphanumeric, accents, dash, spaces, underscores in field
    $("#resort_name").on("keyup focus", function() {
        $.validator.addMethod("loginRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9\-\s\_À-ÿ]+$/i.test(value);
        }, Settings.invalid_resort);

        $("#resort_form").validate({
            onkeyup: function(element) {$(element).valid()},
            rules: {
                "resort_name": {
                    required: true,
                    loginRegex: true
                }
            },
            messages: {
                "resort_name": {
                    required: Settings.missing_resort,
                    loginRegex: Settings.invalid_resort
                }
            },
            errorElement : 'span',
            errorLabelContainer: '.errorTxt',
            errorClass: 'danger'
        });
    });


    if ($('#to_borrow').length > 0) {   // Used when page ready
        sync_bank_slider_values();
        init_amount_slider_and_text();
        $("#to_borrow,#loan_duration").on("input", function() {    // When slider is changed, update all elements
            sync_bank_slider_values();
            init_amount_slider_and_text();
        });
    }

    if ($('#investment_amount').length > 0) {
        $("#investment_amount").on("input", function() {
            var v = parseInt($(this).val(), 10) || Settings.investment_min_deposit_raw;
            $("#investment_amount_val").text(v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
        });
    }


    if ($('#message_campaign').length > 0) {   // Only if on marketing page (message_campaign ID exists)
        // When clicking a row in the table, call function to update campaign message and show/hide captcha
        $(document).on('click' , 'tr.campaign_row', function(){       // Any "<div>" tag with a class containing *campaign_row*
            $id_clicked_campaign = $(this).closest('tr').attr('data-id_campaign');
            get_campaign_message($id_clicked_campaign);
            $id_selected_campaign = $("tr.selected").data('id_campaign');    // Get the id_campaign currently selected
            $('#id_selected_campaign').val($id_selected_campaign);
        });
        // When page is loaded, call function to update campaign message and show/hide captcha
        $id_selected_campaign = $("tr.selected").data('id_campaign');    // Get the id_campaign currently selected
        get_campaign_message($id_selected_campaign);
        $('#id_selected_campaign').val($id_selected_campaign);
    }
    
    
    // ***************
    // START Climate Change adaptation investment buttons
    // ***************
    $(document).on('click', '.invest-btn', function () {
        var btn = $(this);
        var investType = btn.data('type');
        btn.prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: Settings.base_url + 'climate_change_controller/invest',
            data: { invest_type: investType },
            dataType: 'json',
            success: function (resp) {
                var type = resp.success ? 'success' : 'error';
                smToast(resp.message, type);
                if (resp.success) {
                    var investedLabel = (typeof Settings.climate_invested !== 'undefined') ? Settings.climate_invested : 'Invested';
                    btn.closest('.card').addClass('border-success');
                    var $investedBtn = $('<button class="btn btn-success w-100" disabled><i class="fa-regular fa-circle-check" aria-hidden="true"></i> </button>');
                    $investedBtn.append(document.createTextNode(investedLabel));
                    btn.replaceWith($investedBtn);
                } else {
                    btn.prop('disabled', false);
                }
            },
            error: function () {
                var msg = (typeof Settings.climate_invest_failed !== 'undefined') ? Settings.climate_invest_failed : 'Investment failed. Please try again.';
                smToast(msg, 'error');
                btn.prop('disabled', false);
            }
        });
    });
    // ***************
    // END Climate Change adaptation investment buttons
    // ***************



}); // end of Document.ready


function get_campaign_message ($id_campaign) {
    $id_ongoing_campaign = $('#id_ongoing_campaign').val();
    $.ajax({
        type: "POST",
        url:Settings.base_url+'marketing_controller/get_campaign_status',
        data: "id_campaign="+$id_campaign,
        dataType:"json",
        success:function(data){
            $('#message_campaign').html(data.campaign_message); 
            if (data.display_captcha == true) {
                $("#captcha_area").html(data.captcha_data);
                $('#captcha_area').show();           // shows the captcha
                $("#captcha_area").removeClass('hidden');              // Remove class green button
            }
            else {
                $('#captcha_area').hide();           // hides the captcha    (already ran today)
            }
            update_cash_player();
            update_reputation();
            update_genepis();
        }
    }); 
}

// Start of Bank sliders functions
function format_number_with_spaces(value) {
    var value_string = String(parseInt(value, 10) || 0);
    var remainder = value_string.length % 3;
    return (value_string.substr(0, remainder) + value_string.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
}

function sync_bank_slider_values() {
    to_borrow_value = parseInt($('#to_borrow').val(), 10) || 0;
    to_borrow_display = format_number_with_spaces(to_borrow_value);
    loan_duration_display = parseInt($('#loan_duration').val(), 10) || 0;
}

function init_amount_slider_and_text() {
    $("body").addClass('no_select'); 
    //Displays the friendly format of the amount to borrow in the text next to the slider? or in tooltip?
    $("#to_borrow_SliderVal").text(to_borrow_display);
    $("#loan_duration_SliderVal").text(loan_duration_display);
    // Calculate real interest rate for each bank (calculation based on 12 paymentsd per year)
    var min_loan = []; 
    var max_loan = [];
    var interest_rate = [];
    //var interest_rate2 = [];
    var daily_payment = [];
    //var daily_payment2 = [];
    var integer_daily_payment = [];
    var daily_payment_display = [];
    
    var interest_rate_per_period = [];
    var periods_per_year = 365;
    
    for (var i = 0; i < 3; i++) {
        //interest_rate[i] = $('#interest_rate_'+[i]).text()/100/12;
        //console.log('i: '+i)
        //console.log('interest_rate: '+interest_rate[i]);
        // Calculate the monthly payment based on interest rate, borrowed amount and duration
        daily_payment[i] = to_borrow_value * interest_rate[i] / (1 - (Math.pow(1/(1 + interest_rate[i]), loan_duration_display)));
        //console.log('daily_payment: '+daily_payment[i]);
        // Retrieves the min and max loan value for each bank. Retrieved via a min_loan data field in each column/row 
        //console.log('-----');
        interest_rate[i] = $('#interest_rate_'+[i]).text();
        //console.log('interest_rate2: '+interest_rate2[i]);
        
        interest_rate_per_period[i] = interest_rate[i]/periods_per_year/100;
        
        daily_payment[i] = to_borrow_value * interest_rate_per_period[i] / ( 1 - (Math.pow ( (1 + interest_rate_per_period[i]) , (loan_duration_display * -1))));
        //console.log('detail = '+to_borrow_value+'*'+interest_rate_per_period[i]+' / ( 1 - (Math.pow ( (1 + '+interest_rate_per_period[i]+') , '+loan_duration_display+'* -1)))');
        //console.log('interest_rate_per_period: '+interest_rate_per_period[i]);
        //console.log('loan_duration_display: '+loan_duration_display);

        min_loan[i] = $('#min_loan_'+[i]).attr('data-min_loan');
        max_loan[i] = $('#max_loan_'+[i]).attr('data-max_loan');
        // Dynamically change result area (monthly payment) depending on borrowed amount
        if (to_borrow_value < min_loan[i]) {                             // Case with too low amount
            $("#daily_payment_"+[i]).text(Settings.amount_too_low);      // Show too low amount
            $("#daily_payment_"+[i]).addClass('red_text');               // Set message to red color
            $("#bankButton_"+[i]).removeClass('btn-success');              // Remove class green button
            $("#bankButton_"+[i]).addClass('btn-warning');                 // Add class orange button
            $("#bankButton_"+[i]).prop('disabled', true);                  // Disable button (and link)
        }
        else if (to_borrow_value > max_loan[i]) {                        // Case with too high amount
            $("#daily_payment_"+[i]).text(Settings.amount_too_high);     // Show too high amount
            $("#daily_payment_"+[i]).addClass('red_text');               // Set message to red color
            $("#bankButton_"+[i]).removeClass('btn-success');              // Remove class green button
            $("#bankButton_"+[i]).addClass('btn-warning');                 // Add class orange button
            $("#bankButton_"+[i]).prop('disabled', true);                  // Disable button (and link)
        }
        else {  
            integer_daily_payment[i] = String(Math.floor(daily_payment[i]));      // Restore back to okay to signup status (within range)
            var remainder = integer_daily_payment[i].length % 3;
            daily_payment_display[i] = (integer_daily_payment[i].substr(0, remainder) + integer_daily_payment[i].substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
            $("#daily_payment_"+[i]).text(daily_payment_display[i]+' €');   // Show amount monthly payment with friendly format
            $("#daily_payment_"+[i]).removeClass('red_text');            // Set message back to black (remove red color)
            $("#bankButton_"+[i]).removeClass('btn-warning');              // Remove class orange button
            $("#bankButton_"+[i]).addClass('btn-success');                 // Add class orange button
            $("#bankButton_"+[i]).prop('disabled', false);                 // Enable button (and link)
        }
    }
}

// End of Bank sliders functions


// FAQ page - answers are hidden by default via CSS (.faq dd { display: none })
// If question is clicked, show the answer and hide the rest
$('dt').click(
    function() {
        var toggle = $(this).nextUntil('dt');
        toggle.slideToggle();
        $('dd').not(toggle).slideUp();
    });
// END of FAQ page hide/show



// Refresh captcha on contact page if button if clicked
$('.reload-captcha').click(function(event){
    event.preventDefault();
    $.ajax({
        type: "POST",
        url:Settings.base_url+'contact_controller/get_captcha',
        data: "origin=javascript",
        dataType:"json",
        success:function(data){
            $('.captcha-img').replaceWith(data.img);
        }
    });            
 });
 

//GOOGLE CHARTS START
if (window.location.href.indexOf('finances_controller') !== -1) {
//if (window.location.href == 'https://www.ski-manager.net/finances_controller' || window.location.href == 'https://127.0.0.1/ski-manager/finances_controller' || window.location.href == 'https://localhost/ski-manager/finances_controller') {  
    currentResortId = $('#currentResortId').attr('value');
    // This chart draws a dual axis chart showing revenues and expenses
    function drawCharts() {
        // Define the chart to be drawn in PHP
            $.ajax({
                type: "POST",
                url: Settings.base_url+"finances_controller/draw_dual_lineChart",
                data: "currentResortID="+currentResortId+"&table1=revenue&table2=expenses&title=titleRevenuesExpenses",
                dataType:"json"
            }).done(function (jsonData) {          
                // Create our data table out of JSON data loaded from server.
                var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
                // Set chart's options
                var options = jsonData[1]['options'];
                // Instantiate and draw the chart
                var chart = new google.visualization.LineChart(document.getElementById('dual_chart_revenues_expenses')); 
                chart.draw(data_chart, options);
            });
            $.ajax({
                type: "POST",
                url: Settings.base_url+"finances_controller/draw_single_lineChart",
                data: "currentResortID="+currentResortId+"&table1=affluence&title=titleAffluence",
                dataType:"json"
            }).done(function (jsonData) {          
                // Create our data table out of JSON data loaded from server.
                var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
                // Set chart's options
                var options = jsonData[1]['options'];
                // Instantiate and draw the chart
                var chart = new google.visualization.LineChart(document.getElementById('single_chart_affluence')); 
                chart.draw(data_chart, options);
            });
            
            $.ajax({
                type: "POST",
                url: Settings.base_url+"finances_controller/draw_single_lineChart",
                data: "currentResortID="+currentResortId+"&table1=reputation&title=titleReputation",
                dataType:"json"
            }).done(function (jsonData) {          
                // Create our data table out of JSON data loaded from server.
                var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
                // Set chart's options
                var options = jsonData[1]['options'];
                // Instantiate and draw the chart
                var chart = new google.visualization.AreaChart(document.getElementById('single_chart_reputation')); 
                chart.draw(data_chart, options);
            });
            
            $.ajax({
                type: "POST",
                url: Settings.base_url+"finances_controller/draw_single_lineChart",
                data: "currentResortID="+currentResortId+"&table1=snow_level&title=titleSnowLevel",
                dataType:"json"
            }).done(function (jsonData) {          
                // Create our data table out of JSON data loaded from server.
                var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
                // Set chart's options
                var options = jsonData[1]['options'];
                // Instantiate and draw the chart
                var chart = new google.visualization.AreaChart(document.getElementById('single_chart_snow_level')); 
                chart.draw(data_chart, options);
            });
            $.ajax({
                type: "POST",
                url: Settings.base_url+"finances_controller/draw_PieChartRevenues",
                data: "currentResortID="+currentResortId+"&title=titleRevenues",
                dataType:"json"
            }).done(function (jsonData) {          
                // Create our data table out of JSON data loaded from server.
                var data_chart = new google.visualization.DataTable(jsonData[0]['data']);

                // Set chart's options
                var options = jsonData[1]['options'];

                // Instantiate and draw the chart
                var chart = new google.visualization.PieChart(document.getElementById('pie_chart_revenues')); 
                chart.draw(data_chart, options);
            });

    }
    google.charts.load('current', {
    callback: drawCharts,
    packages: ['corechart']
});
}
    //GOOGLE CHARTS END

//STATISTICS CHARTS START
if (window.location.href.indexOf('statistics_controller') !== -1) {
    currentResortId = $('#currentResortId').attr('value');

    function drawStatisticsCharts() {

        // Peak Lift Usage - Bar chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "statistics_controller/get_lift_usage_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.BarChart(document.getElementById('chart_lift_usage'));
            chart.draw(data_chart, options);
        });

        // Revenue per Lift - Bar chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "statistics_controller/get_revenue_per_lift_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.BarChart(document.getElementById('chart_revenue_per_lift'));
            chart.draw(data_chart, options);
        });

        // Most Popular Slope - Bar chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "statistics_controller/get_slope_popularity_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.BarChart(document.getElementById('chart_slope_popularity'));
            chart.draw(data_chart, options);
        });

        // Guest Satisfaction - Line chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "statistics_controller/get_satisfaction_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.LineChart(document.getElementById('chart_satisfaction'));
            chart.draw(data_chart, options);
        });

        // Weather History - Area chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "statistics_controller/get_weather_history_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.AreaChart(document.getElementById('chart_weather_history'));
            chart.draw(data_chart, options);
        });

        // Daily Visitor Count - Line chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "statistics_controller/get_visitor_count_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.LineChart(document.getElementById('chart_visitor_count'));
            chart.draw(data_chart, options);
        });

        // Daily Revenue vs Expenses - Line chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "statistics_controller/get_revenue_expenses_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.LineChart(document.getElementById('chart_revenue_expenses'));
            chart.draw(data_chart, options);
        });

    }
    google.charts.load('current', {
        callback: drawStatisticsCharts,
        packages: ['corechart']
    });
}
//STATISTICS CHARTS END

//DATA DASHBOARD CHARTS START
if (window.location.href.indexOf('data_dashboard_controller') !== -1) {
    currentResortId = $('#currentResortId').attr('value');

    function drawDataDashboardCharts() {

        // Traffic Heatmap - Bar chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "data_dashboard_controller/get_traffic_heatmap_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.BarChart(document.getElementById('chart_traffic_heatmap'));
            chart.draw(data_chart, options);
        });

        // Profit Breakdown - Donut chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "data_dashboard_controller/get_profit_breakdown_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.PieChart(document.getElementById('chart_profit_breakdown'));
            chart.draw(data_chart, options);
        });

        // Visitor Segmentation - Donut chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "data_dashboard_controller/get_visitor_segmentation_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.PieChart(document.getElementById('chart_visitor_segmentation'));
            chart.draw(data_chart, options);
        });

        // Accident Probability - Bar chart
        $.ajax({
            type: "POST",
            url: Settings.base_url + "data_dashboard_controller/get_accident_probability_chart",
            data: "currentResortID=" + currentResortId,
            dataType: "json"
        }).done(function (jsonData) {
            var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
            var options = jsonData[1]['options'];
            var chart = new google.visualization.BarChart(document.getElementById('chart_accident_probability'));
            chart.draw(data_chart, options);
        });

    }
    google.charts.load('current', {
        callback: drawDataDashboardCharts,
        packages: ['corechart']
    });
}
//DATA DASHBOARD CHARTS END

// Javascrip code refreshing the sidebar achievement table after loading the page. The content will be updated if an achievement is changed during a new page loading.
function refresh_achievements_sidebar() {
    // Declaring arrays
    var progress = []; 
    var id_achievement = []; 
    var name = []; 
    var linkname = []; 
    var button = []; 
    $.ajax({
       type: "POST",
       dataType: "json",
       url: Settings.base_url+"achievements_controller/get_achievements_from_session",   // Retrieve the 3 achievements from the session
       success: function(result){
           var achievements = (result && result.achievements) ? result.achievements : [];
           var rows_to_render = Math.min(3, achievements.length);
           for (row = 0; row < rows_to_render; row ++) {    // Render up to 3 achievements
               // Putting values in friendly variables
               progress[row] = achievements[row].progress;
               id_achievement[row] = achievements[row].id_achievement;
               button[row] = achievements[row].button;
               name[row] = achievements[row].name;
               achievements_to_claim = result.achievements_to_claim || 0;
               linkname[row] = '<a href="'+Settings.base_url+'achievements_controller#status-'+id_achievement[row]+'">'+name[row]+'</a>';
               // Editing the relevant SPAN tags
               $('#progress-'+row).html(progress[row]);                   
               $('#linkname-'+row).html(linkname[row]);                   
               $('#button-'+row).html(button[row]);                   
               $('#achievements_to_claim').html(achievements_to_claim);     // Updates the number of achievements left to claim (red background)               
           }
           for (row = rows_to_render; row < 3; row++) {
               $('#progress-'+row).html('0');
               $('#linkname-'+row).html('');
               $('#button-'+row).html('');
           }
       }
    });
}

//if ($('#chars').length > 0) {
    (function($) {  // counts how many characters left in textarea
        $.fn.extend( {
            limiter: function(limit, elem) {
                $(this).on("keyup focus", function() {
                    setCount(this, elem);
                });

                    function setCount(src, elem) {
                        var chars = src.value.length;
                        if (chars > limit) {
                            src.value = src.value.substr(0, limit);
                            chars = limit;
                        }
                        elem.html( limit - chars + '/500');
                    }
                    setCount($(this)[0], elem);
            }
        });
    })(jQuery);
//}



// Collapses or expands the sector blocks in the resort controller
// http://stackoverflow.com/questions/9209403/expand-collapse-all-table-rows-at-onc-click
// Issue here: the arrows are not shown due to the cell background. May be fized when optimizing CSS or moving the arrows next to the table.

$('.collapsable_cmd_header').click(function(){
  var table_id = $(this).closest('table').attr('id');   // Gets the table ID
  var $table = $('#'+table_id);
  var datarows = document.querySelectorAll('#' + table_id + ' tr.datarow');
  // Use a data attribute to track collapse state instead of querying :visible (avoids forced reflow).
  // Initial state is not collapsed (rows visible), so first click collapses.
  var isCollapsed = $table.data('rows-collapsed') === true;
  if (isCollapsed) {
    // Currently collapsed - show all rows using direct style writes
    for (var i = 0; i < datarows.length; i++) { datarows[i].style.display = ''; }
    $table.data('rows-collapsed', false);
    $('#collapse_expand-'+table_id).html('<i class="fa-solid fa-angle-down transition-transform"></i>'); 
  } else {
    // Currently shown - hide all rows using direct style writes
    for (var i = 0; i < datarows.length; i++) { datarows[i].style.display = 'none'; }
    $table.data('rows-collapsed', true);
    $('#collapse_expand-'+table_id).html('<i class="fa-solid fa-angle-up transition-transform"></i>'); 
  }
});

// Collasping locked sectors in resort controller
var sector_access = [];     // Initiating an empty array
for (var sector_id = 0; sector_id <= active_sectors; sector_id++) {     // For each of the active sector... (active_sectors is a global JS variable (from PHP CONSTANT) got in footer.php)
    sector_access[sector_id] = $('#'+sector_id).closest('table').attr('data-access');   // Get the "data-access" value from the table/sector. Should be 0 or 1.
    if (sector_access[sector_id] == 0) {        // If status locked
        // Use direct style writes (not jQuery .hide()) to avoid a synchronous forced reflow
        var lockedRows = document.querySelectorAll('#' + parseInt(sector_id, 10) + ' tr.datarow');
        for (var ri = 0; ri < lockedRows.length; ri++) { lockedRows[ri].style.display = 'none'; }
    }
}


// Circle progress bar test
// from http://stackoverflow.com/a/24224498/893204
//var ele = [];
//var el = document.getElementById('graph-1'); // get canvas
specific_graph = document.querySelectorAll('div[id^="graph-"]');
number_of_achievements = specific_graph.length;

var drawCircle = function(ctx, radius, color, lineWidth, percent) {
    percent = Math.min(Math.max(0, percent || 1), 1);
    ctx.beginPath();
    ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
    ctx.strokeStyle = color;
    ctx.lineCap = 'round'; // butt, round or square
    ctx.lineWidth = lineWidth;
    ctx.stroke();
};

var options = [];
var color = [];
var canvas = [];
var span = [];
var radius = [];
var ctx = [];
for (count = 0; count < number_of_achievements; count++) { 


    options[count] = {
        percent:  specific_graph[count].getAttribute('data-percent') || 0,
        size: specific_graph[count].getAttribute('data-size') || 70,
        lineWidth: specific_graph[count].getAttribute('data-line') || 8,
        rotate: specific_graph[count].getAttribute('data-rotate') || 0
    }
    switch(true) {
        case (options[count].percent<20):
            color[count] = 'red';
            break;
        case (options[count].percent<40):
            color[count] = 'orange';
            break;
        case (options[count].percent<60):
            color[count] = 'yellow';
            break;
        case (options[count].percent<80):
            color[count] = 'lightgreen';
            break;
        default:
            color[count] = 'green';
    }

    if(options[count].size == 30) {         // Need to make the drawings smaller
        spanprogress_element = 'spanprogress';
        canvas_element = 'canvas';
    }
    else {
        spanprogress_element = 'bigspanprogress';
        canvas_element = 'bigcanvas';
    }
    canvas[count] = document.createElement('canvas');
    canvas[count].setAttribute("id", "canvas-"+count);
    canvas[count].setAttribute("class", canvas_element);
    span[count] = document.createElement(spanprogress_element);
    span[count].textContent = options[count].percent + '%';

    if (typeof(G_vmlCanvasManager) !== 'undefined') {
        G_vmlCanvasManager.initElement(canvas[count]);
    }

    ctx[count] = canvas[count].getContext('2d');
    canvas[count].width = canvas[count].height = options[count].size;

    specific_graph[count].appendChild(span[count]);
    specific_graph[count].appendChild(canvas[count]);

    ctx[count].translate(options[count].size / 2, options[count].size / 2); // change center
    ctx[count].rotate((-1 / 2 + options[count].rotate / 180) * Math.PI); // rotate -90 deg

    //imd = ctx.getImageData(0, 0, 240, 240);
    radius[count] = (options[count].size - options[count].lineWidth) / 2;

    drawCircle(ctx[count], radius[count], '#efefef', options[count].lineWidth, 100 / 100);
    drawCircle(ctx[count], radius[count], color[count], options[count].lineWidth, options[count].percent / 100);

}





// tournament status progress circle
var options = [];
var color = [];
var canvas = [];
var span = [];
var radius = [];
var ctx = [];

graph_tournament_progress = document.querySelectorAll('div[id="ongoing_tournament_progress"]');

if (typeof(graph_tournament_progress[0]) !== 'undefined') {
    options_tournament_progress = {
        percent:  graph_tournament_progress[0].getAttribute('data-percent') || 0,
        size: 70,
        lineWidth: 8,
        rotate: 0
    }
    switch(true) {
        case (options_tournament_progress.percent<20):
            color_tournament_progress = 'red';
            break;
        case (options_tournament_progress.percent<40):
            color_tournament_progress = 'orange';
            break;
        case (options_tournament_progress.percent<60):
            color_tournament_progress = 'yellow';
            break;
        case (options_tournament_progress.percent<80):
            color_tournament_progress = 'lightgreen';
            break;
        default:
            color_tournament_progress = 'green';
    }

        spanprogress_element = 'bigspanprogress';
        canvas_element = 'bigcanvas';

    canvas_tournament_progress = document.createElement('canvas');
    canvas_tournament_progress.setAttribute("id", "canvas-"+'tournament');
    canvas_tournament_progress.setAttribute("class", canvas_element);
    span_tournament_progress = document.createElement(spanprogress_element);
    span_tournament_progress.textContent = options_tournament_progress.percent + '%';

    if (typeof(G_vmlCanvasManager) !== 'undefined') {
        G_vmlCanvasManager.initElement(canvas_tournament_progress);
    }

    ctx_tournament_progress = canvas_tournament_progress.getContext('2d');
    canvas_tournament_progress.width = canvas_tournament_progress.height = options_tournament_progress.size;

    graph_tournament_progress[0].appendChild(span_tournament_progress);
    graph_tournament_progress[0].appendChild(canvas_tournament_progress);

    ctx_tournament_progress.translate(options_tournament_progress.size / 2, options_tournament_progress.size / 2); // change center
    ctx_tournament_progress.rotate((-1 / 2 + options_tournament_progress.rotate / 180) * Math.PI); // rotate -90 deg

    //imd = ctx.getImageData(0, 0, 240, 240);
    radius_tournament_progress = (options_tournament_progress.size - options_tournament_progress.lineWidth) / 2;

    drawCircle(ctx_tournament_progress, radius_tournament_progress, '#efefef', options_tournament_progress.lineWidth, 100 / 100);
    drawCircle(ctx_tournament_progress, radius_tournament_progress, color_tournament_progress, options_tournament_progress.lineWidth, options_tournament_progress.percent / 100);

}


function secondsToHms(d) {  // Converts a time (seconds) into friendly format. e.g. 22000 > 1 day 5 hours 3 minutes 
    d = Number(d);
var result = '';
    var days = Math.floor(d / 3600 / 24);
    var h = Math.floor(d / 3600 - (days*24));
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);
    if (days > 0)
        result = days + ' ' + Settings.days + ' ';
    if (h > 0)
        result = result.concat(h + ' ' + Settings.hours + ' ');
    if (m > 0)
        result = result.concat(m + ' ' + Settings.minutes);
    if (days <= 0 && h <= 0 && m <= 0)
        result = result.concat('<1 ' + Settings.seconds);
    return result;
}



// Edit Resort Mode
//if ($('#edit_resort_mode').length > 0 || $('input[id="submit_edit_resort"]').length > 0) {
        $('#edit_resort_mode').on("click", function(e){
            $.ajax({
                type: "POST",
                dataType: "json",
                url: Settings.base_url+"resort_controller/edit_resort_mode_info",
                success: function(result){
                    if (result.returned == true){
                        $('#infoResort').html(result.data);    
                        var elem = $("#chars");
                        $("#resort_description").limiter(500, elem);
                    } 
                }
            });
        });
        
        $(document).on("click", 'input[id="submit_edit_resort"]', function(e){
            e.preventDefault();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: Settings.base_url+"resort_controller/update_resort",
                data: $('#resort_form').serialize(),
                success: function(result){
                    if (result.returned == true){
                        window.location.reload();
                    } 
                }
            });
        });
   // } 
   
   
// In marketing page, if row is selected, change CSS (background). If not selected, remove selected class
$("tbody").on("click", "tr", function(e) {     
    $(this).addClass("selected").siblings(".selected").removeClass("selected");
});  


function update_cash_player() {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"overview_staff_controller/get_cash_player",
        success: function(result){
            cash = result.cash;
            var remainder = cash.length % 3;
            cash_display = (cash.substr(0, remainder) + cash.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
            $('#cash_div').html(cash_display);
        }
    });
}
function update_genepis() {
    $.ajax({ 
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"genepis_controller/get_genepis",
        success: function(result){
            genepis = result.genepis;
            var remainder = genepis.length % 3;
            genepis_display = (genepis.substr(0, remainder) + genepis.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
            $('#genepis_div').html(genepis_display);
        }
    });
}
function update_reputation() {   
    $.ajax({
        type: "POST",
        dataType: "json",
        url: Settings.base_url+"overview_staff_controller/get_reputation_player",
        success: function(result){
            reputation = result.reputation;
            var remainder = reputation.length % 3;
            reputation_display = (reputation.substr(0, remainder) + reputation.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
            $('#reputation_div').html(reputation_display);
        }
    });
}


/* ── Night Skiing AJAX enhancement ─────────────────────────────────────────
 * Intercepts forms and the toggle link on the night skiing management page,
 * converting full-page POSTs into AJAX calls so the user gets instant
 * smToast feedback without a page reload.
 * ────────────────────────────────────────────────────────────────────────── */
(function () {
    'use strict';

    if (!document.querySelector('form[action*="night_skiing_controller"]')) return;

    function nsPost(url, body) {
        return fetch(url, {
            method: 'POST',
            body: body,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        });
    }

    /* Resort-level settings form ----------------------------------------- */
    var resortForm = document.querySelector('form[action*="save_resort_settings"]');
    if (resortForm) {
        resortForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = resortForm.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;
            nsPost(resortForm.action, new FormData(resortForm))
                .then(function (d) {
                    smToast(d.success ? '\u2713 Night skiing settings saved' : 'Error saving settings', d.success ? 'success' : 'error');
                })
                .catch(function () { smToast('Network error — settings not saved', 'error'); })
                .finally(function () { if (btn) btn.disabled = false; });
        });
    }

    /* Trail modal form ------------------------------------------------------- */
    var trailForm = document.querySelector('form[action*="save_trail_settings"]');
    if (trailForm) {
        trailForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = trailForm.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            nsPost(trailForm.action, new FormData(trailForm))
                .then(function (d) {
                    if (d.success) {
                        smToast('\u2713 Trail settings saved', 'success');
                        var modal = document.getElementById('trailModal');
                        if (modal) modal.close();

                        var slopeId    = (trailForm.querySelector('#modal_slope_id')    || {}).value;
                        var nsToggle   = trailForm.querySelector('#ns_enabled_toggle');
                        var nsEnabled  = nsToggle && nsToggle.checked ? '1' : '0';
                        var lightType  = (trailForm.querySelector('#modal_light_type')  || {}).value;
                        var brightness = (trailForm.querySelector('#modal_brightness')  || {}).value;
                        var spacingEl  = trailForm.querySelector('input[name="trail_pole_spacing"]:checked');
                        var spacing    = spacingEl ? spacingEl.value : null;

                        var cfgBtn = document.querySelector('button[data-slope-id="' + slopeId + '"]');
                        if (cfgBtn) {
                            cfgBtn.setAttribute('data-ns-enabled',  nsEnabled);
                            cfgBtn.setAttribute('data-light-type',  lightType);
                            cfgBtn.setAttribute('data-brightness',  brightness);
                            if (spacing) cfgBtn.setAttribute('data-pole-spacing', spacing);

                            var row   = cfgBtn.closest('tr');
                            var badge = row && row.querySelector('.badge');
                            if (badge) {
                                badge.className   = nsEnabled === '1' ? 'badge badge-success' : 'badge badge-neutral';
                                badge.textContent = nsEnabled === '1'
                                    ? (badge.dataset.onText  || 'ON')
                                    : (badge.dataset.offText || 'OFF');
                            }
                        }
                    } else {
                        smToast('Error saving trail settings', 'error');
                    }
                })
                .catch(function () { smToast('Network error — trail not saved', 'error'); })
                .finally(function () { if (btn) btn.disabled = false; });
        });
    }

    /* Global night skiing toggle link ---------------------------------------- */
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[href*="night_skiing_controller/toggle"]');
        if (!link) return;
        e.preventDefault();

        var parts    = link.getAttribute('href').replace(/\/$/, '').split('/');
        var action   = parts[parts.length - 1];
        var resortID = parts[parts.length - 2];

        var fd = new FormData();
        fd.append('resort_id', resortID);
        fd.append('action',    action);

        nsPost(Settings.base_url + 'night_skiing_controller/ajax_toggle', fd)
            .then(function (d) {
                if (d.success) {
                    smToast(action === '1' ? '\u2713 Night skiing enabled' : '\u2713 Night skiing disabled', 'success');
                    setTimeout(function () { window.location.reload(); }, 900);
                } else {
                    smToast('Error toggling night skiing', 'error');
                }
            })
            .catch(function () { smToast('Network error — toggle failed', 'error'); });
    });
}());

/* ── Night skiing helpers: events & trail quality ─────────────────────── */
(function () {
    'use strict';

    if (typeof Settings === 'undefined' || !Settings.base_url) {
        return;
    }

    var currentFilter = 'upcoming';
    var eventsCache = [];

    function getQualityConfig() {
        var cfg = window.NightSkiingQualityConfig || {};
        var base = typeof cfg.baseLoss === 'number' ? cfg.baseLoss : parseFloat(cfg.baseLoss || 1);
        var factor = typeof cfg.brightnessFactor === 'number' ? cfg.brightnessFactor : parseFloat(cfg.brightnessFactor || 0.5);
        if (!isFinite(base)) base = 1;
        if (!isFinite(factor)) factor = 0.5;
        return { base: base, factor: factor };
    }

    function updateTrailQuality(brightnessValue) {
        var cfg = getQualityConfig();
        var slider = document.getElementById('modal_brightness');
        var val = brightnessValue != null ? parseInt(brightnessValue, 10) : (slider ? parseInt(slider.value, 10) : 3);
        if (!val || isNaN(val)) val = 3;

        var dailyLoss = Math.round(cfg.base + (val - 1) * cfg.factor);
        var lossEl = document.getElementById('modal_quality_loss');
        if (lossEl) {
            lossEl.textContent = '-' + dailyLoss + ' / night';
            lossEl.title = 'Quality decreases by ' + dailyLoss + ' points per night based on brightness.';
        }
    }

    function renderEventsList(events) {
        eventsCache = Array.isArray(events) ? events.slice() : [];
        var listEl = document.getElementById('ns_events_list');
        if (!listEl) return;

        if (!eventsCache.length) {
            listEl.innerHTML = '<p class="text-white/60 text-sm">No events scheduled.</p>';
            return;
        }

        var todayStr = (window.NightSkiingTonight && NightSkiingTonight.date) || new Date().toISOString().split('T')[0];

        // Group events by YYYY-MM for a simple month-based timeline
        var groups = {};
        eventsCache.forEach(function (e) {
            var date = String(e.scheduled_date || '');
            var key = date && date.length >= 7 ? date.slice(0, 7) : 'other';
            if (!groups[key]) {
                groups[key] = [];
            }
            groups[key].push(e);
        });

        var monthKeys = Object.keys(groups).sort();
        var htmlParts = [];

        monthKeys.forEach(function (key) {
            var monthEvents = groups[key].slice().sort(function (a, b) {
                var da = String(a.scheduled_date || '');
                var db = String(b.scheduled_date || '');
                if (da === db) {
                    return (a.id || 0) - (b.id || 0);
                }
                return da < db ? -1 : 1;
            });

            var label;
            if (key !== 'other') {
                var year = parseInt(key.slice(0, 4), 10);
                var month = parseInt(key.slice(5, 7), 10) - 1;
                var d = new Date(year, month, 1);
                label = d.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
            } else {
                label = 'Other';
            }

            htmlParts.push('<div class="mb-3 pl-3 border-l border-[#1e3a5f]">');
            htmlParts.push('<div class="text-[0.7rem] uppercase tracking-wide text-white/40 mb-1">' + label + '</div>');

            monthEvents.forEach(function (e) {
                var typeLabel = String(e.event_type || '').replace(/_/g, ' ').toUpperCase();
                var status = e.status || 'pending';
                var statusClass = 'badge-neutral';
                if (status === 'pending') statusClass = 'badge-info';
                else if (status === 'completed') statusClass = 'badge-success';
                else if (status === 'cancelled') statusClass = 'badge-error';

                var mult = Number(e.revenue_multiplier || 1);
                var multText = isFinite(mult) ? mult.toFixed(2) : '1.00';

                var isTonight = String(e.scheduled_date || '') === todayStr;
                var dotClass = isTonight ? 'bg-green-400' : 'bg-sky-500';
                var tonightBadge = isTonight ? '<span class="ml-2 badge badge-success text-[0.65rem]">Tonight</span>' : '';

                htmlParts.push(
                    '<div class="mb-2 p-2 rounded border border-[#1e3a5f] bg-[#020617] text-xs text-white" data-event-id="' + e.id + '">' +
                        '<div class="flex items-center justify-between mb-1">' +
                            '<div class="flex items-center gap-2">' +
                                '<span class="w-2 h-2 rounded-full ' + dotClass + '"></span>' +
                                '<span class="font-semibold">' + typeLabel + '</span>' +
                                '<span class="ml-2 opacity-70">' + (e.scheduled_date || '') + '</span>' +
                                tonightBadge +
                            '</div>' +
                            '<span class="badge ' + statusClass + ' ml-2">' + status + '</span>' +
                        '</div>' +
                        '<div class="flex flex-wrap gap-x-4 gap-y-1 opacity-80">' +
                            '<span>Visitors: +' + (e.visitor_bonus_pct || 0) + '%</span>' +
                            '<span>Revenue: ×' + multText + '</span>' +
                            '<span>Cost: ' + (e.cost || 0) + ' €</span>' +
                            '<span>Rep: +' + (e.reputation_bonus || 0) + '</span>' +
                        '</div>' +
                        '<div class="mt-1 flex gap-2">' +
                            '<button type="button" class="btn btn-ghost btn-xs" onclick="editEvent(' + e.id + ')">Edit</button>' +
                            '<button type="button" class="btn btn-outline btn-xs" onclick="deleteEvent(' + e.id + ')">Delete</button>' +
                        '</div>' +
                    '</div>'
                );
            });

            htmlParts.push('</div>');
        });

        listEl.innerHTML = htmlParts.join('');
    }

    function loadScheduledEvents(filter) {
        if (filter) currentFilter = filter;
        var listEl = document.getElementById('ns_events_list');
        if (listEl) {
            listEl.innerHTML = '<p class="text-white/60 text-sm">Loading events…</p>';
        }

        var url = Settings.base_url + 'night_skiing_controller/get_events?filter=' + encodeURIComponent(currentFilter);
        fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (d) {
                if (!d.success) {
                    if (listEl) listEl.innerHTML = '<p class="text-red-400 text-sm">Error loading events.</p>';
                    return;
                }
                renderEventsList(d.events || []);
            })
            .catch(function () {
                if (listEl) listEl.innerHTML = '<p class="text-red-400 text-sm">Network error loading events.</p>';
            });
    }

    function scheduleEvent(eventType) {
        var form = document.getElementById('ns_custom_event_form');

        // Quick preset scheduling when no form exists
        if (!form) {
            var today = new Date().toISOString().split('T')[0];
            var fdQuick = new FormData();
            fdQuick.append('event_type', eventType || 'dj_night');
            fdQuick.append('scheduled_date', today);
            fetch(Settings.base_url + 'night_skiing_controller/schedule_event', {
                method: 'POST',
                body: fdQuick,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
              .then(function (d) {
                  smToast(d.success ? '✓ Event scheduled' : 'Error scheduling event', d.success ? 'success' : 'error');
                  if (d.success) loadScheduledEvents(currentFilter);
              })
              .catch(function () { smToast('Network error — event not scheduled', 'error'); });
            return;
        }

        var typeSelect = form.querySelector('#ns_event_type');
        if (eventType && typeSelect) {
            typeSelect.value = eventType;
        }

        var type = typeSelect ? String(typeSelect.value || '').trim() : '';
        var dateInput = form.querySelector('#ns_event_date');
        var dateVal = dateInput ? dateInput.value : '';
        if (!dateVal && dateInput) {
            var todayStr = new Date().toISOString().split('T')[0];
            dateInput.value = todayStr;
            dateVal = todayStr;
        }

        if (!type) {
            smToast('Please choose an event type.', 'error');
            return;
        }
        if (!dateVal) {
            smToast('Please choose a date for the event.', 'error');
            return;
        }

        var eventIdInput = form.querySelector('#ns_event_id');
        var eventId = eventIdInput && eventIdInput.value ? parseInt(eventIdInput.value, 10) : 0;
        var endpoint = eventId > 0 ? 'night_skiing_controller/update_event' : 'night_skiing_controller/schedule_event';

        var fd = new FormData();
        if (eventId > 0) {
            fd.append('event_id', String(eventId));
        }
        fd.append('event_type', type);
        fd.append('scheduled_date', dateVal);

        // For custom events (or edits), send numeric fields
        var sendNumbers = (type === 'custom' || eventId > 0);
        if (sendNumbers) {
            var vEl = form.querySelector('#ns_event_visitors');
            var rEl = form.querySelector('#ns_event_revenue');
            var cEl = form.querySelector('#ns_event_cost');
            var repEl = form.querySelector('#ns_event_rep');

            if (vEl && vEl.value !== '') fd.append('visitor_bonus_pct', vEl.value);
            if (rEl && rEl.value !== '') fd.append('revenue_multiplier', rEl.value);
            if (cEl && cEl.value !== '') fd.append('cost', cEl.value);
            if (repEl && repEl.value !== '') fd.append('reputation_bonus', repEl.value);
        }

        var submitBtn = document.getElementById('ns_event_submit_btn');
        if (submitBtn) submitBtn.disabled = true;

        fetch(Settings.base_url + endpoint, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
          .then(function (d) {
              if (d.success) {
                  smToast(eventId > 0 ? '✓ Event updated' : '✓ Event scheduled', 'success');
                  if (eventIdInput) eventIdInput.value = '';
                  if (form && eventId === 0) {
                      form.reset();
                  }
                  if (submitBtn) submitBtn.textContent = 'Schedule Event';
                  loadScheduledEvents(currentFilter);
              } else {
                  smToast('Error saving event', 'error');
              }
          })
          .catch(function () { smToast('Network error — event not saved', 'error'); })
          .finally(function () { if (submitBtn) submitBtn.disabled = false; });
    }

    function deleteEvent(eventId) {
        eventId = parseInt(eventId, 10);
        if (!eventId) return;

        var fd = new FormData();
        fd.append('event_id', String(eventId));

        fetch(Settings.base_url + 'night_skiing_controller/delete_event', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
          .then(function (d) {
              smToast(d.success ? '✓ Event deleted' : 'Error deleting event', d.success ? 'success' : 'error');
              if (d.success) loadScheduledEvents(currentFilter);
          })
          .catch(function () { smToast('Network error — event not deleted', 'error'); });
    }

    function editEvent(eventId) {
        eventId = parseInt(eventId, 10);
        if (!eventId || !eventsCache.length) return;

        var form = document.getElementById('ns_custom_event_form');
        if (!form) return;

        var evt = null;
        for (var i = 0; i < eventsCache.length; i++) {
            if (parseInt(eventsCache[i].id, 10) === eventId) {
                evt = eventsCache[i];
                break;
            }
        }
        if (!evt) return;

        var idInput = form.querySelector('#ns_event_id');
        var typeSelect = form.querySelector('#ns_event_type');
        var dateInput = form.querySelector('#ns_event_date');
        var vEl = form.querySelector('#ns_event_visitors');
        var rEl = form.querySelector('#ns_event_revenue');
        var cEl = form.querySelector('#ns_event_cost');
        var repEl = form.querySelector('#ns_event_rep');

        if (idInput) idInput.value = String(eventId);
        if (typeSelect) typeSelect.value = 'custom';
        if (dateInput) dateInput.value = evt.scheduled_date || '';
        if (vEl) vEl.value = evt.visitor_bonus_pct || '';
        if (rEl) rEl.value = evt.revenue_multiplier || '';
        if (cEl) cEl.value = evt.cost || '';
        if (repEl) repEl.value = evt.reputation_bonus || '';

        var submitBtn = document.getElementById('ns_event_submit_btn');
        if (submitBtn) submitBtn.textContent = 'Update Event';

        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Attach custom event form listeners if present
    var customForm = document.getElementById('ns_custom_event_form');
    if (customForm) {
        customForm.addEventListener('submit', function (e) {
            e.preventDefault();
            scheduleEvent();
        });
        var resetBtn = document.getElementById('ns_event_reset_btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                customForm.reset();
                var idInput = document.getElementById('ns_event_id');
                if (idInput) idInput.value = '';
                var submitBtn = document.getElementById('ns_event_submit_btn');
                if (submitBtn) submitBtn.textContent = 'Schedule Event';
            });
        }
    }

    // Filter buttons (Upcoming / Past / All)
    var filterButtons = document.querySelectorAll('.ns-events-filter-btn');
    if (filterButtons.length) {
        filterButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                currentFilter = btn.getAttribute('data-ns-events-filter') || 'upcoming';
                filterButtons.forEach(function (b) {
                    b.classList.toggle('btn-active', b === btn);
                });
                loadScheduledEvents(currentFilter);
            });
        });
    }

    // Expose helpers globally for inline handlers
    window.updateTrailQuality = updateTrailQuality;
    window.loadScheduledEvents = loadScheduledEvents;
    window.scheduleEvent = scheduleEvent;
    window.deleteEvent = deleteEvent;
    window.editEvent = editEvent;
}());
