<?php

namespace Tests\Unit;

use App\Models\ProfessorModel;
use CodeIgniter\Test\CIUnitTestCase;

final class ProfessorHistoricoTest extends CIUnitTestCase
{
    protected ProfessorModel $professorModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->professorModel = new ProfessorModel();

        session()->set([
            'login'  => 'admin_prof_hist',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);
    }

    public function testHistoricoProfessoresRegistraAlteracoesCorretamente(): void
    {
        $db = \Config\Database::connect();

        // 1. Cria professor
        $cpf = (string) rand(10000000000, 99999999999);
        $emailOriginal = 'prof.hist.' . uniqid() . '@teste.com';
        $this->professorModel->insert([
            'nome'     => 'Professor Historico Inicial',
            'cpf'      => $cpf,
            'email'    => $emailOriginal,
            'telefone' => '85988880000',
            'siape'    => '1234567'
        ]);
        $idProfessor = $this->professorModel->getInsertID();
        $this->assertIsNumeric($idProfessor);

        // 2. Primeira alteração (simula outro usuário)
        session()->set('login', 'editor_prof');
        $this->professorModel->update($idProfessor, [
            'nome'     => 'Professor Historico Editado 1',
            'telefone' => '85988881111'
        ]);

        // 3. Consulta a tabela de histórico (professores_historico)
        $historico = $db->table('professores_historico')
            ->where('id_professor', $idProfessor)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $this->assertCount(1, $historico, 'Deveria haver 1 revisão histórica gravada pela trigger.');
        $this->assertEquals('Professor Historico Inicial', $historico[0]['nome']);
        $this->assertEquals('85988880000', $historico[0]['telefone']);

        // O estado atual no banco é 'Professor Historico Editado 1'
        $atual = $this->professorModel->find($idProfessor);
        $this->assertEquals('Professor Historico Editado 1', $atual['nome']);
        $this->assertEquals('85988881111', $atual['telefone']);
        $this->assertEquals('editor_prof', $atual['_atualizado_por']);

        // Limpeza
        $this->professorModel->delete($idProfessor);
        $db->table('professores_historico')->where('id_professor', $idProfessor)->delete();
    }
}

