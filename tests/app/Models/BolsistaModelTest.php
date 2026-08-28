<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;

class BolsistaModelTest extends CIUnitTestCase
{
    // Habilita as transações de teste para limpar a base de dados no fim
    use DatabaseTestTrait; 

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. Iniciar o serviço de Sessão do CodeIgniter
        $session = Services::session();
        
        // 2. Injetar o utilizador falso (mock) na sessão
        // Isto simula que o utilizador 'admin.teste' fez o login com sucesso
        $session->set('login', 'admin.teste');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Limpar a sessão no final de cada teste para evitar poluir outros testes
        Services::session()->destroy();
    }

    public function testVerificaSeAuditoriaCriadoPorRegistaOUtilizadorDaSessao()
    {
        $model = new BolsistaModel();

        // Dados base para passar na validação do model
        $dados = [
            'nome'  => 'Ana Teste',
            'email' => 'ana.teste.' . uniqid() . '@ufc.br',
            'cpf'   => (string) rand(10000000000, 99999999999)
        ];

        // 3. Executar o Insert
        // É neste momento que o CodeIgniter vai disparar o callback $beforeInsert
        // O seu código do Model vai invocar session()->get('login') e encontrará 'admin.teste'
        $id = $model->insert($dados);

        // Verifica se a inserção ocorreu sem erros de base de dados
        $this->assertIsNumeric($id);

        // 4. A Prova Final: Verificar na base de dados
        // Confirmamos se o registo inserido tem exatamente o login na coluna _criado_por
        $this->seeInDatabase('bolsistas', [
            'id_bolsista' => $id,
            '_criado_por' => 'admin.teste' // A magia acontece aqui!
        ]);
    }
}