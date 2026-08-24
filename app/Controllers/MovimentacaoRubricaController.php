<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MovimentacaoRubricaModel;
use App\Models\RubricaModel;

class MovimentacaoRubricaController extends BaseController
{
    protected MovimentacaoRubricaModel $movimentacaoModel;
    protected RubricaModel $rubricaModel;

    public function __construct()
    {
        $this->movimentacaoModel = new MovimentacaoRubricaModel();
        $this->rubricaModel = new RubricaModel();
    }

    public function createAporteAjuste()
    {
        $idRubrica = $this->request->getPost('id_rubrica');
        $idProjeto = $this->request->getPost('id_projeto');
        
        $rules = [
            'id_rubrica' => 'required|is_natural_no_zero',
            'tipo'       => 'required|in_list[APORTE,AJUSTE]',
            'valor'      => 'required|numeric',
            'descricao'  => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rubrica = $this->rubricaModel->find($idRubrica);
        if (!$rubrica) {
            return redirect()->back()->with('erro', 'Rubrica não encontrada.');
        }

        $valorOperacao = (float) $this->request->getPost('valor');
        $saldoAnterior = (float) $rubrica['saldo_disponivel'];
        $saldoPosterior = $saldoAnterior + $valorOperacao;

        $dadosMovimentacao = [
            'id_rubrica'      => $idRubrica,
            'tipo'            => $this->request->getPost('tipo'),
            'valor'           => abs($valorOperacao), // Guarda o valor absoluto
            'saldo_anterior'  => $saldoAnterior,
            'saldo_posterior' => $saldoPosterior,
            'descricao'       => $this->request->getPost('descricao')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Grava o extrato manual
        $this->movimentacaoModel->insert($dadosMovimentacao);

        // 2. Atualiza o saldo principal da rubrica
        $this->rubricaModel->update($idRubrica, ['saldo_disponivel' => $saldoPosterior]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erro', 'Erro ao registrar a movimentação.');
        }

        return redirect()->to("/projetos/gerenciar/{$idProjeto}")->with('sucesso', 'Movimentação registrada com sucesso!');
    }

    /**
     * Carrega a tela de leitura do Extrato Financeiro
     */
    public function extrato($idRubrica)
    {
        $rubrica = $this->rubricaModel->find($idRubrica);

        if (!$rubrica) {
            return redirect()->back()->with('erro', 'Rubrica não encontrada.');
        }

        // Busca todas as movimentações dessa rubrica ordenadas da mais antiga para a mais recente
        $movimentacoes = $this->movimentacaoModel
                              ->where('id_rubrica', $idRubrica)
                              ->orderBy('_criado_em', 'ASC')
                              ->findAll();

        $data = [
            'titulo'        => 'Extrato da Rubrica #' . $idRubrica,
            'rubrica'       => $rubrica,
            'movimentacoes' => $movimentacoes
        ];

        return view('movimentacoes/extrato', $data);
    }
}