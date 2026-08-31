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
        $projetos = $this->projetoModel->findAll();

        $db = \Config\Database::connect();

        // Professores para mapear os nomes
        $professoresRaw = $db->table('professores')->get()->getResultArray();
        $professoresMap = [];
        foreach ($professoresRaw as $p) {
            $professoresMap[$p['id_professor']] = $p['nome'];
        }

        // Fundações para mapear as siglas
        $fundacoesRaw = $db->table('fundacoes')->get()->getResultArray();
        $fundacoesMap = [];
        foreach ($fundacoesRaw as $f) {
            $fundacoesMap[$f['id_fundacao']] = $f['sigla'] . ' - ' . $f['nome'];
        }

        // Histórico de alterações ordenado decrescente por _atualizado_em
        $historicosRaw = $db->table('projetos_historico')
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $historicosPorProjeto = [];
        foreach ($historicosRaw as $h) {
            $historicosPorProjeto[$h['id_projeto']][] = $h;
        }

        $data = [
            'titulo'               => 'Gerenciamento de Projetos',
            'projetos'             => $projetos,
            'professoresMap'       => $professoresMap,
            'fundacoesMap'         => $fundacoesMap,
            'historicosPorProjeto' => $historicosPorProjeto
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
            'professores' => $professorModel->orderBy('nome', 'ASC')->findAll(),
            'fundacoes'   => $fundacaoModel->orderBy('sigla', 'ASC')->findAll()
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
            'professores' => $professorModel->orderBy('nome', 'ASC')->findAll(),
            'fundacoes'   => $fundacaoModel->orderBy('sigla', 'ASC')->findAll()
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

        $abaAtiva = $this->request->getGet('aba') ?? session()->getFlashdata('aba') ?? 'rubricas';
        $rubricas = $rubricaModel->where('id_projeto', $id)->findAll();

        $db = \Config\Database::connect();

        // Histórico de alterações de rubricas deste projeto ordenado decrescente por _atualizado_em
        $historicosRubricasRaw = $db->table('rubricas_historico')
            ->where('id_projeto', $id)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $historicosPorRubrica = [];
        foreach ($historicosRubricasRaw as $h) {
            $historicosPorRubrica[$h['id_rubrica']][] = $h;
        }

        // Histórico de alterações de vínculos de bolsistas deste projeto ordenado decrescente por _atualizado_em
        $historicosBolsistasRaw = $db->table('projetos_bolsistas_historico')
            ->where('id_projeto', $id)
            ->orderBy('_atualizado_em', 'DESC')
            ->get()
            ->getResultArray();

        $historicosPorVinculoBolsista = [];
        foreach ($historicosBolsistasRaw as $h) {
            $historicosPorVinculoBolsista[$h['id_projeto_bolsista']][] = $h;
        }

        // Mapeia nomes dos bolsistas para o histórico
        $bolsistasRaw = $db->table('bolsistas')->get()->getResultArray();
        $bolsistasMap = [];
        foreach ($bolsistasRaw as $b) {
            $bolsistasMap[$b['id_bolsista']] = $b['nome'];
        }

        $data = [
            'titulo'                       => 'Painel do Projeto: ' . $projeto['codigo_projeto_fundacao'],
            'projeto'                      => $projeto,
            'rubricas'                     => $rubricas,
            'historicosPorRubrica'         => $historicosPorRubrica,
            'equipe'                       => $projetoBolsistaModel->getBolsistasPorProjeto((int) $id),
            'historicosPorVinculoBolsista' => $historicosPorVinculoBolsista,
            'bolsistasMap'                 => $bolsistasMap,
            'bolsistas_disponiveis'        => $bolsistaModel->findAll(), // Traz todos os bolsistas para o <select>
            'abaAtiva'                     => $abaAtiva
        ];

        return view('projetos/gerenciar', $data);
    }
}