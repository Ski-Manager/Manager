function initializeTable(tbl){
    var previous = '';
    var new_class = '';
    var not_used_class = '';
    return tbl.DataTable({
        "processing": true,
        "serverSide": false,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "searching": true,
        "order": [ [ 2, "asc" ], [ 1, "asc" ], [ 5, "asc" ] ],
        "ajax": {
            "dataSrc": "Data",
            "url": Settings.base_url+"admin/admin_lift_controller/getDataTable",
            "type": "POST"
        },
        "columns": [
            { "data": "id_lift" },
            { "data": "id_group" },
            { "data": "name_english" },
            { "data": "name_french" },
            { "data": "level" },
            { "data": "lift_type" },
            { "data": "grip_type" },
            { "data": "speed",
                "render": function (data, type, row) {
                    if (type === 'display') {
                        return row.speed + ' m/s';
                    }
                    return row.speed;
                }
            },
            { "data": "capacity" },
            { "data": "base_cost",
                "render": function (data, type, row) {
                    if (type === 'display') {
                        var remainder = row.base_cost.length % 3;   // Gets how many chunks of three are left
                        var cost_display = (row.base_cost.substr(0, remainder) + row.base_cost.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();   // Friendly format with thousand space separator
                        return cost_display + ' + ' + row.meter_cost + ' €/m';
                    }
                    return row.base_cost;
                }
            },
            { "data": "building_time" },
            { "data": "throughput",
                "render": function (data, type, row) {
                    if (type === 'display') {
                        return row.throughput + ' s/h';
                    }
                    return row.throughput;
                }
            },
            { "data": "reputation" },
            { "data": "daily_cost",
                "render": function (data, type, row) {
                    if (type === 'display') {
                        return row.daily_cost + ' €';
                    }
                    return row.daily_cost;
                }
            },
            { "data": "total" },
            { "data": null,       // The data to use in the link
              "render": function(data, type, row) {
                var content_last_col = "<a href='"+Settings.base_url+"admin/admin_lift_controller/edit_lifts/"+row.id_group+"/edit'><img height='25' width='25' src='"+Settings.base_url+"img/icons/edit.png' title='edit'/></a>";
                content_last_col += "<a href='"+Settings.base_url+"admin/admin_lift_controller/edit_lifts/"+row.id_group+"/duplicate'><i class='fa-solid fa-clone' title='duplicate'></i></a>";
                content_last_col += "<a href='?action=delete' class='two_px_padding delete-dialog-admin-items btn btn-danger'>"+Settings.delete+"</a>";
                return content_last_col;
              }
            }
        ],
        "rowCallback": function( nRow, aData ) {
            if (previous === '') {
                $(nRow).addClass('ach_even').removeClass('ach_odd');
                new_class = 'ach_even';
                not_used_class = 'ach_odd';
            } else if ( aData.id_group === previous ) {
                $(nRow).addClass(new_class).removeClass(not_used_class);
            } else {
                $(nRow).addClass(not_used_class).removeClass(new_class);
                var new_class_temp = not_used_class;
                not_used_class = new_class;
                new_class = new_class_temp;
            }
            $(nRow).attr('data-item_type', 'lift');
            $(nRow).attr('data-id_item', aData.id_group);
            previous = aData.id_group;
        }
    });
}

function initializeLocTable(tbl){
    var previous = '';
    var new_class = '';
    var not_used_class = '';
    return tbl.DataTable({
        "processing": true,
        "serverSide": false,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "searching": true,
        "order": [ [ 3, "asc" ] ],
        "ajax": {
            "dataSrc": "Data",
            "url": Settings.base_url+"admin/admin_location_controller/getDataTable",
            "type": "POST"
        },
        "columns": [
            { "data": "id_location" },
            { "data": "id_sector" },
            { "data": "x_coordinates",
                "render": function (data, type, row) {
                    return 'x = ' + row.x_coordinates + ' , y = ' + row.y_coordinates;
                }
            },
            { "data": "id_group" },
            { "data": "length" },
            { "data": "area" },
            { "data": null,       // The data to use in the link
              "render": function(data, type, row) {
                var content_last_col = "<a href='"+Settings.base_url+"admin/admin_location_controller/edit_locations/"+row.id_group+"/edit'><img height='25' width='25' src='"+Settings.base_url+"img/icons/edit.png' title='edit'/></a>";
                content_last_col += "<a href='?action=delete' class='two_px_padding delete-dialog-admin-items btn btn-danger'>"+Settings.delete+"</a>";
                return content_last_col;
              }
            }
        ],
        "rowCallback": function( nRow, aData ) {
            if (previous === '') {
                $(nRow).addClass('ach_even').removeClass('ach_odd');
                new_class = 'ach_even';
                not_used_class = 'ach_odd';
            } else if ( aData.id_group === previous ) {
                $(nRow).addClass(new_class).removeClass(not_used_class);
            } else {
                $(nRow).addClass(not_used_class).removeClass(new_class);
                var new_class_temp = not_used_class;
                not_used_class = new_class;
                new_class = new_class_temp;
            }
            $(nRow).attr('data-item_type', 'location');
            $(nRow).attr('data-id_item', aData.id_group);
            previous = aData.id_group;
        }
    });
}



$(() => {

    // On Admin page, when length (of slope) is entered, we auto-fill the reputation with 10% of the length. 1km = 100 rep.
    var $reputation = $("#reputation");
    $("#length").on('keyup', function() {
        $reputation.val( Math.round(this.value/10) );
    });
    // On Admin page, when base_cost (of lift) is entered, we auto-fill the reputation with 0.01% of the base_cost. 1 000 000€ = 100 rep.
    $("#base_cost").on('keyup', function() {
        $reputation.val( Math.round(this.value/10000) );
    });
    // On Admin page, when building_cost (of building) is entered, we auto-fill the reputation with 0.01% of the base_cost. 1 000 000€ = 100 rep.
    $("#building_cost").on('keyup', function() {
        $reputation.val( Math.round(this.value/10000) );
    });
    // On Admin page, when buying_cost (of equipment) is entered, we auto-fill the reputation with 0.01% of the buying_cost. 100 000€ = 10 rep.
    $("#buying_cost").on('keyup', function() {
        $reputation.val( Math.round(this.value/10000) );
    });

    
    document.getElementById("page-content-wrapper").style.maxWidth = "100%";
    
    // ***************
    // START Warning if Deleting resort
    // ***************

    $(document).on('click', 'a.delete-dialog-admin', function(){
        // Get the values from the data attributes of the <tr>
        var id_resort = $(this).closest('tr').attr('data-id_resort');
        var id_player = $(this).closest('tr').attr('data-id_player');
        // Pass the variables into the dialog
        $("#dialog-confirm").data('del-id_resort', id_resort).dialog('open');
        $("#dialog-confirm").data('del-id_player', id_player).dialog('open');
        return false;
    });
   
   
    $(document).on('click', 'a.delete-dialog-admin-all', function(){
        // Pass the variables into the dialog
        $("#dialog-confirm").data('del-id_resort', 'all').dialog('open');
        $("#dialog-confirm").data('del-id_player', 'all').dialog('open');
        return false;
    });

// Gets the current controller to make the ajax function more dynamic between different pages
var url = window.location.pathname.split("/");
var host = window.location.host;
var controllerName;
if (host.indexOf("localhost") >= 0 || host.indexOf("127.0.0.1") >= 0) {  //If contains localhost
    controllerName = url[3];    // Local [3]
} else {
    controllerName = url[2];    // Production [2]
}

    // Initiating the Delete/Cancel buttons
    var buttonsOpts = {};
    // Creating the Delete button
    buttonsOpts[Settings.delete] = function() {
        // Getting variables from the dialog
        var id_resort = $(this).data('del-id_resort');
        var id_player = $(this).data('del-id_player');
        // For normal use cases, when deleting one player only. The two variables should contain integer IDs
        // Only when using delete_all, the two variables will contain "all"
        var url_to_be_used = Settings.base_url+"admin/"+controllerName+"/delete_action";
        // If called, will delete the resort in delete_resort controller
        $.ajax({
            type: "POST",
            url: url_to_be_used,
            dataType: "json",
            data: "id_resort="+id_resort+"&id_player="+id_player,
            success: function(result){
                if (result.returned === true) {
                    smToast(Settings.item_deleted, 'success');
                    $("table").find("tr[data-id_player='" + id_player + "']").hide();     // Hides the deleted row in the table
                } else {
                    smToast(Settings.something_went_wrong, 'error');
                }
            }
        });
        $(this).dialog('close');
    };
    // Creating the Cancel button
    buttonsOpts[Settings.cancel] = function() {
        $(this).dialog('close');
    };

    // Parameters of the dialog for delete resort and players
    $("#dialog-confirm").dialog({
        resizable: false,
        height: 330,
        modal: true,
        autoOpen: false,
        buttons: buttonsOpts
    });
    
    // ***************
    // END Warning if Deleting resort
    // ***************
    
    // ***************
    // START Warning if Deleting slope, building or lift (items)
    // ***************
    
    $(document).on('click', 'a.delete-dialog-admin-items', function(){
        // Get the values from the data attributes of the <tr>
        var id_item = $(this).closest('tr').attr('data-id_item');
        var item_type = $(this).closest('tr').attr('data-item_type');
        // Pass the variables into the dialog
        $("#dialog-confirm-items").data('del-id_item', id_item).dialog('open');
        $("#dialog-confirm-items").data('del-item_type', item_type).dialog('open');
        return false;
    });
   
    $(document).on('click', 'a.delete-dialog-admin-items-all', function(){
        // Get the values from the data attributes of the <tr>
        var id_item = 'all';
        var item_type = $(this).attr('data-item_type');
        // Pass the variables into the dialog
        $("#dialog-confirm-items").data('del-id_item', id_item).dialog('open');
        $("#dialog-confirm-items").data('del-item_type', item_type).dialog('open');
        return false;
    });
   
    // Initiating the Delete/Cancel buttons
    var buttonsOptsItems = {};
    // Creating the Delete button
    buttonsOptsItems[Settings.delete] = function() {
        // Getting variables from the dialog
        var id_item = $(this).data('del-id_item');
        var item_type = $(this).data('del-item_type');
        
        // If called, will delete the resort in delete_resort controller
        $.ajax({
            type: "POST",
            url: Settings.base_url+"admin/admin_slope_controller/delete_action",
            data: "id_item="+id_item+"&item_type="+item_type,
            success: function(){
                smToast(Settings.item_deleted, 'success');
                $("table").find("tr[data-id_item='" + id_item + "']").hide();     // Hides the deleted row in the table
            }
        });
        $(this).dialog('close');
    };
    // Creating the Cancel button
    buttonsOptsItems[Settings.cancel] = function() {
        $(this).dialog('close');
    };
    
    // Parameters of the dialog for delete slope, building or lift (items)
    $("#dialog-confirm-items").dialog({
        resizable: false,
        height: 330,
        modal: true,
        autoOpen: false,
        buttons: buttonsOptsItems
    });
    
    // ***************
    // END Warning if Deleting slope, building or lift (items)
    // ***************
    
    // ***************
    // START Scrolling and Bootstrap Table tabs
    // ***************
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    });

var table1 = initializeTable($("#admin_lift_table"));
var table2 = initializeLocTable($("#admin_location_table"));
            
    if ( $("table.staff_table").length ) {
        $('table.staff_table').DataTable({
            "processing": true,
            "paging":   false,      // no paging in the table
            "ordering": true,      // no ordering, no link on header, no sorting
            "info":     true,
            "searching": true,
            "order": [[ 3, "desc" ], [ 4, "asc" ]]
        });
    }
    
    // ***************
    // END Scrolling and Bootstrap Table tabs
    // ***************
    
    
    
    
    // ***************
    // START Click button to empty table
    // ***************
    $(document).on('click', 'button.empty_button', function(){
        var id_button = $(this).attr('id');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: Settings.base_url+"admin/Admin_maintenance_controller/empty_table/"+id_button,
            data: "table="+id_button,
            success: function(result){
                if (result.returned === true) {
                    smToast(result.message, 'success');
                    $('#rows_'+id_button).html('0');
                } else {
                    smToast(result.message, 'error');
                }
            },
            error: function(){
                smToast(Settings.something_went_wrong, 'error');
            }
        });
    });

    // ***************
    // END Click button to empty table
    // ***************
    
    
    // create one global variable for when we need to access data globally
    window.com_domain = window.com_domain || {};
    // if one click in field, select all. For textarea, the type didn't work so I use the ID instead
    $('input[type="text"],#resort_description').on('click', function(){
        var current_input_val = $(this).val();
        $(this).select();
        $(this).one('focusout', function(){
            if ($(this).val() === '') {
                $(this).val(current_input_val);
            }
        });
    });
    
    if (document.getElementById("myTable1_filter")) {
        // Changes the class of the Search field to display it
        document.getElementById("myTable1_filter").className = "dataTables_filter_show";
    }
    
    
    $(document).on('click', 'a.duplicate_button', function(){
        var id_resort = $(this).closest('tr').attr('data-id_resort');
        var id_player = $(this).closest('tr').attr('data-id_player');
        var username = $(this).closest('tr').attr('data-username');
        $.ajax({
            type: "POST",
            url: Settings.base_url+"admin/"+controllerName+"/duplicate_action",
            data: "id_resort="+id_resort+"&id_player="+id_player+"&username="+username,
            success: function(){
                smToast(Settings.item_duplicated, 'success');
            }
        });
    });
   
    $(document).on('click', 'a.activate_button', function(){
        var id_player = $(this).closest('tr').attr('data-id_player');
        $.ajax({
            type: "POST",
            url: Settings.base_url+"admin/"+controllerName+"/activate_action",
            data: "id_player="+id_player,
            success: function(){
                smToast(Settings.item_activated, 'success');
            }
        });
    });
   
    $(document).on('click', 'a.impersonate_button', function(){
        var id_player = $(this).closest('tr').attr('data-id_player');
        var impersonate_password = $('#impersonate_password').val();
        $.ajax({
            type: "POST",
            url: Settings.base_url+"admin/"+controllerName+"/impersonate_action",
            data: "id_player="+id_player+"&impersonate_password="+impersonate_password,
            dataType: "json",
            success: function(result){
                if (result.returned === true) {
                    var redirect_url;
                    if (result.admin === true) {
                        redirect_url = Settings.base_url+"admin/admin_player_controller";
                    } else {
                        redirect_url = Settings.base_url+"resort_controller";
                    }
                    window.location.href = redirect_url;
                } else {
                    smToast(result.error, 'error');
                }
            },
            error: function(){
                smToast(Settings.something_went_wrong, 'error');
            }
        });
    });
   
   $(document).on('click', 'button.impersonate_button_edit', function(){
        var id_player = $(this).data('id_player');
        var impersonate_password = $('#edit_impersonate_password').val();
        $.ajax({
            type: "POST",
            url: Settings.base_url+"admin/admin_player_controller/impersonate_action",
            data: {id_player: id_player, impersonate_password: impersonate_password},
            dataType: "json",
            success: function(result){
                if (result.returned === true) {
                    var redirect_url;
                    if (result.admin === true) {
                        redirect_url = Settings.base_url+"admin/admin_player_controller";
                    } else {
                        redirect_url = Settings.base_url+"resort_controller";
                    }
                    window.location.href = redirect_url;
                } else {
                    smToast(result.error, 'error');
                }
            },
            error: function(){
                smToast(Settings.something_went_wrong, 'error');
            }
        });
    });

    // Update id_group when value entered
    $('input.id_group').on('change keyup', function(){             // If value of id_group is changed
        var id_group = $(this).val();                 // We store the value of the current element
        $('input.id_group').val(id_group);            // We set the value to the same for all matching fields
    });
    // Update id_sector when value entered
    $('input.id_sector').on('change keyup', function(){             // If value of id_sector is changed
        var id_sector = $(this).val();                 // We store the value of the current element
        $('input.id_sector').val(id_sector);           // We set the value to the same for all matching fields
    });
    // Update lift_type when value entered
    $('select.lift_type').on('change keyup', function(){             // If value of lift_type is changed
        var lift_type = $(this).val();                 // We store the value of the current element
        $('select.lift_type').val(lift_type);          // We set the value to the same in the selects
    });
   
   
    $('div.dataTables_filter').switchClass('dataTables_filter', 'dataTables_filter_show');
   
});


