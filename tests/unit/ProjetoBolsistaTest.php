<?php

namespace Tests\Unit;

use App\Models\BolsistaModel;
use App\Models\ProjetoBolsistaModel;
use App\Models\ProjetoModel;
use CodeIgniter\Test\CIUnitTestCase;

final class ProjetoBolsistaTest extends CIUnitTestCase
{
    protected ProjetoBolsistaModel $pbModel;
    protected ProjetoModel $projetoModel;
    protected BolsistaModel $bolsistaModel;
    protected int $idProf;
    protected int $idFund;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pbModel       = new ProjetoBolsistaModel();
        $this->projetoModel   = new ProjetoModel();
        $this->bolsistaModel  = new BolsistaModel();

        session()->set([
            'login'  => 'admin_test',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);

        $db = \Config\Database::connect();
        $prof = $db->table('professores')->get()->getFirstRow('array');
        if (!$prof) {
            $db->table('professores')->insert([
                'nome'  => 'Prof Teste Bolsista',
                'cpf'   => (string) rand(10000000000, 99999999999),
                'email' => 'prof.' . uniqid() . '@ufc.br'
            ]);
            $this->idProf = (int) $db->insertID();
        } else {
            $this->idProf = (int) $prof['id_professor'];
        }

        $fund = $db->table('fundacoes')->get()->getFirstRow('array');
        if (!$fund) {
            $db->table('fundacoes')->insert([
                'sigla' => 'F' . rand(100, 999),
                'nome'  => 'Fundacao Teste Bolsista',
                'tipo'  => 'FUNDACAO_APOIO'
            ]);
            $this->idFund = (int) $db->insertID();
        } else {
            $this->idFund = (int) $fund['id_fundacao'];
        }
    }

    public function testVinculoEAlteracaoDeBolsistaNoProjeto(): void
    {
        // 1. Cria bolsista
        $cpf = (string) rand(10000000000, 99999999999);
        $this->bolsistaModel->insert([
            'nome'  => 'Bolsista Teste Alteracao',
            'cpf'   => $cpf,
            'email' => 'bolsista.' . uniqid() . '@teste.com'
        ]);
        $idBolsista = $this->bolsistaModel->getInsertID();

        // 2. Cria projeto
        $cod = 'TESTE-PB-' . uniqid();
        $this->projetoModel->insert([
            'id_professor'            => $this->idProf,
            'id_fundacao'             => $this->idFund,
            'codigo_projeto_fundacao' => $cod,
            'titulo'                  => 'Projeto Teste Bolsistas',
            'orcamento_total'         => 80000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();

        // 3. Cria o vínculo inicial
        $this->pbModel->insert([
            'id_projeto'  => $idProjeto,
            'id_bolsista' => $idBolsista,
            'valor_bolsa' => 700.00,
            'data_inicio' => '2026-02-01',
            'data_fim'    => '2026-06-30',
            'status'      => 'ATIVO'
        ]);
        $idVinculo = $this->pbModel->getInsertID();
        $this->assertIsInt($idVinculo);

        // 4. Altera o vínculo (datas, valor, status)
        $dadosAlteracao = [
            'valor_bolsa' => 1200.00,
            'data_inicio' => '2026-03-01',
            'data_fim'    => '2026-11-30',
            'status'      => 'DESLIGADO'
        ];
        $atualizado = $this->pbModel->update($idVinculo, $dadosAlteracao);
        $this->assertTrue($atualizado);

        // 5. Verifica se as alterações foram persistidas
        $vinculoSalvo = $this->pbModel->find($idVinculo);
        $this->assertEquals(1200.00, (float) $vinculoSalvo['valor_bolsa']);
        $this->assertEquals('2026-03-01', $vinculoSalvo['data_inicio']);
        $this->assertEquals('2026-11-30', $vinculoSalvo['data_fim']);
        $this->assertEquals('DESLIGADO', $vinculoSalvo['status']);

        // Limpeza
        $this->pbModel->delete($idVinculo);
        $this->projetoModel->delete($idProjeto);
        $this->bolsistaModel->delete($idBolsista);
    }
}

