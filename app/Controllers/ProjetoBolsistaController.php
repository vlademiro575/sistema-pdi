<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProjetoBolsistaModel;

class ProjetoBolsistaController extends BaseController
{
    protected ProjetoBolsistaModel $projetoBolsistaModel;

    public function __construct()
    {
        $this->projetoBolsistaModel = new ProjetoBolsistaModel();
    }

    public function create()
    {
        $idProjeto = $this->request->getPost('id_projeto');

        $rules = [
            'id_projeto'  => 'required|is_natural_no_zero',
            'id_bolsista' => 'required|is_natural_no_zero',
            'valor_bolsa' => 'required|numeric|greater_than[0]',
            'data_inicio' => 'required|valid_date',
            'data_fim'    => 'permit_empty|valid_date',
            'status'      => 'required|in_list[ATIVO,INATIVO,DESLIGADO]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'id_projeto'  => $idProjeto,
            'id_bolsista' => $this->request->getPost('id_bolsista'),
            'valor_bolsa' => $this->request->getPost('valor_bolsa'),
            'data_inicio' => $this->request->getPost('data_inicio'),
            'data_fim'    => $this->request->getPost('data_fim') ?: null,
            'status'      => $this->request->getPost('status')
        ];

        if ($this->projetoBolsistaModel->insert($dados)) {
            return redirect()->to("/projetos/gerenciar/{$idProjeto}")->with('sucesso', 'Bolsista vinculado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao vincular o bolsista.');
    }

    public function delete($id)
    {
        $vinculo = $this->projetoBolsistaModel->find($id);

        if (!$vinculo) {
            return redirect()->back()->with('erro', 'Vínculo não encontrado.');
        }

        $idProjeto = $vinculo['id_projeto'];

        if ($this->projetoBolsistaModel->delete($id)) {
            return redirect()->to("/projetos/gerenciar/{$idProjeto}")->with('sucesso', 'Bolsista desvinculado com sucesso!');
        }

        return redirect()->to("/projetos/gerenciar/{$idProjeto}")->with('erro', 'Erro ao desvincular bolsista.');
    }
}