<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProfessorModel;

class ProfessorController extends BaseController
{
    protected ProfessorModel $professorModel;

    public function __construct()
    {
        $this->professorModel = new ProfessorModel();
    }

    /**
     * Exibe a listagem de professores
     */
    public function index()
    {
        $data = [
            'titulo'    => 'Gerenciamento de Professores',
            'professores' => $this->professorModel->orderBy('nome', 'ASC')->findAll()
        ];

        return view('professores/index', $data);
    }

    /**
     * Formulário de criação
     */
    public function new()
    {
        $data = [
            'titulo' => 'Novo Professor'
        ];

        return view('professores/form', $data);
    }

    /**
     * Insere um novo professor no banco de dados
     */
    public function create()
    {
        $rules = [
            'nome'           => 'required|min_length[3]|max_length[255]',
            'cpf'            => 'required|is_unique[professores.cpf]',
            'email'          => 'required|valid_email|is_unique[professores.email]',
            'siape'          => 'permit_empty|max_length[20]',
            'telefone'       => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'           => $this->request->getPost('nome'),
            'cpf'            => $this->request->getPost('cpf'),
            'email'          => $this->request->getPost('email'),
            'siape'          => $this->request->getPost('siape'),
            'telefone'          => $this->request->getPost('telefone')
        ];

        if ($this->professorModel->insert($dados)) {
            return redirect()->to('/professores')->with('sucesso', 'Professor cadastrado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao cadastrar professor.');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $professor = $this->professorModel->find($id);

        if (!$professor) {
            return redirect()->to('/professores')->with('erro', 'Professor não encontrado.');
        }

        $data = [
            'titulo'   => 'Editar Professor',
            'professor' => $professor
        ];

        return view('professores/form', $data);
    }

    /**
     * Atualiza o registro do professor
     */
    public function update($id)
    {
        $professor = $this->professorModel->find($id);

        if (!$professor) {
            return redirect()->to('/professores')->with('erro', 'Professor não encontrado.');
        }

        $rules = [
            'nome'           => 'required|min_length[3]|max_length[255]',
            'cpf'            => "required|is_unique[professores.cpf,id_professor,{$id}]",
            'email'          => "required|valid_email|is_unique[professores.email,id_professor,{$id}]",
            'siape'          => 'permit_empty|max_length[20]',
            'telefone'       => 'permit_empty|max_length[20]'
           
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'           => $this->request->getPost('nome'),
            'cpf'            => $this->request->getPost('cpf'),
            'email'          => $this->request->getPost('email'),
            'siape'          => $this->request->getPost('siape'),
            'telefone'       => $this->request->getPost('telefone')
         
        ];

        if ($this->professorModel->update($id, $dados)) {
            return redirect()->to('/professores')->with('sucesso', 'Professor atualizado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar professor.');
    }

    /**
     * Remove o professor (a trigger SQLite intercepta e insere na professores_historico)
     */
    public function delete($id)
    {
        if ($this->professorModel->delete($id)) {
            return redirect()->to('/professores')->with('sucesso', 'Professor removido com sucesso!');
        }

        return redirect()->to('/professores')->with('erro', 'Erro ao remover professor.');
    }
}