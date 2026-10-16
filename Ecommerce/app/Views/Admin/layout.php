<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo esc($title ?? 'Painel'); ?> | Utopia Admin</title>
  <meta name="base-url" content="<?php echo base_url() ?>">
  <meta name="csrf-token" content="<?php echo csrf_hash() ?>">
  <meta name="csrf-header" content="<?php echo csrf_header() ?>">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,600,700&display=fallback">
  <!-- Font Awesome -->
  <?php echo link_tag('assets/plugins/fontawesome-free/css/all.min.css') ?>
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- iCheck -->
  <?php echo link_tag('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>
  <!-- Theme style -->
  <?php echo link_tag('assets/dist/css/adminlte.min.css') ?>
  <!-- overlayScrollbars -->
  <?php echo link_tag('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>
  <!-- sweetalert 2 -->
  <?php echo link_tag('assets/plugins/sweetalert2/sweetalert2.min.css') ?>
  <!-- Ajustes do painel -->
  <?php echo link_tag('assets/admin/css/admin.css') ?>

  <?php echo $this->renderSection('styles') ?>
</head>

<?php
  $menuAberto = static fn (array $padroes) => array_filter($padroes, static fn ($padrao) => url_is($padrao)) !== [];
?>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url(route_to('loja')) ?>" target="_blank" title="Ver loja">
            <i class="fas fa-store"></i><span class="d-none d-sm-inline ml-1">Ver loja</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" title="Sair" href="<?= base_url(route_to('signOut')) ?>">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="<?= base_url(route_to('home')) ?>" class="brand-link">
        <?php
          echo img([
            'src' => 'assets/images/logo-utopia.jpeg',
            'alt' => 'Utopia',
            'class' => 'brand-image img-square',
            'style' => 'opacity: .9'
          ])
        ?>
        <span class="brand-text font-weight-bold">Utopia</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <?php echo img([
                  'src' => 'assets/images/avatars/1.jpg',
                  'alt' => 'avatar',
                  'class' => 'img-circle elevation-2',
                  'width' => '160'
              ]) ?>
          </div>
          <div class="info">
            <a href="#" class="d-block">
              <?php echo esc(session()->name) ?>
            </a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="<?= base_url(route_to('home')) ?>" class="nav-link <?= url_is('admin') ? 'active' : '' ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item <?= $menuAberto(['admin/categoria*', 'admin/produto*']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= $menuAberto(['admin/categoria*', 'admin/produto*']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-box-open"></i>
                <p>
                  Estoque
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url(route_to('categorias')) ?>" class="nav-link <?= url_is('admin/categoria*') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Categorias</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(route_to('produtos')) ?>" class="nav-link <?= url_is('admin/produto*') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Produtos</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item <?= $menuAberto(['admin/pedido*', 'admin/devolucoes']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= $menuAberto(['admin/pedido*', 'admin/devolucoes']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>
                  Vendas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url(route_to('pedidos')) ?>" class="nav-link <?= url_is('admin/pedido*') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Pedidos</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(route_to('devolucoes')) ?>" class="nav-link <?= url_is('admin/devolucoes') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Devoluções</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item <?= $menuAberto(['admin/financeiro*']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= $menuAberto(['admin/financeiro*']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-wallet"></i>
                <p>
                  Financeiro
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url(route_to('financeiro')) ?>" class="nav-link <?= url_is('admin/financeiro') || url_is('admin/financeiro/lancamento*') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lançamentos</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(route_to('fluxo_caixa')) ?>" class="nav-link <?= url_is('admin/financeiro/fluxo-caixa') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Fluxo de caixa</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item <?= $menuAberto(['admin/clientes', 'admin/vendedores']) ? 'menu-open' : '' ?>">
              <a href="#" class="nav-link <?= $menuAberto(['admin/clientes', 'admin/vendedores']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-address-card"></i>
                <p>
                  Comercial
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url(route_to('clientes')) ?>" class="nav-link <?= url_is('admin/clientes') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Clientes</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(route_to('vendedores')) ?>" class="nav-link <?= url_is('admin/vendedores') ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Vendedores</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-header">LOJA</li>
            <li class="nav-item">
              <a href="<?= base_url(route_to('loja')) ?>" class="nav-link" target="_blank">
                <i class="nav-icon fas fa-store"></i>
                <p>Abrir vitrine</p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><?php echo esc($title); ?></h1>
              <?php if (! empty($subtitle)): ?>
                <small class="text-muted"><?php echo esc($subtitle); ?></small>
              <?php endif; ?>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= base_url(route_to('home')) ?>">Home</a></li>
                <li class="breadcrumb-item active"><?php echo esc($title); ?></li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <?php $mensagemSucesso = session()->getFlashdata('success'); ?>
      <?php $mensagemErro = session()->getFlashdata('error'); ?>
      <?php $errosValidacao = session('errors'); ?>
      <?php if ($mensagemSucesso || $mensagemErro || $errosValidacao): ?>
        <div class="container-fluid">
          <?php if ($mensagemSucesso): ?>
            <div class="alert alert-success alert-dismissible fade show">
              <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">&times;</button>
              <i class="icon fas fa-check"></i><?php echo esc($mensagemSucesso); ?>
            </div>
          <?php endif; ?>
          <?php if ($mensagemErro): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">&times;</button>
              <i class="icon fas fa-ban"></i><?php echo esc($mensagemErro); ?>
            </div>
          <?php endif; ?>
          <?php if ($errosValidacao): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">&times;</button>
              <h5 class="mb-1"><i class="icon fas fa-exclamation-triangle"></i>Revise os campos</h5>
              <ul class="mb-0 pl-3">
                <?php foreach ((array) $errosValidacao as $erroValidacao): ?>
                  <li><?php echo esc($erroValidacao); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php echo $this->renderSection('content') ?>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>Todos os direitos reservados <a href="#">&nbsp;DecodeZero&nbsp;</a> &copy; <?php echo date('Y'); ?>.</strong>
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.2.0
      </div>
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <?php echo script_tag('assets/plugins/jquery/jquery.min.js') ?>
  <!-- Bootstrap 4 -->
  <?php echo script_tag('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>
  <!-- overlayScrollbars -->
  <?php echo script_tag('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>
  <!-- AdminLTE App -->
  <?php echo script_tag('assets/dist/js/adminlte.js') ?>
  <!-- sweetalert 2 -->
  <?php echo script_tag('assets/plugins/sweetalert2/sweetalert2.all.min.js') ?>

  <?php echo $this->renderSection('scripts') ?>
</body>

</html>
