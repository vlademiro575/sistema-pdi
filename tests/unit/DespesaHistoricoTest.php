<?php

namespace Tests\Unit;

use App\Models\DespesaModel;
use App\Models\ProjetoModel;
use App\Models\ProfessorModel;
use App\Models\FundacaoModel;
use App\Models\RubricaModel;
use CodeIgniter\Test\CIUnitTestCase;

final class DespesaHistoricoTest extends CIUnitTestCase
{
    protected DespesaModel $despesaModel;
    protected ProjetoModel $projetoModel;
    protected ProfessorModel $professorModel;
    protected FundacaoModel $fundacaoModel;
    protected RubricaModel $rubricaModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->despesaModel = new DespesaModel();
        $this->projetoModel = new ProjetoModel();
        $this->professorModel = new ProfessorModel();
        $this->fundacaoModel = new FundacaoModel();
        $this->rubricaModel = new RubricaModel();

        session()->set([
            'login'  => 'admin_desp_hist',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);
    }

    public function testHistoricoDespesasRegistraAlteracoesCorretamente(): void
    {
        $db = \Config\Database::connect();

        // 1. Cria professor, fundação, projeto e rubrica para vínculo
        $cpfProf = (string) rand(10000000000, 99999999999);
        $this->professorModel->insert([
            'nome'  => 'Prof Teste Desp Hist',
            'cpf'   => $cpfProf,
            'email' => 'prof.desp.' . uniqid() . '@teste.com'
        ]);
        $idProf = $this->professorModel->getInsertID();

        $cnpjFund = (string) rand(10000000000000, 99999999999999);
        $siglaFund = 'F' . rand(100, 999);
        $this->fundacaoModel->insert([
            'nome'  => 'Fundacao Teste Desp Hist',
            'cnpj'  => $cnpjFund,
            'sigla' => $siglaFund,
            'tipo'  => 'FUNDACAO_APOIO'
        ]);
        $idFund = $this->fundacaoModel->getInsertID();

        $this->projetoModel->insert([
            'id_professor'            => $idProf,
            'id_fundacao'             => $idFund,
            'codigo_projeto_fundacao' => 'PJ-DESP-' . rand(1000, 9999),
            'titulo'                  => 'Projeto Para Teste Despesa Hist',
            'orcamento_total'         => 50000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();

        $this->rubricaModel->insert([
            'id_projeto'       => $idProjeto,
            'nome'             => 'Rubrica Teste Desp Hist',
            'tipo'             => 'CUSTEIO',
            'valor_alocado'    => 20000.00,
            'saldo_disponivel' => 20000.00
        ]);
        $idRubrica = $this->rubricaModel->getInsertID();

        // 2. Cria despesa
        $this->despesaModel->insert([
            'id_projeto'       => $idProjeto,
            'id_rubrica'       => $idRubrica,
            'nome_fornecedor'  => 'Fornecedor Desp Hist Inicial',
            'cnpj_fornecedor'  => '12.345.678/0001-90',
            'numero_nota'      => 'NF-100',
            'data_emissao'     => '2026-05-10',
            'valor_total'      => 1500.00,
            'descricao_itens'  => 'Item Inicial A',
            'status_aprovacao' => 'EM_ANALISE'
        ]);
        $idDespesa = $this->despesaModel->getInsertID();
        $this->assertIsNumeric($idDespesa);

        // 3. Primeira alteração (simula outro usuário)
        session()->set('login', 'editor_despesa');
        $this->despesaModel->update($idDespesa, [
            'nome_fornecedor'  => 'Fornecedor Desp Hist Editado 1',
            'status_aprovacao' => 'APROVADO'
        ]);

        // 4. Consulta a tabela de histórico (despesas_historico)
        $historico = $db->table('despesas_historico')
            ->where('id_despesa', $idDespesa)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $this->assertCount(1, $historico, 'Deveria haver 1 revisão histórica gravada pela trigger.');
        $this->assertEquals('Fornecedor Desp Hist Inicial', $historico[0]['nome_fornecedor']);
        $this->assertEquals('EM_ANALISE', $historico[0]['status_aprovacao']);

        // O estado atual no banco é 'Fornecedor Desp Hist Editado 1'
        $atual = $this->despesaModel->find($idDespesa);
        $this->assertEquals('Fornecedor Desp Hist Editado 1', $atual['nome_fornecedor']);
        $this->assertEquals('APROVADO', $atual['status_aprovacao']);
        $this->assertEquals('editor_despesa', $atual['_atualizado_por']);

        // Limpeza
        $this->despesaModel->delete($idDespesa);
        $db->table('movimentacoes_rubricas')->where('id_despesa', $idDespesa)->orWhere('id_rubrica', $idRubrica)->delete();
        $db->table('movimentacoes_rubricas_historico')->where('id_despesa', $idDespesa)->orWhere('id_rubrica', $idRubrica)->delete();
        $this->rubricaModel->delete($idRubrica);
        $this->projetoModel->delete($idProjeto);
        $this->professorModel->delete($idProf);
        $this->fundacaoModel->delete($idFund);
        $db->table('despesas_historico')->where('id_despesa', $idDespesa)->delete();
        $db->table('rubricas_historico')->where('id_rubrica', $idRubrica)->delete();
        $db->table('projetos_historico')->where('id_projeto', $idProjeto)->delete();
    }
}
