<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BolsistaModel;

class BolsistaController extends BaseController
{
    protected BolsistaModel $bolsistaModel;

    public function __construct()
    {
        $this->bolsistaModel = new BolsistaModel();
    }

    /**
     * Exibe a listagem de bolsistas
     */
    public function index()
    {
        $data = [
            'titulo'    => 'Gerenciamento de Bolsistas',
            'bolsistas' => $this->bolsistaModel->findAll()
        ];

        return view('bolsistas/index', $data);
    }

    /**
     * Formulário de criação
     */
    public function new()
    {
        $data = [
            'titulo' => 'Novo Bolsista'
        ];

        return view('bolsistas/form', $data);
    }

    /**
     * Insere um novo bolsista no banco de dados
     */
    public function create()
    {
        $rules = [
            'nome'           => 'required|min_length[3]|max_length[255]',
            'cpf'            => 'required|is_unique[bolsistas.cpf]',
            'email'          => 'required|valid_email|is_unique[bolsistas.email]',
            'telefone'       => 'permit_empty|max_length[20]',
            'banco'          => 'permit_empty|max_length[50]',
            'agencia'        => 'permit_empty|max_length[20]',
            'conta_corrente' => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'           => $this->request->getPost('nome'),
            'cpf'            => $this->request->getPost('cpf'),
            'email'          => $this->request->getPost('email'),
            'telefone'       => $this->request->getPost('telefone'),
            'banco'          => $this->request->getPost('banco'),
            'agencia'        => $this->request->getPost('agencia'),
            'conta_corrente' => $this->request->getPost('conta_corrente'),
        ];

        if ($this->bolsistaModel->insert($dados)) {
            return redirect()->to('/bolsistas')->with('sucesso', 'Bolsista cadastrado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao cadastrar bolsista.');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $bolsista = $this->bolsistaModel->find($id);

        if (!$bolsista) {
            return redirect()->to('/bolsistas')->with('erro', 'Bolsista não encontrado.');
        }

        $data = [
            'titulo'   => 'Editar Bolsista',
            'bolsista' => $bolsista
        ];

        return view('bolsistas/form', $data);
    }

    /**
     * Atualiza o registro do bolsista
     */
    public function update($id)
    {
        $bolsista = $this->bolsistaModel->find($id);

        if (!$bolsista) {
            return redirect()->to('/bolsistas')->with('erro', 'Bolsista não encontrado.');
        }

        $rules = [
            'nome'           => 'required|min_length[3]|max_length[255]',
            'cpf'            => "required|is_unique[bolsistas.cpf,id_bolsista,{$id}]",
            'email'          => "required|valid_email|is_unique[bolsistas.email,id_bolsista,{$id}]",
            'telefone'       => 'permit_empty|max_length[20]',
            'banco'          => 'permit_empty|max_length[50]',
            'agencia'        => 'permit_empty|max_length[20]',
            'conta_corrente' => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'           => $this->request->getPost('nome'),
            'cpf'            => $this->request->getPost('cpf'),
            'email'          => $this->request->getPost('email'),
            'telefone'       => $this->request->getPost('telefone'),
            'banco'          => $this->request->getPost('banco'),
            'agencia'        => $this->request->getPost('agencia'),
            'conta_corrente' => $this->request->getPost('conta_corrente'),
        ];

        if ($this->bolsistaModel->update($id, $dados)) {
            return redirect()->to('/bolsistas')->with('sucesso', 'Bolsista atualizado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar bolsista.');
    }

    /**
     * Remove o bolsista (a trigger SQLite intercepta e insere na bolsistas_historico)
     */
    public function delete($id)
    {
        if ($this->bolsistaModel->delete($id)) {
            return redirect()->to('/bolsistas')->with('sucesso', 'Bolsista removido com sucesso!');
        }

        return redirect()->to('/bolsistas')->with('erro', 'Erro ao remover bolsista.');
    }
}