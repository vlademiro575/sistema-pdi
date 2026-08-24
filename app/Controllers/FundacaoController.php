<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FundacaoModel;

class FundacaoController extends BaseController
{
    protected FundacaoModel $fundacaoModel;

    public function __construct()
    {
        $this->fundacaoModel = new FundacaoModel();
    }

    /**
     * Exibe a listagem de fundações
     */
    public function index()
    {
        $data = [
            'titulo'    => 'Gerenciamento de Fundações',
            'fundacoes' => $this->fundacaoModel->findAll()
        ];

        return view('fundacoes/index', $data);
    }

    /**
     * Formulário de criação
     */
    public function new()
    {
        $data = [
            'titulo' => 'Nova Fundação'
        ];

        return view('fundacoes/form', $data);
    }

    /**
     * Insere uma nova fundação no banco de dados
     */
    public function create()
    {
        $rules = [
            'nome'           => 'required|min_length[3]|max_length[255]',
            'cnpj'            => 'required|is_unique[fundacoes.cnpj]',
            'sigla'          => 'required|min_length[2]|max_length[10]',
            'tipo'          => 'required|in_list[FUNDACAO_APOIO,FAP_ESTADUAL,ORGAO_FEDERAL]' 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'           => $this->request->getPost('nome'),
            'cnpj'            => $this->request->getPost('cnpj'),
            'sigla'          => $this->request->getPost('sigla'),
            'tipo'          => $this->request->getPost('tipo')
        ];

        if ($this->fundacaoModel->insert($dados)) {
            return redirect()->to('/fundacoes')->with('sucesso', 'Fundação cadastrada com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao cadastrar fundação.');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $fundacao = $this->fundacaoModel->find($id);

        if (!$fundacao) {
            return redirect()->to('/fundacoes')->with('erro', 'Fundação não encontrada.');
        }

        $data = [
            'titulo'   => 'Editar Fundação',
            'fundacao' => $fundacao
        ];

        return view('fundacoes/form', $data);
    }

    /**
     * Atualiza o registro da fundação
     */
    public function update($id)
    {
        $fundacao = $this->fundacaoModel->find($id);

        if (!$fundacao) {
            return redirect()->to('/fundacoes')->with('erro', 'Fundação não encontrada.');
        }

        $rules = [
            'nome'           => 'required|min_length[3]|max_length[255]',
            'cnpj'            => "required|is_unique[fundacoes.cnpj,id_fundacao,{$id}]",
            'sigla'          => "required|min_length[2]|max_length[10]|is_unique[fundacoes.sigla,id_fundacao,{$id}]",
            'tipo'          => 'required|in_list[FUNDACAO_APOIO,FAP_ESTADUAL,ORGAO_FEDERAL]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'           => $this->request->getPost('nome'),
            'cnpj'            => $this->request->getPost('cnpj'),
            'sigla'          => $this->request->getPost('sigla'),
            'tipo'          => $this->request->getPost('tipo')
        ];
            

        if ($this->fundacaoModel->update($id, $dados)) {
            return redirect()->to('/fundacoes')->with('sucesso', 'Fundação atualizada com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar fundação.');
    }

    /**
     * Remove a fundação (a trigger SQLite intercepta e insere na fundacoes_historico)
     */
    public function delete($id)
    {
        if ($this->fundacaoModel->delete($id)) {
            return redirect()->to('/fundacoes')->with('sucesso', 'Fundação removida com sucesso!');
        }

        return redirect()->to('/fundacoes')->with('erro', 'Erro ao remover fundação.');
    }
}