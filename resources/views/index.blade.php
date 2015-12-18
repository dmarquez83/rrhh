<!doctype html>
<html lang="es" ng-app="app">
<head>
    <meta charset="UTF-8">
    <title>SAE | Sistema de administraci&oacute;n Empresarial</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta content="Sistema de Administraci&oacute;n Empresarial" name="description" />
    <meta content="Code Studios" name="author" />
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="dist/css/components.min.css" rel="stylesheet" />
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="dist/css/theme.min.css" rel="stylesheet" />
    <link href="dist/css/main.min.css" rel="stylesheet" />




</head>
<body>
    {{--*/
    if(Session::has('userInformation')){
        $userInformation = Session::get('userInformation');
        $companyInformation = Session::get('companyInformation');
        $currentWarehouse = Session::get('currentWarehouse');
        $companyInformation = Session::get('companyInformation');
        $SAEBASIC =  Session::get('saeBasic');
    }
    /*--}}

    <div id="page-container" class="fade page-header-fixed in page-sidebar-fixed" style="padding-top:60px">

        <div id="header" class="header navbar navbar-default navbar-fixed-top navbar-inverse">

            <div class="container-fluid">

                <div class="navbar-header">
                    <a href="http:\\www.sae-erp.com" class="navbar-brand" target="_blank">
                        <img src="dist/images/system/logo-sae.png" width="100" class="sae-logo" /></a>
                    <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <ul class="nav navbar-nav navbar-left hidden-sm hidden-xs">
                    <li>
                        <a href="#" style="padding-top: 0px !important; padding-bottom: 0px !important;">

                            <h3 style="margin-top: 13px !important; margin-bottom: 0px !important; color: #a8acb1 !important;">
                                @if ($companyInformation != null)
                                    {{$companyInformation['businessName']}}
                                @endif
                                @if ($companyInformation== null)
                                     Nombre Empresa
                                @endif
                                <small style="color: #a8acb1;"> ( {{ $currentWarehouse['name'] }} )</small>
                            </h3>
                        </a>
                    </li>
                </ul>


                @if (!$SAEBASIC)
                    <ul class="nav navbar-nav navbar-left hidden-sm hidden-xs">
                        <li><a class="cursorPointer" ng-click="openCompanyModal()" ng-controller="CompanySelectionCtrl"><i class="fa fa-building"></i>&nbsp;Cambiar Empresa</a></li>
                    </ul>
                @endif

                @if (!$SAEBASIC)
                    <ul class="nav navbar-nav navbar-left hidden-sm hidden-xs">
                        <li><a class="cursorPointer" ng-click="openWarehouses()" ng-controller="WarehousesSelectionCtrl"><i class="fa fa-simplybuilt"></i>&nbsp;Cambiar Bodegas</a></li>
                    </ul>
                @endif

                <ul class="nav navbar-nav navbar-right hidden-sm hidden-xs">
                    <li>
                        <a href="javascript:launchFullScreen()"><i class="fa fa-arrows-alt fa-lg"></i></a>
                    </li>
                    <!-- Icono para notificaciones
                    <li class="dropdown hidden-sm hidden-xs">
                        <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle f-s-14">
                            <i class="fa fa-bell-o"></i>
                            <span class="label">5</span>
                        </a>
                    </li> -->
                    <li class="dropdown navbar-user">

                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                <img alt="image" class="img-circle" id="user-button"
                                     src="{{{ isset($userInformation['employee']['photo']) ?
                                         $userInformation['employee']['photo']['src'] :
                                        'dist/images/user/default-user-icon.jpg' }}}"
                                     width="40"/>
                                <span class="hidden-xs">
                                  {{{$userInformation['systemUser']['username'] }}}
                                </span>
                                <b class="caret"></b>
                            </a>

                        <ul class="dropdown-menu animated fadeInLeft">
                            <li class="arrow"></li>
                            @if (!$SAEBASIC)
                            <li><a href="javascript:;">Perfil</a></li>
                            <li><a href="javascript:;"><span class="badge badge-danger pull-right">2</span> Notificaciones</a></li>
                            <li class="divider"></li>
                            @endif
                            <li><a href="/logout">Salir</a></li>
                        </ul>
                    </li>
                </ul>

            </div>

        </div>


    <div ui-view id="uiView" id="content" class="content"></div>
    <div id="sidebar" class="sidebar sidebar-grid">
      	<div data-scrollbar="true" data-height="100%" style="overflow: hidden; width: auto; height: 100%;">

            <ul class="nav">
                <li class="nav-header">Men&uacute;</li>

                @foreach($userInformation['modules'] as $module)
                    @if ($module['isSelected'])
                        <li ng-class="{'has-sub active': $state.includes('{{ $module['state'] }}'), 'has-sub': !$state.includes('{{ $module['state'] }}')}"
                            class="has-sub">
                            <a href="" id="menu{{ $module['state'] }}"><b class="caret pull-right"></b><i class="fa {{ $module['cssClass'] }}"></i><span>{{ $module['name'] }}</span></a>
                            @if (isset($module['submodules']))
                                @if ($module['isSelected'])
                                    <ul class="sub-menu">
                                        @foreach($module['submodules'] as $submodule)
                                            @if ($submodule['isVisible'] && $submodule['isSelected'])
                                                <li ui-sref-active="active"><a id="menu{{ $module['state'] }}{{ $submodule['state'] }}" ui-sref="{{ $module['state'] }}.{{ $submodule['state'] }}">{{ $submodule['name'] }}</a></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <div class="sidebar-bg"></div>

    </div>

    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top">
        <i class="fa fa-angle-up"></i></a>
    </div>

    <script src="dist/js/components.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="dist/js/main.min.js"></script>
    <script type="text/javascript">
        var USER_INFO = <?php echo json_encode($userInformation);?>;
        var MODULES = <?php echo json_encode($userInformation['modules']);?>;
        var COMPANY_INFORMATION = <?php echo json_encode($companyInformation);?>;
        var WAREHOUSE_INFORMATION = <?php echo json_encode($currentWarehouse);?>;
        var CSRF_TOKEN = '<?php echo csrf_token(); ?>';
        App.init();
        handleSlimScroll();
    </script>


    <!--
    <div class="modal-backdrop fade in" ng-class="{in: animate}" ng-style="{'z-index': 1040 + (index &amp;&amp; 1 || 0) + index*10}" modal-backdrop="" style="z-index: 1040;"></div>
    -->
</body>
</html>
