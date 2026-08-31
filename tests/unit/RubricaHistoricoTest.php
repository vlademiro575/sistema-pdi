<?php

namespace Tests\Unit;

use App\Models\RubricaModel;
use App\Models\ProjetoModel;
use App\Models\ProfessorModel;
use App\Models\FundacaoModel;
use CodeIgniter\Test\CIUnitTestCase;

final class RubricaHistoricoTest extends CIUnitTestCase
{
    protected RubricaModel $rubricaModel;
    protected ProjetoModel $projetoModel;
    protected ProfessorModel $professorModel;
    protected FundacaoModel $fundacaoModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rubricaModel = new RubricaModel();
        $this->projetoModel = new ProjetoModel();
        $this->professorModel = new ProfessorModel();
        $this->fundacaoModel = new FundacaoModel();

        session()->set([
            'login'  => 'admin_rubr_hist',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);
    }

    public function testHistoricoRubricasRegistraAlteracoesCorretamente(): void
    {
        $db = \Config\Database::connect();

        // 1. Cria professor, fundação e projeto para vínculo
        $cpfProf = (string) rand(10000000000, 99999999999);
        $this->professorModel->insert([
            'nome'  => 'Prof Teste Rubr Hist',
            'cpf'   => $cpfProf,
            'email' => 'prof.rubr.' . uniqid() . '@teste.com'
        ]);
        $idProf = $this->professorModel->getInsertID();

        $cnpjFund = (string) rand(10000000000000, 99999999999999);
        $siglaFund = 'F' . rand(100, 999);
        $this->fundacaoModel->insert([
            'nome'  => 'Fundacao Teste Rubr Hist',
            'cnpj'  => $cnpjFund,
            'sigla' => $siglaFund,
            'tipo'  => 'FUNDACAO_APOIO'
        ]);
        $idFund = $this->fundacaoModel->getInsertID();

        $this->projetoModel->insert([
            'id_professor'            => $idProf,
            'id_fundacao'             => $idFund,
            'codigo_projeto_fundacao' => 'PJ-RUBR-' . rand(1000, 9999),
            'titulo'                  => 'Projeto Para Teste Rubrica Hist',
            'orcamento_total'         => 80000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();

        // 2. Cria rubrica
        $this->rubricaModel->insert([
            'id_projeto'       => $idProjeto,
            'nome'             => 'Rubrica Historico Inicial',
            'tipo'             => 'CUSTEIO',
            'valor_alocado'    => 30000.00,
            'saldo_disponivel' => 30000.00
        ]);
        $idRubrica = $this->rubricaModel->getInsertID();
        $this->assertIsNumeric($idRubrica);

        // 3. Primeira alteração (simula outro usuário)
        session()->set('login', 'editor_rubrica');
        $this->rubricaModel->update($idRubrica, [
            'nome' => 'Rubrica Historico Editada 1',
            'tipo' => 'CAPITAL'
        ]);

        // 4. Consulta a tabela de histórico (rubricas_historico)
        $historico = $db->table('rubricas_historico')
            ->where('id_rubrica', $idRubrica)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $this->assertCount(1, $historico, 'Deveria haver 1 revisão histórica gravada pela trigger.');
        $this->assertEquals('Rubrica Historico Inicial', $historico[0]['nome']);
        $this->assertEquals('CUSTEIO', $historico[0]['tipo']);
        $this->assertEquals(30000.00, (float) $historico[0]['valor_alocado']);

        // O estado atual no banco é 'Rubrica Historico Editada 1'
        $atual = $this->rubricaModel->find($idRubrica);
        $this->assertEquals('Rubrica Historico Editada 1', $atual['nome']);
        $this->assertEquals('CAPITAL', $atual['tipo']);
        $this->assertEquals('editor_rubrica', $atual['_atualizado_por']);

        // Limpeza
        $this->rubricaModel->delete($idRubrica);
        $this->projetoModel->delete($idProjeto);
        $this->professorModel->delete($idProf);
        $this->fundacaoModel->delete($idFund);
        $db->table('rubricas_historico')->where('id_rubrica', $idRubrica)->delete();
        $db->table('projetos_historico')->where('id_projeto', $idProjeto)->delete();
    }
}

