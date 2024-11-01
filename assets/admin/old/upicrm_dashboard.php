<?phpif ( !class_exists('UpiCRMAdminIndex') ):    class UpiCRMAdminIndex{        public function __construct() {            wp_register_script('upicrm_js_flot',  UPICRM_URL.'assets/resources/js/plugin/flot/jquery.flot.cust.min.js', array('jquery'), '1.0');            wp_register_script('upicrm_js_vectormap',  UPICRM_URL.'assets/resources/js/plugin/vectormap/jquery-jvectormap-1.2.2.min.js', array('jquery'), '1.0');            wp_register_script('upicrm_js_chartjs',  UPICRM_URL.'assets/resources/js/plugin/chartjs/chart.min.js', array('jquery'), '1.0');            wp_enqueue_script('upicrm_js_flot');            wp_enqueue_script('upicrm_js_vectormap');            wp_enqueue_script('upicrm_js_chartjs');        }        public function Render() {        if(isset($_GET['action'])) {            switch ( $_GET['action'] ) {                case 'excel_output':                    upicrm_excel_output();                    break;                case 'change_time':                    //$msg = __('Changes saved successfully','upicrm');                    $this->change_time();                    break;            }        }            $UpiCRMStatistics = new UpiCRMStatistics();            $UpiCRMUsers = new UpiCRMUsers();            $UpiCRMLeads = new UpiCRMLeads();            $UpiCRMUIBuilder = new UpiCRMUIBuilder();            $UpiCRMFieldsMapping = new UpiCRMFieldsMapping();            if ($UpiCRMUsers->get_permission() == 1 && $UpiCRMUsers->get_wp_role()=='administrator') {                $UpiCRMUsers->set_permission(2);            }            $user_id = get_current_user_id();            $userOBJ = $UpiCRMUsers->get_inside_by_user_id($user_id);            $colorARR = $UpiCRMStatistics->color_array();            $list_option = $UpiCRMUIBuilder->get_list_option();            $getNamesMap = $UpiCRMFieldsMapping->get();                        $check_date = isset($_COOKIE['upicrm_lead_table_days']) ? $_COOKIE['upicrm_lead_table_days'] : 7;            if ($UpiCRMUsers->get_permission() == 1) {                $is_admin = false;                if(isset($_COOKIE['upicrm_lead_table_from_date'])&&isset($_COOKIE['upicrm_lead_table_to_date'])) {                    $totalLeads = $UpiCRMStatistics->get_total_leads_by_user_id($user_id, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadStatus = $UpiCRMStatistics->get_total_leads_status_by_user_id($user_id, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadUser = $UpiCRMStatistics->get_total_leads_assigned_by_user_id($user_id, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadContry = $UpiCRMStatistics->get_total_leads_group_field_by_user_id($user_id, 17, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadProduct = $UpiCRMStatistics->get_total_leads_group_field_by_user_id($user_id, 13, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadReceivedFrom = $UpiCRMStatistics->get_total_leads_group_field_name_by_user_id($user_id, 'Received From', $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadWebsite = $UpiCRMStatistics->get_total_leads_group_field_by_user_id($user_id, 12, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                } else {                    $totalLeads = $UpiCRMStatistics->get_total_leads_by_user_id($user_id, $check_date);                    $totalLeadStatus = $UpiCRMStatistics->get_total_leads_status_by_user_id($user_id, $check_date);                    $totalLeadUser = $UpiCRMStatistics->get_total_leads_assigned_by_user_id($user_id, $check_date);                    $totalLeadContry = $UpiCRMStatistics->get_total_leads_group_field_by_user_id($user_id, 17, $check_date);                    $totalLeadProduct = $UpiCRMStatistics->get_total_leads_group_field_by_user_id($user_id, 13, $check_date);                    $totalLeadReceivedFrom = $UpiCRMStatistics->get_total_leads_group_field_name_by_user_id($user_id, 'Received From', $check_date);                    $totalLeadWebsite = $UpiCRMStatistics->get_total_leads_group_field_by_user_id($user_id, 12, $check_date);                }                $getLeads = $UpiCRMLeads->get($user_id,1,8);            }            if ($UpiCRMUsers->get_permission() == 2) {                $is_admin = true;                $totalLeadsMe = $UpiCRMStatistics->get_total_leads_by_user_id($user_id);                if(isset($_COOKIE['upicrm_lead_table_from_date'])&&isset($_COOKIE['upicrm_lead_table_to_date'])) {                    $totalLeads = $UpiCRMStatistics->get_total_leads($check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadStatus = $UpiCRMStatistics->get_total_leads_status_by_user_id(0, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadUser = $UpiCRMStatistics->get_total_leads_assigned_by_user_id(0, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadContry = $UpiCRMStatistics->get_total_leads_group_field_by_user_id(0, 17, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadProduct = $UpiCRMStatistics->get_total_leads_group_field_by_user_id(0, 13, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadReceivedFrom = $UpiCRMStatistics->get_total_leads_group_field_name_by_user_id(0, 'Received From', $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                    $totalLeadWebsite = $UpiCRMStatistics->get_total_leads_group_field_by_user_id(0, 12, $check_date, $_COOKIE['upicrm_lead_table_from_date'], $_COOKIE['upicrm_lead_table_to_date']);                } else {                    $totalLeads = $UpiCRMStatistics->get_total_leads($check_date);                    $totalLeadStatus = $UpiCRMStatistics->get_total_leads_status_by_user_id(0, $check_date);                    $totalLeadUser = $UpiCRMStatistics->get_total_leads_assigned_by_user_id(0, $check_date);                    $totalLeadContry = $UpiCRMStatistics->get_total_leads_group_field_by_user_id(0, 17, $check_date);                    $totalLeadProduct = $UpiCRMStatistics->get_total_leads_group_field_by_user_id(0, 13, $check_date);                    $totalLeadReceivedFrom = $UpiCRMStatistics->get_total_leads_group_field_name_by_user_id(0, 'Received From');                    $totalLeadWebsite = $UpiCRMStatistics->get_total_leads_group_field_by_user_id(0, 12, $check_date);                }                $getLeads = $UpiCRMLeads->get(0,1,8);                for ($i=0; $i <= 5; $i++) {                    $weeksArr[] = $UpiCRMStatistics->get_total_leads_by_weeks($i);                }                $weeksArr = array_reverse($weeksArr);            }?><script type="text/javascript">    $j(document).ready(function () {        pageSetUp();        var lineOptions = {            ///Boolean - Whether grid lines are shown across the chart            scaleShowGridLines: true,            //String - Colour of the grid lines            scaleGridLineColor: "rgba(0,0,0,.05)",            //Number - Width of the grid lines            scaleGridLineWidth: 1,            //Boolean - Whether the line is curved between points            bezierCurve: true,            //Number - Tension of the bezier curve between points            bezierCurveTension: 0.4,            //Boolean - Whether to show a dot for each point            pointDot: true,            //Number - Radius of each point dot in pixels            pointDotRadius: 4,            //Number - Pixel width of point dot stroke            pointDotStrokeWidth: 1,            //Number - amount extra to add to the radius to cater for hit detection outside the drawn point            pointHitDetectionRadius: 20,            //Boolean - Whether to show a stroke for datasets            datasetStroke: true,            //Number - Pixel width of dataset stroke            datasetStrokeWidth: 2,            //Boolean - Whether to fill the dataset with a colour            datasetFill: true,            //Boolean - Re-draw chart on page resize            responsive: true        };        <?php if ($is_admin) { ?>        var lineData = {            labels: ["5 <?php _e('Weeks Ago','upicrm'); ?>", "4 <?php _e('Weeks Ago','upicrm'); ?>", "3 <?php _e('Weeks Ago','upicrm'); ?>", "2 <?php _e('Weeks Ago','upicrm'); ?>", "1 <?php _e('Week Ago','upicrm'); ?>", "<?php _e('This Week','upicrm'); ?>"],            datasets: [                {                    label: "My Second dataset",                    fillColor: "rgba(151,187,205,0.2)",                    strokeColor: "rgba(151,187,205,1)",                    pointColor: "rgba(151,187,205,1)",                    pointStrokeColor: "#fff",                    pointHighlightFill: "#fff",                    pointHighlightStroke: "rgba(151,187,205,1)",                    data: [<?php foreach ($weeksArr as $arr) echo $arr.", "; ?>]                }            ]        };        // render chart        var ctx = document.getElementById("lineChart").getContext("2d");        var myNewChart = new Chart(ctx).Line(lineData, lineOptions);        <?php } ?>        if ($j("#site-stats").length) {            /* chart colors default */            var $chrt_border_color = "#efefef";            var $chrt_grid_color = "#DDD"            var $chrt_main = "#E24913";            /* red       */            var $chrt_second = "#6595b4";            /* blue      */            var $chrt_third = "#FF9F01";            /* orange    */            var $chrt_fourth = "#7e9d3a";            /* green     */            var $chrt_fifth = "#BD362F";            /* dark red  */            var $chrt_mono = "#000";            var pageviews = [[1, 75], [3, 87], [4, 93], [5, 127], [6, 116], [7, 137], [8, 135], [9, 130], [10, 167], [11, 169], [12, 179], [13, 185], [14, 176], [15, 180], [16, 174], [17, 193], [18, 186], [19, 177], [20, 153], [21, 149], [22, 130], [23, 100], [24, 50]];            var visitors = [[1, 65], [3, 50], [4, 73], [5, 100], [6, 95], [7, 103], [8, 111], [9, 97], [10, 125], [11, 100], [12, 95], [13, 141], [14, 126], [15, 131], [16, 146], [17, 158], [18, 160], [19, 151], [20, 125], [21, 110], [22, 100], [23, 85], [24, 37]];            //console.log(pageviews)            var plot = $j.plot($j("#site-stats"), [{                data: pageviews,                label: "Leads Received"            }, {                data: visitors,                label: "Lead Accepted"            }], {                series: {                    lines: {                        show: true,                        lineWidth: 1,                        fill: true,                        fillColor: {                            colors: [{                                opacity: 0.1                            }, {                                opacity: 0.15                            }]                        }                    },                    points: {                        show: true                    },                    shadowSize: 0                },                xaxis: {                    mode: "time",                    tickLength: 10                },                yaxis: [{                    min: 20,                    tickLength: 5                }],                grid: {                    hoverable: true,                    clickable: true,                    tickColor: $chrt_border_color,                    borderWidth: 0,                    borderColor: $chrt_border_color,                },                tooltip: true,                tooltipOpts: {                    content: "%s for <b>%x:00 hrs</b> was %y",                    dateFormat: "%y-%0m-%0d",                    defaultTheme: false                },                colors: [$chrt_main, $chrt_second],                xaxis: {                    ticks: 15,                    tickDecimals: 2                },                yaxis: {                    ticks: 15,                    tickDecimals: 0                },            });        }    })</script>                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 bg-cell">                                <div class="text-wellcome">                                    <?php _e('Hello', 'upicrm'); ?> <span class="text-username"><?php echo $UpiCRMUsers->get_by_id($user_id); ?></span>.<br />                                    <?php _e('You Have a total of', 'upicrm'); ?> <span class="text-numbers"><?php echo $totalLeads; ?></span> leads.<br />                                    <?php if ($userOBJ->user_parent_id > 0) { ?>                                        <?php _e('You are reporting to:', 'upicrm'); ?> <span class="a_blue"><?php echo $UpiCRMUsers->get_by_id($userOBJ->user_parent_id); ?></span><br />                                    <?php }                                    $childrens = $UpiCRMUsers->get_childrens_by_parent_id($user_id);                                    if (count($childrens)) { _e('Reporting to you:', 'upicrm');                                        foreach ($childrens as $child_user_id) {                                            echo '<span class="a_blue">'.$UpiCRMUsers->get_by_id($child_user_id->user_id).'</span>';                                            if ($child_user_id !== end($childrens))                                                echo ", ";                                             //echo $child_user_id;                                        }                                        ?><br /><?php } ?>                                    <!--Reporting to you: [list, hierarchy]<br /> -->                                </div>                            </div>                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 bg-cell">                                <div class="new-leads">                                    <div class="new-leads-title"><?php _e('Latest New Leads:', 'upicrm'); ?></div>                                    <div class="new-leads-list">                                        <ul>                                            <?php foreach ($getLeads as $leadObj) { ?><li class="a_blue"><?php                                                    $i=0;                                                    foreach ($list_option as $key => $arr) {                                                        foreach ($arr as $key2 => $value) {                                                            $val = $UpiCRMUIBuilder->lead_routing($leadObj,$key,$key2,$getNamesMap,true);                                                            if ($val && $i < 2) {                                                                echo $val." ";                                                                $i++;                                                            }                                                            //echo $value;                                                        }                                                    }                                                    ?><span><?php echo $leadObj->time; ?></span></li><?php } ?>                                        </ul>                                    </div>                                </div>                            </div>                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 bg-cell">                                <div class="manage-export">                                    <a href="admin.php?page=upicrm_allitems"><i class="fa-width fa fa-cogs"></i><?php _e('Manage Leads', 'upicrm'); ?></a>                                    <a href="admin.php?page=upicrm_index&action=excel_output"><i class="fa-width fa fa-file-excel-o"></i><?php _e('Export to Excel', 'upicrm'); ?></a>                                </div>                            </div>                        </div>            <div class="clearfix"></div>            <br /><br />   <div id="ChooseDate">        <?php _e('Default date range:','upicrm'); ?> &nbsp;&nbsp;       <a href="admin.php?page=upicrm_index&action=change_time&days=1" data-id="1" class="btn btn-default"><?php _e('1 Day','upicrm'); ?></a>       <a href="admin.php?page=upicrm_index&action=change_time&days=7" data-id="7" class="btn btn-default"><?php _e('7 Days','upicrm'); ?></a>       <a href="admin.php?page=upicrm_index&action=change_time&days=30" data-id="30" class="btn btn-default"><?php _e('1 Month','upicrm'); ?></a>       <a href="admin.php?page=upicrm_index&action=change_time&days=90" data-id="90" class="btn btn-default"><?php _e('3 Months','upicrm'); ?></a>       <a href="admin.php?page=upicrm_index&action=change_time&days=0" data-id="0" class="btn btn-default"><?php _e('All Time','upicrm'); ?></a>       <a href="javascript:void(0);" id="custom_date" data-id="custom" class="btn btn-default"><?php _e('Custom','upicrm'); ?></a>   </div>   <div id="upicrm_date_range">       <form action="" method="get">            <div class="input-group">                 <label><?php _e('from:','upicrm'); ?></label>                 <input type="text" name="from_date" value="<?php echo $_COOKIE['upicrm_lead_table_from_date']; ?>" class="form-control datepicker" data-dateformat="yy-mm-dd">                 <i class="fa fa-calendar"></i>           </div>            <div class="input-group">                 <label><?php _e('to:','upicrm'); ?></label>                 <input type="text" name="to_date" value="<?php echo $_COOKIE['upicrm_lead_table_to_date']; ?>" class="form-control datepicker" data-dateformat="yy-mm-dd">                 <i class="fa fa-calendar"></i>           </div>           <input type="hidden" name="page" value="upicrm_index">           <input type="hidden" name="action" value="change_time">           <input type="hidden" name="days" value="custom">           <input type="submit" class="btn btn-primary" value="<?php _e('Apply','upicrm'); ?> " style="margin-left: -29px;">        </form>   </div>   <br /><br />            <!-- row -->            <div class="">                <article class="col-sm-12">                    <!-- new widget -->                    <div class="jarviswidget" id="wid-id-0" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">                        <header>                            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>                            <h2><?php _e('Lead Overview', 'upicrm'); ?></h2>                        </header>                        <!-- widget div-->                        <div class="no-padding">                            <!-- widget edit box -->                            <div class="jarviswidget-editbox">                            </div>                            <!-- end widget edit box -->                            <div class="widget-body">                                <!-- content -->                                <div id="myTabContent" class="tab-content">                                    <div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">                                        <div class="row no-space">                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                                                <span class="demo-liveupdate-1" style="display: none"><span class="onoffswitch-title">Live switch</span> <span class="onoffswitch">                                                        <input type="checkbox" name="start_interval" class="onoffswitch-checkbox" id="start_interval">                                                        <label class="onoffswitch-label" for="start_interval">                                                            <span class="onoffswitch-inner" data-swchon-text="ON" data-swchoff-text="OFF"></span>                                                            <span class="onoffswitch-switch"></span>                                                        </label>                                                    </span></span>                                                <!--<div id="site-stats" class="chart has-legend"></div>-->                                                <div class="widget-body">                                                    <!-- this is what the user will see -->                                                    <canvas id="lineChart" height="40"></canvas>                                                </div>                                            </div>                                        </div>                                    </div>                                    <!-- end s1 tab pane -->                                </div>                                <!-- end content -->                            </div>                        </div>                        <!-- end widget div -->                    </div>                    <!-- end widget -->                </article>            </div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 status-pie" style="display: none;">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Status', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <canvas id="StatusPie" width="250" height="250"></canvas>        </div>    </div> </div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 status-pie" style="display: none;">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Assigned to', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <canvas id="AssignedPie" width="250" height="250"></canvas>        </div>    </div> </div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Status', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <table class="table table-striped table-bordered data_table" width="100%">               <thead>                    <tr>                        <th data-class="expand"><?php _e('Status', 'upicrm'); ?></th>                        <th data-class="expand"><?php _e('Number', 'upicrm'); ?></th>                    </tr>               </thead>               <tbody>                <?php foreach ($totalLeadStatus as $arr) { ?><tr><td><?php echo $arr['lead_status_name'];?></td><td><?php echo $arr['count'];?></td></tr><?php } ?>               </tbody>            </table>        </div>    </div> </div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Assigned to', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <table class="table table-striped table-bordered data_table" width="100%">               <thead>                    <tr>                        <th data-class="expand"><?php _e('Status', 'upicrm'); ?></th>                        <th data-class="expand"><?php _e('Number', 'upicrm'); ?></th>                    </tr>               </thead>               <tbody>                <?php foreach ($totalLeadUser as $arr) { ?><tr><td><?php echo $arr['user_name'];?></td><td><?php echo $arr['count'];?></td></tr><?php } ?>               </tbody>            </table>        </div>    </div> </div><div class="clearfix"></div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Country', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <table class="table table-striped table-bordered data_table" width="100%">               <thead>                    <tr>                        <th data-class="expand"><?php _e('Country', 'upicrm'); ?></th>                        <th data-class="expand"><?php _e('Number', 'upicrm'); ?></th>                    </tr>               </thead>               <tbody>                <?php foreach ($totalLeadContry as $key => $value) { ?><tr><td><?php echo $key;?></td><td><?php echo $value;?></td></tr><?php } ?>               </tbody>            </table>        </div>    </div> </div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Product', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <table class="table table-striped table-bordered data_table" width="100%">               <thead>                    <tr>                        <th data-class="expand"><?php _e('Product', 'upicrm'); ?></th>                        <th data-class="expand"><?php _e('Number', 'upicrm'); ?></th>                    </tr>               </thead>               <tbody>                <?php foreach ($totalLeadProduct as $key => $value) { ?><tr><td><?php echo $key;?></td><td><?php echo $value;?></td></tr><?php } ?>               </tbody>            </table>        </div>    </div> </div><div class="clearfix"></div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Received From', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <table class="table table-striped table-bordered data_table" width="100%">               <thead>                    <tr>                        <th data-class="expand"><?php _e('Received From', 'upicrm'); ?></th>                        <th data-class="expand"><?php _e('Number', 'upicrm'); ?></th>                    </tr>               </thead>               <tbody>                <?php foreach ($totalLeadReceivedFrom as $key => $value) { ?><tr><td><?php echo $key;?></td><td><?php echo $value;?></td></tr><?php } ?>               </tbody>            </table>        </div>    </div> </div> <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    <div class="jarviswidget" id="" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">        <header>            <span class="widget-icon"><i class="glyphicon glyphicon-stats txt-color-darken"></i></span>            <h2><?php _e('Leads / Website', 'upicrm'); ?></h2>        </header>        <div class="widget-body" style="text-align: center;">            <table class="table table-striped table-bordered data_table" width="100%">               <thead>                    <tr>                        <th data-class="expand"><?php _e('Website', 'upicrm'); ?></th>                        <th data-class="expand"><?php _e('Number', 'upicrm'); ?></th>                    </tr>               </thead>               <tbody>               <?php foreach ($totalLeadWebsite as $key => $value) { ?><tr><td><?php echo $key;?></td><td><?php echo $value;?></td></tr><?php } ?>               </tbody>            </table>        </div>    </div> </div>            <script type="text/javascript">    $j(document).ready(function ($) {        var data = [            <?php            $i=0;            foreach ($totalLeadStatus as $arr) { ?>            {                value: <?php echo $arr['count'];?>,                color:"<?php echo $colorARR[$i];?>",                label: "<?php echo $arr['lead_status_name'];?>"            },            <?php            $i++;            } ?>        ]        option = [];        var ctx = document.getElementById("StatusPie").getContext("2d");        var myPieChart = new Chart(ctx).Pie(data,option);        var data = [            <?php            $i=0;            foreach ($totalLeadUser as $arr) { ?>            {                value: <?php echo $arr['count'];?>,                color:"<?php echo $colorARR[$i];?>",                label: "<?php echo $arr['user_name'];?>",            },            <?php            $i++;            } ?>        ]        option = [];        var ctx = document.getElementById("AssignedPie").getContext("2d");        var myPieChart = new Chart(ctx).Pie(data,option);        $('.data_table').DataTable({ "order": [[ 1, "desc" ]] });        <?php if (isset($_COOKIE['upicrm_lead_table_days'])) { ?>                var cda = $("#ChooseDate a[data-id='<?php echo $_COOKIE['upicrm_lead_table_days']; ?>']");            <?php } else { ?>                var cda = $("#ChooseDate a[data-id='7']");            <?php } ?>            cda.removeClass('btn-default');            cda.addClass('btn-primary');            function CustomDateOpen() {                $("#upicrm_date_range").css("display","inline-block");            }             $("#custom_date").click(function() {                CustomDateOpen();                $("#ChooseDate a").removeClass('btn-primary');                $("#ChooseDate a").addClass('btn-default');                $("#custom_date").addClass('btn-primary');            });            <?php if ($_COOKIE['upicrm_lead_table_days'] == "custom") { ?>CustomDateOpen();<?php } ?>    });</script><?php        }    function change_time() {        @setcookie("upicrm_lead_table_days", $_GET['days']);        $_COOKIE['upicrm_lead_table_days'] = $_GET['days'];        @setcookie("upicrm_lead_table_from_date", $_GET['from_date']);        $_COOKIE['upicrm_lead_table_from_date'] = $_GET['from_date'];        @setcookie("upicrm_lead_table_to_date", $_GET['to_date']);        $_COOKIE['upicrm_lead_table_to_date'] = $_GET['to_date'];    }    }endif;