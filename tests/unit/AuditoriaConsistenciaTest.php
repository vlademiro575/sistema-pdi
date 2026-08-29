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
    }

    public function testDetectaDiscrepanciaOrcamentoVersusRubricas(): void
    {
        // 1. Cria um projeto com orçamento de R$ 100.000,00
        $db = \Config\Database::connect();
        $idProf = $db->table('professores')->select('id_professor')->get()->getRowArray()['id_professor'] ?? 1;
        $idFund = $db->table('fundacoes')->select('id_fundacao')->get()->getRowArray()['id_fundacao'] ?? 1;

        $cod = 'TESTE-AUD-' . uniqid();
        $this->projetoModel->insert([
            'id_professor'            => $idProf,
            'id_fundacao'             => $idFund,
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
        $db = \Config\Database::connect();
        $idProf = $db->table('professores')->select('id_professor')->get()->getRowArray()['id_professor'] ?? 1;
        $idFund = $db->table('fundacoes')->select('id_fundacao')->get()->getRowArray()['id_fundacao'] ?? 1;

        $cod = 'TESTE-VIG-' . uniqid();
        $this->projetoModel->insert([
            'id_professor'            => $idProf,
            'id_fundacao'             => $idFund,
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
}
