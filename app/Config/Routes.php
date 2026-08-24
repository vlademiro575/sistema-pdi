<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
//$routes->get('/', 'Home::index');
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

// Rotas de Autenticação
$routes->get('login', 'AuthController::login');
$routes->post('login/autenticar', 'AuthController::autenticar');
$routes->get('logout', 'AuthController::logout');

// Dashboard
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

// Bolsistas
$routes->group('bolsistas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'BolsistaController::index');
    $routes->get('novo', 'BolsistaController::new');
    $routes->post('salvar', 'BolsistaController::create');
    $routes->get('editar/(:num)', 'BolsistaController::edit/$1');
    $routes->post('atualizar/(:num)', 'BolsistaController::update/$1');
    $routes->get('deletar/(:num)', 'BolsistaController::delete/$1');
});

// Professores
$routes->group('professores', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'ProfessorController::index');
    $routes->get('novo', 'ProfessorController::new');
    $routes->post('salvar', 'ProfessorController::create');
    $routes->get('editar/(:num)', 'ProfessorController::edit/$1');
    $routes->post('atualizar/(:num)', 'ProfessorController::update/$1');
    $routes->get('deletar/(:num)', 'ProfessorController::delete/$1');
});

// Fundações
$routes->group('fundacoes', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'FundacaoController::index');
    $routes->get('novo', 'FundacaoController::new');
    $routes->post('salvar', 'FundacaoController::create');              
    $routes->get('editar/(:num)', 'FundacaoController::edit/$1');
    $routes->post('atualizar/(:num)', 'FundacaoController::update/$1');
    $routes->get('deletar/(:num)', 'FundacaoController::delete/$1');
});

// Módulo de Projetos (Mestre)
$routes->group('projetos', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'ProjetoController::index');
    $routes->get('novo', 'ProjetoController::new');
    $routes->post('create', 'ProjetoController::create');
    $routes->get('editar/(:num)', 'ProjetoController::edit/$1');
    $routes->post('update/(:num)', 'ProjetoController::update/$1');
    $routes->get('delete/(:num)', 'ProjetoController::delete/$1');
    $routes->get('gerenciar/(:num)', 'ProjetoController::gerenciar/$1');
});

// Módulo de Rubricas (Detalhe)
$routes->group('rubricas', ['filter' => 'auth'], function($routes) {
    $routes->post('create', 'RubricaController::create');
    $routes->post('update/(:num)', 'RubricaController::update/$1');
    $routes->get('delete/(:num)', 'RubricaController::delete/$1');
});

// Módulo de Vínculo de Bolsistas (Detalhe do Projeto)
$routes->group('projetos-bolsistas', ['filter' => 'auth'], function($routes) {
    $routes->post('create', 'ProjetoBolsistaController::create');
    $routes->get('delete/(:num)', 'ProjetoBolsistaController::delete/$1');
});

// Módulo de Movimentações de Rubrica (Ajustes e Extrato)
$routes->group('movimentacoes-rubricas', ['filter' => 'auth'], function($routes) {
    // Processa o formulário do Modal
    $routes->post('createAjuste', 'MovimentacaoRubricaController::createAporteAjuste');
});

$routes->group('movimentacoes', ['filter' => 'auth'], function($routes) {
    // Carrega a tela de visualização do extrato
    $routes->get('extrato/(:num)', 'MovimentacaoRubricaController::extrato/$1');
});

// Módulo de Despesas
$routes->group('despesas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'DespesaController::index');
    $routes->get('novo', 'DespesaController::new');
    $routes->post('create', 'DespesaController::create');
    $routes->get('editar/(:num)', 'DespesaController::edit/$1');
    $routes->post('update/(:num)', 'DespesaController::update/$1');
    $routes->get('delete/(:num)', 'DespesaController::delete/$1');
    $routes->post('mudar-status/(:num)', 'DespesaController::mudarStatus/$1');
    
    // Rota AJAX para carregamento das Rubricas
    $routes->get('rubricas-por-projeto/(:num)', 'DespesaController::getRubricasPorProjeto/$1');
});

