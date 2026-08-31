<?php

namespace Tests\Unit;

use App\Models\ProjetoBolsistaModel;
use App\Models\ProjetoModel;
use App\Models\BolsistaModel;
use App\Models\ProfessorModel;
use App\Models\FundacaoModel;
use CodeIgniter\Test\CIUnitTestCase;

final class ProjetoBolsistaHistoricoTest extends CIUnitTestCase
{
    protected ProjetoBolsistaModel $projetoBolsistaModel;
    protected ProjetoModel $projetoModel;
    protected BolsistaModel $bolsistaModel;
    protected ProfessorModel $professorModel;
    protected FundacaoModel $fundacaoModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projetoBolsistaModel = new ProjetoBolsistaModel();
        $this->projetoModel         = new ProjetoModel();
        $this->bolsistaModel        = new BolsistaModel();
        $this->professorModel       = new ProfessorModel();
        $this->fundacaoModel        = new FundacaoModel();

        session()->set([
            'login'  => 'admin_vinc_hist',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);
    }

    public function testHistoricoProjetosBolsistasRegistraAlteracoesCorretamente(): void
    {
        $db = \Config\Database::connect();

        // 1. Cria professor, fundação, projeto e bolsista para vínculo
        $cpfProf = (string) rand(10000000000, 99999999999);
        $this->professorModel->insert([
            'nome'  => 'Prof Teste Vinc Hist',
            'cpf'   => $cpfProf,
            'email' => 'prof.vinc.' . uniqid() . '@teste.com'
        ]);
        $idProf = $this->professorModel->getInsertID();

        $cnpjFund = (string) rand(10000000000000, 99999999999999);
        $siglaFund = 'F' . rand(100, 999);
        $this->fundacaoModel->insert([
            'nome'  => 'Fundacao Teste Vinc Hist',
            'cnpj'  => $cnpjFund,
            'sigla' => $siglaFund,
            'tipo'  => 'FUNDACAO_APOIO'
        ]);
        $idFund = $this->fundacaoModel->getInsertID();

        $this->projetoModel->insert([
            'id_professor'            => $idProf,
            'id_fundacao'             => $idFund,
            'codigo_projeto_fundacao' => 'PJ-VINC-' . rand(1000, 9999),
            'titulo'                  => 'Projeto Para Teste Vinculo Hist',
            'orcamento_total'         => 120000.00,
            'data_inicio'             => '2026-01-01',
            'data_fim'                => '2026-12-31'
        ]);
        $idProjeto = $this->projetoModel->getInsertID();

        $cpfBolsista = (string) rand(10000000000, 99999999999);
        $this->bolsistaModel->insert([
            'nome'           => 'Bolsista Teste Vinc Hist',
            'cpf'            => $cpfBolsista,
            'email'          => 'bolsista.vinc.' . uniqid() . '@teste.com',
            'telefone'       => '85977770000',
            'banco'          => 'Banco do Brasil',
            'agencia'        => '1234',
            'conta_corrente' => '56789-0'
        ]);
        $idBolsista = $this->bolsistaModel->getInsertID();

        // 2. Cria vínculo do bolsista
        $this->projetoBolsistaModel->insert([
            'id_projeto'  => $idProjeto,
            'id_bolsista' => $idBolsista,
            'valor_bolsa' => 1200.00,
            'data_inicio' => '2026-02-01',
            'data_fim'    => '2026-12-31',
            'status'      => 'ATIVO'
        ]);
        $idVinculo = $this->projetoBolsistaModel->getInsertID();
        $this->assertIsNumeric($idVinculo);

        // 3. Primeira alteração (simula outro usuário)
        session()->set('login', 'editor_vinculo');
        $this->projetoBolsistaModel->update($idVinculo, [
            'valor_bolsa' => 1500.00,
            'status'      => 'INATIVO'
        ]);

        // 4. Consulta a tabela de histórico (projetos_bolsistas_historico)
        $historico = $db->table('projetos_bolsistas_historico')
            ->where('id_projeto_bolsista', $idVinculo)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $this->assertCount(1, $historico, 'Deveria haver 1 revisão histórica gravada pela trigger.');
        $this->assertEquals(1200.00, (float) $historico[0]['valor_bolsa']);
        $this->assertEquals('ATIVO', $historico[0]['status']);

        // O estado atual no banco é '1500.00' e 'INATIVO'
        $atual = $this->projetoBolsistaModel->find($idVinculo);
        $this->assertEquals(1500.00, (float) $atual['valor_bolsa']);
        $this->assertEquals('INATIVO', $atual['status']);
        $this->assertEquals('editor_vinculo', $atual['_atualizado_por']);

        // Limpeza
        $this->projetoBolsistaModel->delete($idVinculo);
        $this->bolsistaModel->delete($idBolsista);
        $this->projetoModel->delete($idProjeto);
        $this->professorModel->delete($idProf);
        $this->fundacaoModel->delete($idFund);
        $db->table('projetos_bolsistas_historico')->where('id_projeto_bolsista', $idVinculo)->delete();
        $db->table('bolsistas_historico')->where('id_bolsista', $idBolsista)->delete();
        $db->table('projetos_historico')->where('id_projeto', $idProjeto)->delete();
    }
}

