<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Yolanda Flores - Sistema de administração</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/static/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/static/css/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/static/css/blue.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="/static/css/morris.css">


    <!-- plugin calendario -->

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">


    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="/admin/inicio" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>YF</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Yolanda </b>Flores</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs">
                                <i class="fa fa-user"></i> Bem-Vindo <?php echo $usuario->getNome(); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#"><i class="fa fa-fw fa-gear"></i> Minha Conta</a>
                            </li>
                            <li>
                                <a href="/admin/sair"><i class="fa fa-fw fa-power-off"></i> Sair</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Menu da esquerda -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header">Menu de Navegação</li>

                <li>
                    <a href="/admin/inicio">
                        <i class="glyphicon glyphicon-home"></i> <span>Inicio</span>
                    </a>
                </li>

                <li>
                    <a href="/admin/produto">
                        <i class="glyphicon glyphicon-barcode"></i> <span>Produtos</span>
                    </a>
                </li>

                <li>
                    <a href="/admin/estoque">
                        <i class="fa fa-cube"></i> <span>Estoque</span>
                    </a>
                </li>

                <li>
                    <a href="/admin/baixa">
                        <i class="glyphicon glyphicon-shopping-cart"></i> <span>Baixa</span>
                    </a>
                </li>

                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-area-chart"></i>
                        <span>Relatórios</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/admin/relatorio/periodo"><i class="fa fa-circle-o"></i> Período</a></li>
                        <li><a href="/admin/relatorio/agregado"><i class="fa fa-circle-o"></i> Valor Agregado</a></li>
                        <li><a href="/admin/relatorio/quebra"><i class="fa fa-circle-o"></i> Quebra</a></li>
                    </ul>
                </li>

            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
    <!-- Fim Menu da esquerda -->

