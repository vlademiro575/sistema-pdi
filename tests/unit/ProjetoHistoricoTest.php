<?php

namespace Tests\Unit;

use App\Models\ProjetoModel;
use App\Models\ProfessorModel;
use App\Models\FundacaoModel;
use CodeIgniter\Test\CIUnitTestCase;

final class ProjetoHistoricoTest extends CIUnitTestCase
{
    protected ProjetoModel $projetoModel;
    protected ProfessorModel $professorModel;
    protected FundacaoModel $fundacaoModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projetoModel = new ProjetoModel();
        $this->professorModel = new ProfessorModel();
        $this->fundacaoModel = new FundacaoModel();

        session()->set([
            'login'  => 'admin_proj_hist',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);
    }

    public function testHistoricoProjetosRegistraAlteracoesCorretamente(): void
    {
        $db = \Config\Database::connect();

        // 1. Cria professor e fundação para vínculo
        $cpfProf = (string) rand(10000000000, 99999999999);
        $this->professorModel->insert([
            'nome'  => 'Prof Teste Proj Hist',
            'cpf'   => $cpfProf,
            'email' => 'prof.proj.' . uniqid() . '@teste.com'
        ]);
        $idProf = $this->professorModel->getInsertID();

        $cnpjFund = (string) rand(10000000000000, 99999999999999);
        $siglaFund = 'F' . rand(100, 999);
        $this->fundacaoModel->insert([
            'nome'  => 'Fundacao Teste Proj Hist',
            'cnpj'  => $cnpjFund,
            'sigla' => $siglaFund,
            'tipo'  => 'FUNDACAO_APOIO'
        ]);
        $idFund = $this->fundacaoModel->getInsertID();

        // 2. Cria projeto
        $this->projetoModel->insert([
            'id_professor'            => $idProf,
            'id_fundacao'             => $idFund,
            'codigo_projeto_fundacao' => 'PJ-' . rand(1000, 9999),
            'titulo'                  => 'Projeto Historico Inicial',
            'orcamento_total'         => 100000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();
        $this->assertIsNumeric($idProjeto);

        // 3. Primeira alteração (simula outro usuário)
        session()->set('login', 'editor_proj');
        $this->projetoModel->update($idProjeto, [
            'titulo'          => 'Projeto Historico Editado 1',
            'orcamento_total' => 150000.00
        ]);

        // 4. Consulta a tabela de histórico (projetos_historico)
        $historico = $db->table('projetos_historico')
            ->where('id_projeto', $idProjeto)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $this->assertCount(1, $historico, 'Deveria haver 1 revisão histórica gravada pela trigger.');
        $this->assertEquals('Projeto Historico Inicial', $historico[0]['titulo']);
        $this->assertEquals(100000.00, (float) $historico[0]['orcamento_total']);

        // O estado atual no banco é 'Projeto Historico Editado 1'
        $atual = $this->projetoModel->find($idProjeto);
        $this->assertEquals('Projeto Historico Editado 1', $atual['titulo']);
        $this->assertEquals(150000.00, (float) $atual['orcamento_total']);
        $this->assertEquals('editor_proj', $atual['_atualizado_por']);

        // Limpeza
        $this->projetoModel->delete($idProjeto);
        $this->professorModel->delete($idProf);
        $this->fundacaoModel->delete($idFund);
        $db->table('projetos_historico')->where('id_projeto', $idProjeto)->delete();
    }
}

