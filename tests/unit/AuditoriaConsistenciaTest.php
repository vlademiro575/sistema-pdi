<?php

namespace Tests\Unit;

use App\Libraries\AuditoriaConsistenciaService;
use App\Models\ProjetoModel;
use App\Models\RubricaModel;
use App\Models\DespesaModel;
use CodeIgniter\Test\CIUnitTestCase;

final class AuditoriaConsistenciaTest extends CIUnitTestCase
{
    protected AuditoriaConsistenciaService $service;
    protected ProjetoModel $projetoModel;
    protected RubricaModel $rubricaModel;
    protected DespesaModel $despesaModel;
    protected int $idProf;
    protected int $idFund;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service      = new AuditoriaConsistenciaService();
        $this->projetoModel = new ProjetoModel();
        $this->rubricaModel = new RubricaModel();
        $this->despesaModel = new DespesaModel();

        // Simula sessão autenticada
        session()->set([
            'login'  => 'admin_test',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);

        $db = \Config\Database::connect();
        $prof = $db->table('professores')->get()->getFirstRow('array');
        if (!$prof) {
            $db->table('professores')->insert([
                'nome'  => 'Prof Teste Auditoria',
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
                'nome'  => 'Fundacao Teste Auditoria',
                'tipo'  => 'FUNDACAO_APOIO'
            ]);
            $this->idFund = (int) $db->insertID();
        } else {
            $this->idFund = (int) $fund['id_fundacao'];
        }
    }