//GOOGLE CHARTS START
if (window.location.href === 'https://www.ski-manager.net/admin/admin_stats_controller' || window.location.href === 'https://localhost/ski-manager/admin/admin_stats_controller' || window.location.href === 'https://localhost/test/admin/admin_stats_controller' || window.location.href === 'https://test.ski-manager.net/admin/admin_stats_controller') {
    // This chart draws a dual axis chart showing revenues and expenses
    function drawCharts() {
        // Define the chart to be drawn in PHP
        var chartConfigs = [
            {
                data: "field1=total_accounts&field2=activated_accounts&field3=newsletter_sent&field4=non_vacation&title=accounts_activated_accounts",
                type: "ComboChart",
                element: 'area_chart_accounts'
            },
            {
                data: "field1=number_resorts&field2=open_resorts&field3=newsletter_sent&field4=newsletter_sent&title=resorts_open_resorts",
                type: "AreaChart",
                element: 'area_chart_resorts'
            },
            {
                data: "field1=lifts&field2=open_lifts&field3=newsletter_sent&field4=newsletter_sent&title=lifts_open_lifts",
                type: "AreaChart",
                element: 'area_chart_lifts'
            },
            {
                data: "field1=slopes&field2=open_slopes&field3=newsletter_sent&field4=newsletter_sent&title=slopes_open_slopes",
                type: "AreaChart",
                element: 'area_chart_slopes'
            },
            {
                data: "field1=completed_achievements&field2=claimed_achievements&field3=newsletter_sent&field4=newsletter_sent&title=completed_claimed_achievements",
                type: "AreaChart",
                element: 'area_chart_achievements'
            },
            {
                data: "field1=daily_visitors_per_open_resort&field2=daily_visitors&field3=newsletter_sent&field4=newsletter_sent&title=daily_visitors",
                type: "LineChart",
                element: 'area_chart_daily_visitors'
            }
        ];

        var chartConstructors = {
            ComboChart: google.visualization.ComboChart,
            AreaChart: google.visualization.AreaChart,
            LineChart: google.visualization.LineChart
        };

        chartConfigs.forEach(function(config) {
            $.ajax({
                type: "POST",
                url: Settings.base_url+"admin/admin_stats_controller/draw_stacked_area_chart",
                data: config.data,
                dataType: "json"
            }).done(function(jsonData) {
                // Create our data table out of JSON data loaded from server.
                var data_chart = new google.visualization.DataTable(jsonData[0]['data']);
                // Set chart's options
                var options = jsonData[1]['options'];
                // Instantiate and draw the chart using validated constructor
                var ChartConstructor = chartConstructors[config.type];
                if (ChartConstructor) {
                    var chart = new ChartConstructor(document.getElementById(config.element));
                    chart.draw(data_chart, options);
                }
            });
        });
    }
    google.charts.load('current', {
        callback: drawCharts,
        packages: ['corechart']
    });
}
    //GOOGLE CHARTS END
