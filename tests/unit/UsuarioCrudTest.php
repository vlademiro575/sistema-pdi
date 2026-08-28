<?php

namespace Tests\Unit;

use App\Filters\AdminFilter;
use App\Models\UsuarioModel;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;
use Config\App;

final class UsuarioCrudTest extends CIUnitTestCase
{
    protected UsuarioModel $usuarioModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuarioModel = new UsuarioModel();
    }

    public function testInserirUsuarioAuditoria(): void
    {
        // Simula sessão de admin
        session()->set([
            'login'  => 'admin_test',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);

        $loginUnico = 'test_user_' . time() . '_' . rand(100, 999);
        $emailUnico = $loginUnico . '@teste.com';

        $dados = [
            'nome'   => 'Usuário de Teste',
            'email'  => $emailUnico,
            'login'  => $loginUnico,
            'senha'  => password_hash('123456', PASSWORD_DEFAULT),
            'perfil' => 'PROFESSOR',
            'ativo'  => 1
        ];

        $this->usuarioModel->insert($dados);
        $id = $this->usuarioModel->getInsertID();

        $this->assertNotEmpty($id);

        $usuario = $this->usuarioModel->find($id);
        $this->assertEquals('admin_test', $usuario['_criado_por']);
        $this->assertEquals('INSERT', $usuario['_operacao']);
        $this->assertTrue(password_verify('123456', $usuario['senha']));

        // Limpeza
        $this->usuarioModel->delete($id);
    }

    public function testAtualizarUsuarioAuditoriaHistorico(): void
    {
        session()->set([
            'login'  => 'admin_test',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);

        $loginUnico = 'test_upd_' . time() . '_' . rand(100, 999);
        $emailUnico = $loginUnico . '@teste.com';

        $this->usuarioModel->insert([
            'nome'   => 'Nome Original',
            'email'  => $emailUnico,
            'login'  => $loginUnico,
            'senha'  => password_hash('123456', PASSWORD_DEFAULT),
            'perfil' => 'BOLSISTA',
            'ativo'  => 1
        ]);
        $id = $this->usuarioModel->getInsertID();

        // Atualização
        $this->usuarioModel->update($id, [
            'nome'   => 'Nome Modificado',
            'perfil' => 'SECRETARIO'
        ]);

        $usuarioAtualizado = $this->usuarioModel->find($id);
        $this->assertEquals('Nome Modificado', $usuarioAtualizado['nome']);
        $this->assertEquals('SECRETARIO', $usuarioAtualizado['perfil']);
        $this->assertEquals('UPDATE', $usuarioAtualizado['_operacao']);
        $this->assertEquals('admin_test', $usuarioAtualizado['_atualizado_por']);

        // Verifica se a trigger SQLite populou a tabela usuarios_historico
        $db = \Config\Database::connect();
        $historico = $db->table('usuarios_historico')
            ->where('id_usuario', $id)
            ->get()
            ->getResultArray();

        $this->assertNotEmpty($historico);
        $this->assertEquals('Nome Original', $historico[0]['nome']);

        // Limpeza
        $this->usuarioModel->delete($id);
    }

    public function testExcluirUsuarioAlimentaHistorico(): void
    {
        session()->set([
            'login'  => 'admin_test',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);

        $loginUnico = 'test_del_' . time() . '_' . rand(100, 999);
        $emailUnico = $loginUnico . '@teste.com';

        $this->usuarioModel->insert([
            'nome'   => 'Usuario Para Deletar',
            'email'  => $emailUnico,
            'login'  => $loginUnico,
            'senha'  => password_hash('123456', PASSWORD_DEFAULT),
            'perfil' => 'SECRETARIO',
            'ativo'  => 1
        ]);
        $id = $this->usuarioModel->getInsertID();

        // Exclui
        $this->usuarioModel->delete($id);

        $usuario = $this->usuarioModel->find($id);
        $this->assertNull($usuario);

        // Verifica histórico
        $db = \Config\Database::connect();
        $historico = $db->table('usuarios_historico')
            ->where('id_usuario', $id)
            ->where('_operacao', 'DELETE')
            ->get()
            ->getResultArray();

        $this->assertNotEmpty($historico);
        $this->assertEquals('admin_test', $historico[0]['_deletado_por']);
    }

    public function testAdminFilterBloqueiaNaoLogado(): void
    {
        session()->destroy();
        $filter = new AdminFilter();
        $request = new IncomingRequest(new App(), new URI('http://localhost/usuarios'), null, new UserAgent());

        $resultado = $filter->before($request);
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $resultado);
    }

    public function testAdminFilterBloqueiaNaoAdmin(): void
    {
        session()->set([
            'login'  => 'professor_user',
            'perfil' => 'PROFESSOR',
            'logado' => true
        ]);

        $filter = new AdminFilter();
        $request = new IncomingRequest(new App(), new URI('http://localhost/usuarios'), null, new UserAgent());

        $resultado = $filter->before($request);
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $resultado);
    }

    public function testAdminFilterPermiteAdmin(): void
    {
        session()->set([
            'login'  => 'admin',
            'perfil' => 'ADMIN',
            'logado' => true
        ]);

        $filter = new AdminFilter();
        $request = new IncomingRequest(new App(), new URI('http://localhost/usuarios'), null, new UserAgent());

        $resultado = $filter->before($request);
        $this->assertNull($resultado);
    }
}

