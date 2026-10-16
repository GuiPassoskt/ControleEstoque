<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers\Client');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Loja (namespace padrão: App\Controllers\Client)
$routes->get('/', 'HomeController::index', ['as' => 'loja']);
$routes->get('produto/(:num)', 'ProdutoController::show/$1', ['as' => 'loja_produto']);

$routes->get('carrinho', 'CarrinhoController::index', ['as' => 'carrinho']);
$routes->post('carrinho/adicionar', 'CarrinhoController::adicionar', ['as' => 'carrinho_adicionar']);
$routes->post('carrinho/atualizar', 'CarrinhoController::atualizar', ['as' => 'carrinho_atualizar']);
$routes->post('carrinho/(:num)/remover', 'CarrinhoController::remover/$1', ['as' => 'carrinho_remover']);

$routes->get('checkout', 'CheckoutController::index', ['as' => 'checkout']);
$routes->get('checkout/frete', 'CheckoutController::frete', ['as' => 'checkout_frete']);
$routes->post('checkout', 'CheckoutController::finalizar', ['as' => 'checkout_finalizar']);
$routes->get('checkout/retorno', 'CheckoutController::retorno', ['as' => 'checkout_retorno']);

$routes->get('pedido/(:num)', 'PedidoController::show/$1', ['as' => 'pedido_cliente']);

// Webhook do Mercado Pago (fora do filtro CSRF, ver Config\Filters)
$routes->post('notificacoes/pagamento', '\App\Controllers\NotificacaoController::pagamento', ['as' => 'notificacao_pagamento']);

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
	$routes->get('/', 'HomeController::index', ['as' => 'home']);

	$routes->get('categorias', 'CategoriaController::index', ['as' => 'categorias']);
	$routes->get('categoria/nova', 'CategoriaController::create', ['as' => 'categoria_create']);
	$routes->post('categoria', 'CategoriaController::store', ['as' => 'categoria_store']);
	$routes->get('categoria/editar/(:num)', 'CategoriaController::edit/$1', ['as' => 'categoria_edit']);
	$routes->put('categoria/(:num)', 'CategoriaController::update/$1', ['as' => 'categoria_update']);
	$routes->delete('categoria/(:num)/excluir', 'CategoriaController::delete/$1', ['as' => 'categoria_delete']);

	$routes->get('produtos', 'ProdutoController::index', ['as' => 'produtos']);
	$routes->get('produto/nova', 'ProdutoController::create', ['as' => 'produto_create']);
	$routes->post('produto', 'ProdutoController::store', ['as' => 'produto_store']);
	$routes->get('produto/(:num)/editar', 'ProdutoController::edit/$1', ['as' => 'produto_edit']);
	$routes->put('produto/(:num)', 'ProdutoController::update/$1', ['as' => 'produto_update']);
	$routes->delete('produto/(:num)/excluir', 'ProdutoController::delete/$1', ['as' => 'produto_delete']);
	
	$routes->get('pedidos', 'PedidoController::index', ['as' => 'pedidos']);
	$routes->get('pedido/(:num)', 'PedidoController::show/$1', ['as' => 'pedido_show']);
	$routes->post('pedido/(:num)/status', 'PedidoController::status/$1', ['as' => 'pedido_status']);

	$routes->get('financeiro', 'FinanceiroController::index', ['as' => 'financeiro']);
	$routes->get('financeiro/lancamento/novo', 'FinanceiroController::create', ['as' => 'financeiro_create']);
	$routes->post('financeiro/lancamento', 'FinanceiroController::store', ['as' => 'financeiro_store']);
	$routes->delete('financeiro/lancamento/(:num)/excluir', 'FinanceiroController::delete/$1', ['as' => 'financeiro_delete']);
	$routes->get('financeiro/fluxo-caixa', 'FinanceiroController::fluxoCaixa', ['as' => 'fluxo_caixa']);

	$routes->get('devolucoes', 'DevolucaoController::index', ['as' => 'devolucoes']);
	$routes->get('clientes', 'ClienteController::index', ['as' => 'clientes']);
	$routes->get('vendedores', 'VendedorController::index', ['as' => 'vendedores']);
});

$routes->group('auth', ['namespace' => 'App\Controllers\Auth'], function($routes) {
	$routes->get('registro', 'RegistroController::index');
	$routes->post('registro', 'RegistroController::store');
	$routes->get('login', 'LoginController::index', ['as' => 'login']);
	$routes->post('login/signIn', 'LoginController::signIn');
	$routes->get('logout', 'LoginController::signOut', ['as' => 'signOut']);
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
