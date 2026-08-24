<?php
/*
Como a Rubrica é a Tabela Detalhe atrelada ao Projeto (Tabela Mestre), nós não precisamos dos métodos de exibição de formulários soltos (index(), new() e edit()). 

Todo o cadastro e edição das rubricas acontecerão através de Modais (janelas flutuantes) dentro da tela de gerenciamento do próprio projeto.

Por isso, o RubricaController atua estritamente como um "operário de bastidores": ele recebe a requisição (POST), valida os dados, salva no banco e redireciona o usuário imediatamente de volta para a tela do projeto original.

Destaques da Implementação

    Validação Restritiva (in_list): Utilizamos a regra in_list[CUSTEIO,CAPITAL,BOLSAS] na validação do campo tipo. Isso garante que o usuário não consiga forçar um valor que violaria a Constraint CHECK diretamente no seu banco SQLite.

    Redirecionamentos Cirúrgicos: Repare que todas as ações de sucesso ou falha terminam em redirect()->to("/projetos/gerenciar/{$idProjeto}"). Isso garante que o usuário não se perca no sistema e sempre retorne para a tela do projeto que ele estava editando originalmente.

    Regra de Saldo Automático: No método create(), o campo saldo_disponivel é preenchido de forma transparente no backend com o exato valor do valor_alocado.
*/


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RubricaModel;

class RubricaController extends BaseController
{
    protected RubricaModel $rubricaModel;

    public function __construct()
    {
        $this->rubricaModel = new RubricaModel();
    }

    /**
     * Insere uma nova rubrica no banco de dados
     * (Recebe o POST do Modal na tela do Projeto)
     */
    public function create()
    {
        $idProjeto = $this->request->getPost('id_projeto');

        $rules = [
            'id_projeto'    => 'required|is_natural_no_zero',
            'nome'          => 'required|min_length[3]|max_length[255]',
            'tipo'          => 'required|in_list[CUSTEIO,CAPITAL,BOLSAS]',
            'valor_alocado' => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $valorAlocado = $this->request->getPost('valor_alocado');

        $dados = [
            'id_projeto'       => $idProjeto,
            'nome'             => $this->request->getPost('nome'),
            'tipo'             => $this->request->getPost('tipo'),
            'valor_alocado'    => $valorAlocado,
            // Regra de Negócio: Ao nascer, o saldo disponível é exatamente igual ao valor alocado
            'saldo_disponivel' => $valorAlocado, 
        ];

        if ($this->rubricaModel->insert($dados)) {
            // Redireciona de volta para a aba de rubricas do Projeto Mestre
            return redirect()->to("/projetos/gerenciar/{$idProjeto}")
                             ->with('sucesso', 'Rubrica adicionada com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao cadastrar a rubrica.');
    }

    /**
     * Atualiza o registro da rubrica
     */
    public function update($id)
    {
        $rubrica = $this->rubricaModel->find($id);

        if (!$rubrica) {
            return redirect()->back()->with('erro', 'Rubrica não encontrada.');
        }

        $idProjeto = $rubrica['id_projeto'];

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'tipo' => 'required|in_list[CUSTEIO,CAPITAL,BOLSAS]',
            // ATENÇÃO: Se for alterar o valor_alocado no futuro, 
            // será necessário recalcular o saldo_disponivel considerando as despesas já existentes!
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome' => $this->request->getPost('nome'),
            'tipo' => $this->request->getPost('tipo'),
        ];

        if ($this->rubricaModel->update($id, $dados)) {
            return redirect()->to("/projetos/gerenciar/{$idProjeto}")->with('sucesso', 'Rubrica atualizada com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar a rubrica.');
    }

    /**
     * Remove a rubrica 
     * (A trigger SQLite intercepta e arquiva na rubricas_historico)
     */
    public function delete($id)
    {
        $rubrica = $this->rubricaModel->find($id);

        if (!$rubrica) {
            return redirect()->back()->with('erro', 'Rubrica não encontrada.');
        }

        $idProjeto = $rubrica['id_projeto'];

        // O Delete acionará a Trigger do SQLite para salvar no Histórico, gravando quem apagou
        if ($this->rubricaModel->delete($id)) {
            return redirect()->to("/projetos/gerenciar/{$idProjeto}")->with('sucesso', 'Rubrica removida com sucesso!');
        }

        return redirect()->to("/projetos/gerenciar/{$idProjeto}")->with('erro', 'Erro ao remover a rubrica.');
    }
}