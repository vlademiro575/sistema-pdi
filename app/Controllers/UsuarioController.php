<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Exibe a listagem de usuários do sistema
     */
    public function index()
    {
        $data = [
            'titulo'   => 'Gerenciamento de Usuários',
            'usuarios' => $this->usuarioModel->orderBy('id_usuario', 'ASC')->findAll()
        ];

        return view('usuarios/index', $data);
    }

    /**
     * Formulário de cadastro de novo usuário
     */
    public function new()
    {
        $data = [
            'titulo' => 'Novo Usuário',
            'perfis' => ['ADMIN', 'PROFESSOR', 'BOLSISTA', 'SECRETARIO']
        ];

        return view('usuarios/form', $data);
    }

    /**
     * Insere um novo usuário no banco de dados
     */
    public function create()
    {
        $rules = [
            'nome'   => 'required|min_length[3]|max_length[255]',
            'email'  => 'required|valid_email|is_unique[usuarios.email]',
            'login'  => 'required|min_length[3]|max_length[50]|is_unique[usuarios.login]',
            'senha'  => 'required|min_length[6]',
            'perfil' => 'required|in_list[ADMIN,PROFESSOR,BOLSISTA,SECRETARIO]',
            'ativo'  => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'   => trim($this->request->getPost('nome')),
            'email'  => trim($this->request->getPost('email')),
            'login'  => trim($this->request->getPost('login')),
            'senha'  => password_hash($this->request->getPost('senha'), PASSWORD_DEFAULT),
            'perfil' => $this->request->getPost('perfil'),
            'ativo'  => $this->request->getPost('ativo') !== null ? (int) $this->request->getPost('ativo') : 1
        ];

        if ($this->usuarioModel->insert($dados)) {
            return redirect()->to('/usuarios')->with('sucesso', 'Usuário cadastrado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao cadastrar usuário.');
    }

    /**
     * Formulário de edição de usuário
     */
    public function edit($id)
    {
        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to('/usuarios')->with('erro', 'Usuário não encontrado.');
        }

        $data = [
            'titulo'  => 'Editar Usuário #' . $usuario['id_usuario'],
            'usuario' => $usuario,
            'perfis'  => ['ADMIN', 'PROFESSOR', 'BOLSISTA', 'SECRETARIO']
        ];

        return view('usuarios/form', $data);
    }

    /**
     * Atualiza os dados do usuário
     */
    public function update($id)
    {
        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to('/usuarios')->with('erro', 'Usuário não encontrado.');
        }

        $rules = [
            'nome'   => 'required|min_length[3]|max_length[255]',
            'email'  => "required|valid_email|is_unique[usuarios.email,id_usuario,{$id}]",
            'login'  => "required|min_length[3]|max_length[50]|is_unique[usuarios.login,id_usuario,{$id}]",
            'senha'  => 'permit_empty|min_length[6]',
            'perfil' => 'required|in_list[ADMIN,PROFESSOR,BOLSISTA,SECRETARIO]',
            'ativo'  => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ativo = $this->request->getPost('ativo') !== null ? (int) $this->request->getPost('ativo') : 1;

        // Impede que o usuário logado desative sua própria conta
        if ((int)$id === (int)session()->get('id_usuario') && $ativo === 0) {
            return redirect()->back()->withInput()->with('erro', 'Você não pode desativar o seu próprio usuário logado.');
        }

        $dados = [
            'nome'   => trim($this->request->getPost('nome')),
            'email'  => trim($this->request->getPost('email')),
            'login'  => trim($this->request->getPost('login')),
            'perfil' => $this->request->getPost('perfil'),
            'ativo'  => $ativo
        ];

        // Atualiza a senha somente se foi informada uma nova senha
        $novaSenha = $this->request->getPost('senha');
        if (!empty($novaSenha)) {
            $dados['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
        }

        if ($this->usuarioModel->update($id, $dados)) {
            // Se o usuário logado alterou seu próprio nome/login, atualiza a sessão
            if ((int)$id === (int)session()->get('id_usuario')) {
                session()->set([
                    'nome'   => $dados['nome'],
                    'login'  => $dados['login'],
                    'perfil' => $dados['perfil']
                ]);
            }

            return redirect()->to('/usuarios')->with('sucesso', 'Usuário atualizado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar usuário.');
    }

    /**
     * Remove o usuário (dispara o trigger SQLite de auditoria)
     */
    public function delete($id)
    {
        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to('/usuarios')->with('erro', 'Usuário não encontrado.');
        }

        // Impede que o usuário logado exclua a si mesmo
        if ((int)$id === (int)session()->get('id_usuario')) {
            return redirect()->to('/usuarios')->with('erro', 'Você não pode excluir o seu próprio usuário logado.');
        }

        if ($this->usuarioModel->delete($id)) {
            return redirect()->to('/usuarios')->with('sucesso', 'Usuário removido com sucesso!');
        }

        return redirect()->to('/usuarios')->with('erro', 'Erro ao remover usuário.');
    }
}

