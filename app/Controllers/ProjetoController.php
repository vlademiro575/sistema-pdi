<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProjetoModel;
use App\Models\ProfessorModel;
use App\Models\BolsistaModel;
use App\Models\FundacaoModel;
use App\Models\RubricaModel;
use App\Models\ProjetoBolsistaModel;

class ProjetoController extends BaseController
{
    protected ProjetoModel $projetoModel;

    public function __construct()
    {
        $this->projetoModel = new ProjetoModel();
    }

    /**
     * Exibe a listagem geral de projetos
     */
    public function index()
    {
        $data = [
            'titulo'   => 'Gerenciamento de Projetos',
            'projetos' => $this->projetoModel->findAll()
        ];

        return view('projetos/index', $data);
    }

    /**
     * Formulário de criação do Projeto (Mestre)
     */
    public function new()
    {
        $professorModel = new ProfessorModel();
        $fundacaoModel = new FundacaoModel();

        $data = [
            'titulo'      => 'Novo Projeto',
            'professores' => $professorModel->findAll(),
            'fundacoes'   => $fundacaoModel->findAll()
        ];

        return view('projetos/form', $data);
    }

    /**
     * Insere um novo projeto no banco de dados e redireciona para as abas
     */
    public function create()
    {
        $rules = [
            'id_professor'            => 'required|is_natural_no_zero',
            'id_fundacao'             => 'required|is_natural_no_zero',
            'codigo_projeto_fundacao' => 'required|max_length[255]',
            'titulo'                  => 'required|min_length[3]|max_length[255]',
            'orcamento_total'         => 'required|numeric',
            'data_inicio'             => 'required|valid_date',
            'data_fim'                => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'id_professor'            => $this->request->getPost('id_professor'),
            'id_fundacao'             => $this->request->getPost('id_fundacao'),
            'codigo_projeto_fundacao' => $this->request->getPost('codigo_projeto_fundacao'),
            'titulo'                  => $this->request->getPost('titulo'),
            'orcamento_total'         => $this->request->getPost('orcamento_total'),
            'data_inicio'             => $this->request->getPost('data_inicio'),
            'data_fim'                => $this->request->getPost('data_fim'),
        ];

        if ($this->projetoModel->insert($dados)) {
            $idProjeto = $this->projetoModel->getInsertID();
            
            // Redireciona diretamente para a tela de gerenciar (onde ficam as abas de Rubricas e Bolsistas)
            return redirect()->to("/projetos/gerenciar/{$idProjeto}")
                             ->with('sucesso', 'Projeto criado com sucesso! Agora adicione as rubricas e a equipe.');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao cadastrar projeto.');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $projeto = $this->projetoModel->find($id);

        if (!$projeto) {
            return redirect()->to('/projetos')->with('erro', 'Projeto não encontrado.');
        }

        $professorModel = new ProfessorModel();
        $fundacaoModel = new FundacaoModel();

        $data = [
            'titulo'      => 'Editar Projeto',
            'projeto'     => $projeto,
            'professores' => $professorModel->findAll(),
            'fundacoes'   => $fundacaoModel->findAll()
        ];

        return view('projetos/form', $data);
    }

    /**
     * Atualiza o registro do projeto
     */
    public function update($id)
    {
        $projeto = $this->projetoModel->find($id);

        if (!$projeto) {
            return redirect()->to('/projetos')->with('erro', 'Projeto não encontrado.');
        }

        $rules = [
            'id_professor'            => 'required|is_natural_no_zero',
            'id_fundacao'             => 'required|is_natural_no_zero',
            'codigo_projeto_fundacao' => 'required|max_length[255]',
            'titulo'                  => 'required|min_length[3]|max_length[255]',
            'orcamento_total'         => 'required|numeric',
            'data_inicio'             => 'required|valid_date',
            'data_fim'                => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'id_professor'            => $this->request->getPost('id_professor'),
            'id_fundacao'             => $this->request->getPost('id_fundacao'),
            'codigo_projeto_fundacao' => $this->request->getPost('codigo_projeto_fundacao'),
            'titulo'                  => $this->request->getPost('titulo'),
            'orcamento_total'         => $this->request->getPost('orcamento_total'),
            'data_inicio'             => $this->request->getPost('data_inicio'),
            'data_fim'                => $this->request->getPost('data_fim'),
        ];

        if ($this->projetoModel->update($id, $dados)) {
            // Ao atualizar, podemos mandar o usuário de volta para o gerenciamento do próprio projeto
            return redirect()->to("/projetos/gerenciar/{$id}")->with('sucesso', 'Projeto atualizado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar projeto.');
    }

    /**
     * Remove o projeto (a trigger SQLite intercepta e insere na projetos_historico)
     */
    public function delete($id)
    {
        if ($this->projetoModel->delete($id)) {
            return redirect()->to('/projetos')->with('sucesso', 'Projeto removido com sucesso!');
        }

        return redirect()->to('/projetos')->with('erro', 'Erro ao remover projeto.');
    }

    /**
     * Tela de Gerenciamento Mestre-Detalhe (com Abas)
     */
    public function gerenciar($id)
    {
        $projeto = $this->projetoModel->find($id);

        if (!$projeto) {
            return redirect()->to('/projetos')->with('erro', 'Projeto não encontrado.');
        }

        $rubricaModel = new RubricaModel();
        $projetoBolsistaModel = new ProjetoBolsistaModel();
        $bolsistaModel = new BolsistaModel();

        $data = [
            'titulo'   => 'Painel do Projeto: ' . $projeto['codigo_projeto_fundacao'],
            'projeto'  => $projeto,
            'rubricas' => $rubricaModel->where('id_projeto', $id)->findAll(),
            'equipe'               => $projetoBolsistaModel->where('id_projeto', $id)->findAll(),
            'bolsistas_disponiveis'=> $bolsistaModel->findAll() // Traz todos os bolsistas para o <select>
        ];

        return view('projetos/gerenciar', $data);
    }
}