    public function testDetectaDiscrepanciaOrcamentoVersusRubricas(): void
    {
        // 1. Cria um projeto com orçamento de R$ 100.000,00
        $cod = 'TESTE-AUD-' . uniqid();
        $this->projetoModel->insert([
            'id_professor'            => $this->idProf,
            'id_fundacao'             => $this->idFund,
            'codigo_projeto_fundacao' => $cod,
            'titulo'                  => 'Projeto Teste Auditoria',
            'orcamento_total'         => 100000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();

        // 2. Insere rubrica com apenas R$ 40.000,00 (discrepância proposital de R$ 60.000,00)
        $this->rubricaModel->insert([
            'id_projeto'       => $idProjeto,
            'nome'             => 'Custeio Parcial',
            'tipo'             => 'CUSTEIO',
            'valor_alocado'    => 40000.00,
            'saldo_disponivel' => 40000.00
        ]);
        $idRubrica = $this->rubricaModel->getInsertID();

        // 3. Executa a auditoria
        $resultado = $this->service->executarAuditoria();

        // 4. Valida se a discrepância foi apontada
        $pendenciaEncontrada = false;
        foreach ($resultado['pendencias'] as $p) {
            if ($p['id_projeto'] == $idProjeto && $p['regra'] === 'Soma de Rubricas Divergente do Orçamento') {
                $pendenciaEncontrada = true;
                $this->assertEquals('ERRO', $p['tipo']);
                $this->assertStringContainsString('40.000,00', $p['mensagem']);
                $this->assertStringContainsString('100.000,00', $p['mensagem']);
                break;
            }
        }

        $this->assertTrue($pendenciaEncontrada, 'A divergência de orçamento entre projeto e rubricas deveria ter sido detectada.');

        // Limpeza
        $this->rubricaModel->delete($idRubrica);
        $this->projetoModel->delete($idProjeto);
    }

    public function testDetectaDespesaForaDaVigencia(): void
    {
        $cod = 'TESTE-VIG-' . uniqid();
        $this->projetoModel->insert([
            'id_professor'            => $this->idProf,
            'id_fundacao'             => $this->idFund,
            'codigo_projeto_fundacao' => $cod,
            'titulo'                  => 'Projeto Vigencia Teste',
            'orcamento_total'         => 50000.00,
            'data_inicio'             => '2026-06-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();

        $this->rubricaModel->insert([
            'id_projeto'       => $idProjeto,
            'nome'             => 'Material',
            'tipo'             => 'CAPITAL',
            'valor_alocado'    => 50000.00,
            'saldo_disponivel' => 50000.00
        ]);
        $idRubrica = $this->rubricaModel->getInsertID();

        // Insere despesa com data ANTERIOR ao início do projeto (2025-10-10 vs início 2026-06-01)
        $this->despesaModel->insert([
            'id_projeto'       => $idProjeto,
            'id_rubrica'       => $idRubrica,
            'data_emissao'     => '2025-10-10',
            'valor_total'      => 1500.00,
            'numero_nota'      => 'NF-TESTE-VIG',
            'nome_fornecedor'  => 'Fornecedor Fora Vigencia',
            'status_aprovacao' => 'EM_ANALISE'
        ]);
        $idDespesa = $this->despesaModel->getInsertID();

        $resultado = $this->service->executarAuditoria();

        $pendenciaEncontrada = false;
        foreach ($resultado['pendencias'] as $p) {
            if ($p['id_projeto'] == $idProjeto && $p['regra'] === 'Despesa Fora da Vigência do Projeto') {
                $pendenciaEncontrada = true;
                $this->assertEquals('ERRO', $p['tipo']);
                break;
            }
        }

        $this->assertTrue($pendenciaEncontrada, 'Despesa emitida fora da vigência deveria gerar erro na auditoria.');

        // Limpeza
        $db = \Config\Database::connect();
        $db->table('movimentacoes_rubricas')->where('id_rubrica', $idRubrica)->delete();
        $this->despesaModel->delete($idDespesa);
        $db->table('movimentacoes_rubricas')->where('id_rubrica', $idRubrica)->delete();
        $this->rubricaModel->delete($idRubrica);
        $this->projetoModel->delete($idProjeto);
    }

    public function testDetectaFundacaoInexistenteNoProjeto(): void
    {
        $db = \Config\Database::connect();

        // Desativa FK temporariamente para simular chave inconsistente
        $db->query('PRAGMA foreign_keys = OFF;');

        $cod = 'TESTE-NOFUND-' . uniqid();
        $this->projetoModel->insert([
            'id_professor'            => $this->idProf,
            'id_fundacao'             => 999999,
            'codigo_projeto_fundacao' => $cod,
            'titulo'                  => 'Projeto Sem Fundacao Valida',
            'orcamento_total'         => 20000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();
        $db->query('PRAGMA foreign_keys = ON;');

        $resultado = $this->service->executarAuditoria();

        $pendenciaEncontrada = false;
        foreach ($resultado['pendencias'] as $p) {
            if ($p['id_projeto'] == $idProjeto && $p['regra'] === 'Fundação Inexistente ou Inválida') {
                $pendenciaEncontrada = true;
                $this->assertEquals('ERRO', $p['tipo']);
                $this->assertStringContainsString('999999', $p['mensagem']);
                break;
            }
        }

        $this->assertTrue($pendenciaEncontrada, 'A auditoria deveria identificar que a fundação apontada pelo projeto não existe.');

        // Limpeza
        $this->projetoModel->delete($idProjeto);
    }

    public function testDetectaBolsistaCadastradoMaisDeUmaVezNoMesmoProjeto(): void
    {
        $bolsistaModel = new \App\Models\BolsistaModel();
        $pbModel       = new \App\Models\ProjetoBolsistaModel();

        // Cria bolsista
        $cpf = (string) rand(10000000000, 99999999999);
        $bolsistaModel->insert([
            'nome'  => 'Bolsista Duplicado Teste',
            'cpf'   => $cpf,
            'email' => 'duplicado.' . uniqid() . '@teste.com'
        ]);
        $idBolsista = $bolsistaModel->getInsertID();

        // Cria projeto
        $cod = 'TESTE-DUP-' . uniqid();
        $this->projetoModel->insert([
            'id_professor'            => $this->idProf,
            'id_fundacao'             => $this->idFund,
            'codigo_projeto_fundacao' => $cod,
            'titulo'                  => 'Projeto Teste Bolsista Duplicado',
            'orcamento_total'         => 30000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();

        // Vincula o mesmo bolsista DUAS VEZES ao mesmo projeto
        $pbModel->insert([
            'id_projeto'  => $idProjeto,
            'id_bolsista' => $idBolsista,
            'valor_bolsa' => 500.00,
            'data_inicio' => '2026-01-01',
            'data_fim'    => '2026-06-30',
            'status'      => 'ATIVO'
        ]);
        $idV1 = $pbModel->getInsertID();

        $pbModel->insert([
            'id_projeto'  => $idProjeto,
            'id_bolsista' => $idBolsista,
            'valor_bolsa' => 500.00,
            'data_inicio' => '2026-07-01',
            'data_fim'    => '2026-12-31',
            'status'      => 'ATIVO'
        ]);
        $idV2 = $pbModel->getInsertID();

        $resultado = $this->service->executarAuditoria();

        $pendenciaEncontrada = false;
        foreach ($resultado['pendencias'] as $p) {
            if ($p['id_projeto'] == $idProjeto && $p['regra'] === 'Bolsista com Vínculo Duplicado no Projeto') {
                $pendenciaEncontrada = true;
                $this->assertEquals('AVISO', $p['tipo']);
                $this->assertStringContainsString('Bolsista Duplicado Teste', $p['mensagem']);
                $this->assertStringContainsString('2', $p['mensagem']);
                break;
            }
        }

        $this->assertTrue($pendenciaEncontrada, 'A auditoria deveria identificar bolsista com mais de um vínculo no mesmo projeto.');

        // Limpeza
        $pbModel->delete($idV1);
        $pbModel->delete($idV2);
        $this->projetoModel->delete($idProjeto);
        $bolsistaModel->delete($idBolsista);
    }
}
