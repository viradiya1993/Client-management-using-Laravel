<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <title>@lang('modules.projects.viewGanttChart')</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css'>

    <!-- This is Sidebar menu CSS -->
    <link href="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/bower_components/toast-master/css/jquery.toast.css') }}"   rel="stylesheet">
    <link href="{{ asset('plugins/bower_components/sweetalert/sweetalert.css') }}"   rel="stylesheet">

    <!-- This is a Animation CSS -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">

@stack('head-script')

<!-- This is a Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- color CSS you can use different color css from css/colors folder -->
    <!-- We have chosen the skin-blue (default.css) for this starter
       page. However, you can choose any other skin from folder css / colors .
       -->
    <link href="{{ asset('css/colors/default.css') }}" id="theme"  rel="stylesheet">
    <link href="{{ asset('plugins/froiden-helper/helper.css') }}"   rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}"   rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="//cdn.dhtmlx.com/gantt/edge/skins/dhtmlxgantt_broadway.css" rel="stylesheet">

    <style>
        .sidebar .notify  {
            margin: 0 !important;
        }
        .sidebar .notify .heartbit {
            top: -23px !important;
            right: -15px !important;
        }
        .sidebar .notify .point {
            top: -13px !important;
        }
        .top-notifications .message-center .user-img{
            margin: 0 0 0 0 !important;
        }
    </style>
</head>
<body class="fix-sidebar">
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">

    <!-- Left navbar-header end -->
    <!-- Page Content -->
    <div id="page-wrapper" style="margin-left: 0px !important;">
        <div class="container-fluid">

            <!-- .row -->
            <div class="row" style="margin-top: 70px; !important;">
                <div class="col-md-offset-1 col-md-10 col-md-offset-1">
                    <h2>{{ $project->project_name }} @lang('modules.projects.viewGanttChart')</h2>
                    <div class="card">
                        <div class="card-body">
                            <div id="gantt_here" style='width:100%; height:100vh;'></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js'></script>

<!-- Sidebar menu plugin JavaScript -->
<script src="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
<!--Slimscroll JavaScript For custom scroll-->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('js/waves.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('plugins/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="//cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
<script src="//cdn.dhtmlx.com/gantt/edge/locale/locale_{{ $global->locale }}.js"></script>

<script type="text/javascript">

    gantt.config.xml_date = "%Y-%m-%d %H:%i:%s";

    gantt.templates.task_class = function (st, end, item) {
        return item.$level == 0 ? "gantt_project" : ""
    };

    gantt.config.scale_unit = "month";
    gantt.config.date_scale = "%F, %Y";

    gantt.config.scale_height = 50;

    gantt.config.subscales = [
        {unit: "day", step: 1, date: "%j, %D"}
    ];

    gantt.config.server_utc = false;

    // default columns definition
    gantt.config.columns=[
        {name:"text",       label:"Task name",  tree:true, width:'*' },
        {name:"start_date", label:"Start time", align: "center" },
        {name:"duration",   label:"Duration",   align: "center" }
    ];

    //defines the text inside the tak bars
    gantt.templates.task_text = function (start, end, task) {
        if ( task.$level > 0 ){
            return task.text + ", <b> @lang('modules.tasks.assignTo'):</b> " + task.users;
        }
        return task.text;

    };

    gantt.attachEvent("onTaskCreated", function(task){
        //any custom logic here
        return false;
    });

    gantt.attachEvent("onBeforeTaskDrag", function(id, mode, e){
        return false;
    });

    gantt.attachEvent("onBeforeLightbox", function(id) {
        return false;
    });

    gantt.init("gantt_here");

    gantt.config.open_tree_initially = true;
    gantt.load('{{ route("front.gantt-data", $ganttProjectId) }}');

</script>

</body>
</html>