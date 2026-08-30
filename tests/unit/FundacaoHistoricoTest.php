<?php

namespace Tests\Unit;

use App\Models\FundacaoModel;
use CodeIgniter\Test\CIUnitTestCase;

final class FundacaoHistoricoTest extends CIUnitTestCase
{
    protected FundacaoModel $fundacaoModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fundacaoModel = new FundacaoModel();

        session()->set([
            'login'  => 'admin_fund_hist',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);
    }

    public function testHistoricoFundacoesRegistraAlteracoesCorretamente(): void
    {
        $db = \Config\Database::connect();

        // 1. Cria fundação
        $cnpj = (string) rand(10000000000000, 99999999999999);
        $sigla = 'F' . rand(100, 999);
        $this->fundacaoModel->insert([
            'nome'  => 'Fundacao Historico Inicial',
            'cnpj'  => $cnpj,
            'sigla' => $sigla,
            'tipo'  => 'FUNDACAO_APOIO'
        ]);
        $idFundacao = $this->fundacaoModel->getInsertID();
        $this->assertIsNumeric($idFundacao);

        // 2. Primeira alteração (simula outro usuário)
        session()->set('login', 'editor_fund');
        $this->fundacaoModel->update($idFundacao, [
            'nome' => 'Fundacao Historico Editada 1',
            'tipo' => 'FAP_ESTADUAL'
        ]);

        // 3. Consulta a tabela de histórico (fundacoes_historico)
        $historico = $db->table('fundacoes_historico')
            ->where('id_fundacao', $idFundacao)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $this->assertCount(1, $historico, 'Deveria haver 1 revisão histórica gravada pela trigger.');
        $this->assertEquals('Fundacao Historico Inicial', $historico[0]['nome']);
        $this->assertEquals('FUNDACAO_APOIO', $historico[0]['tipo']);

        // O estado atual no banco é 'Fundacao Historico Editada 1'
        $atual = $this->fundacaoModel->find($idFundacao);
        $this->assertEquals('Fundacao Historico Editada 1', $atual['nome']);
        $this->assertEquals('FAP_ESTADUAL', $atual['tipo']);
        $this->assertEquals('editor_fund', $atual['_atualizado_por']);

        // Limpeza
        $this->fundacaoModel->delete($idFundacao);
        $db->table('fundacoes_historico')->where('id_fundacao', $idFundacao)->delete();
    }
}

