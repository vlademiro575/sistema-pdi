<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DespesaModel;
use App\Models\ProjetoModel;
use App\Models\RubricaModel;
use App\Models\AnexoModel;
use App\Libraries\SefazXmlParser;

class DespesaController extends BaseController
{
    protected DespesaModel $despesaModel;
    protected ProjetoModel $projetoModel;
    protected RubricaModel $rubricaModel;
    protected AnexoModel $anexoModel;

    public function __construct()
    {
        $this->despesaModel = new DespesaModel();
        $this->projetoModel = new ProjetoModel();
        $this->rubricaModel = new RubricaModel();
        $this->anexoModel   = new AnexoModel();
    }

    /**
     * Listagem Geral de Despesas
     */
    public function index()
    {
        $data = [
            'titulo'   => 'Gestão de Despesas',
            'despesas' => $this->despesaModel->getDespesasComRelacionamentos()
        ];

        return view('despesas/index', $data);
    }

    /**
     * Tela de Nova Despesa
     */
    public function new()
    {


        /*
        Carrega as rubricas selecionadas caso haja um erro tipo valor acima do permitido
        Bug registrado em: 32 - Correção de Bug em Despesas
        */
        $idProjetoOld = old('id_projeto');
        $rubricas = [];

        if ($idProjetoOld) {
            $rubricas = $this->rubricaModel->where('id_projeto', $idProjetoOld)->findAll();
        }

        /* Fim da correção */

        $data = [
            'titulo'   => 'Lançamento de Nova Despesa',
            'projetos' => $this->projetoModel->findAll(),
            'rubricas' => $rubricas
        ];

        return view('despesas/form', $data);
    }

    /**
     * Formulário de Upload de XML SEFAZ (NF-e / NFC-e)
     */
    public function importarXml()
    {
        $data = [
            'titulo' => 'Importar Nota Fiscal via XML SEFAZ'
        ];

        return view('despesas/upload_xml', $data);
    }

    /**
     * Processa o arquivo XML e pré-preenche o formulário de despesa
     */
    public function processarXml()
    {
        $rules = [
            'xml_file' => 'uploaded[xml_file]|max_size[xml_file,5120]|ext_in[xml_file,xml]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('xml_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('erro', 'Arquivo XML inválido ou com falha no envio.');
        }

        try {
            $xmlContent = file_get_contents($file->getTempName());
            $parser = new SefazXmlParser();
            $dadosExtraidos = $parser->parse($xmlContent);

            $data = [
                'titulo'        => 'Lançamento de Nova Despesa (via XML SEFAZ)',
                'despesa'       => $dadosExtraidos,
                'projetos'      => $this->projetoModel->findAll(),
                'rubricas'      => [],
                'importado_xml' => true
            ];

            session()->setFlashdata('sucesso', 'Dados da Nota Fiscal extraídos com sucesso! Selecione o Projeto e a Rubrica correspondentes antes de salvar.');

            return view('despesas/form', $data);
        } catch (\Throwable $e) {
            return redirect()->back()->with('erro', 'Falha ao processar XML da NF-e: ' . $e->getMessage());
        }
    }

    /**
     * Salva uma nova despesa
     * (O SQLite Trigger criará o extrato e debitará o saldo da rubrica automaticamente)
     */
    public function create()
    {
        $rules = [
            'id_projeto'       => 'required|is_natural_no_zero',
            'id_rubrica'       => 'required|is_natural_no_zero',
            'data_emissao'     => 'required|valid_date',
            'valor_total'      => 'required|numeric|greater_than[0]',
            'numero_nota'      => 'permit_empty|max_length[100]',
            'nome_fornecedor'  => 'permit_empty|max_length[255]',
            'cnpj_fornecedor'  => 'permit_empty|max_length[20]',
            'comprovante'      => 'permit_empty|uploaded[comprovante]|max_size[comprovante,10240]|ext_in[comprovante,pdf,png,jpg,jpeg,xml]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validação prévia de saldo disponível (evita um valor maior do que a rubrica reservada)
        $rubrica = $this->rubricaModel->find($this->request->getPost('id_rubrica'));
        $valorTotal = (float) $this->request->getPost('valor_total');

        if ($rubrica && $valorTotal > (float) $rubrica['saldo_disponivel']) {
            return redirect()->back()->withInput()->with('erro', 'Saldo insuficiente na rubrica selecionada! Saldo disponível: R$ ' . number_format($rubrica['saldo_disponivel'], 2, ',', '.'));
        }

        $dadosDespesa = [
            'id_projeto'       => $this->request->getPost('id_projeto'),
            'id_rubrica'       => $this->request->getPost('id_rubrica'),
            'cnpj_fornecedor'  => $this->request->getPost('cnpj_fornecedor'),
            'nome_fornecedor'  => $this->request->getPost('nome_fornecedor'),
            'numero_nota'      => $this->request->getPost('numero_nota'),
            'data_emissao'     => $this->request->getPost('data_emissao'),
            'valor_total'      => $valorTotal,
            'descricao_itens'  => $this->request->getPost('descricao_itens'),
            'status_ocr'       => 'PROCESSADO'
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->despesaModel->insert($dadosDespesa);
        $idDespesa = $this->despesaModel->getInsertID();

        // Tratamento do Upload de Comprovante/Anexo
        $arquivo = $this->request->getFile('comprovante');
        if ($arquivo && $arquivo->isValid() && !$arquivo->hasMoved()) {
            $novoNome = $arquivo->getRandomName();
            $caminhoUpload = FCPATH . 'uploads/despesas/';

            if (!is_dir($caminhoUpload)) {
                mkdir($caminhoUpload, 0777, true);
            }

            $arquivo->move($caminhoUpload, $novoNome);

            $this->anexoModel->insert([
                'id_despesa'   => $idDespesa,
                'nome_arquivo' => $arquivo->getClientName(),
                'tipo'         => 'NOTA_FISCAL',
                'url'          => 'uploads/despesas/' . $novoNome
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('erro', 'Falha ao registrar a despesa.');
        }

        return redirect()->to('/despesas')->with('sucesso', 'Despesa lançada com sucesso! O extrato da rubrica foi atualizado.');
    }

    /**
     * Formulário de Edição
     */
    public function edit($id)
    {
        $despesa = $this->despesaModel->find($id);

        if (!$despesa) {
            return redirect()->to('/despesas')->with('erro', 'Despesa não encontrada.');
        }

        $data = [
            'titulo'   => 'Editar Despesa #' . $despesa['id_despesa'],
            'despesa'  => $despesa,
            'projetos' => $this->projetoModel->findAll(),
            'rubricas' => $this->rubricaModel->where('id_projeto', $despesa['id_projeto'])->findAll(),
            'anexos'   => $this->anexoModel->where('id_despesa', $id)->findAll()
        ];

        return view('despesas/form', $data);
    }

    /**
     * Atualização da despesa
     * (O Trigger do SQLite recalcula a diferença no saldo automaticamente)
     */
    public function update($id)
    {
        $despesa = $this->despesaModel->find($id);
        if (!$despesa) {
            return redirect()->to('/despesas')->with('erro', 'Despesa não encontrada.');
        }
        $valorAnterior = $despesa["valor_total"];


        $rules = [
            'id_projeto'       => 'required|is_natural_no_zero',
            'id_rubrica'       => 'required|is_natural_no_zero',
            'data_emissao'     => 'required|valid_date',
            'valor_total'      => 'required|numeric|greater_than[0]',
            'numero_nota'      => 'permit_empty|max_length[100]',
            'nome_fornecedor'  => 'permit_empty|max_length[255]',
            'cnpj_fornecedor'  => 'permit_empty|max_length[20]',
            'comprovante'      => 'permit_empty|uploaded[comprovante]|max_size[comprovante,10240]|ext_in[comprovante,pdf,png,jpg,jpeg,xml]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validação prévia de saldo disponível (evita um valor maior do que a rubrica reservada)
        $rubrica = $this->rubricaModel->find($this->request->getPost('id_rubrica'));
        $valorTotal = (float) $this->request->getPost('valor_total') - $valorAnterior;
        if ($rubrica && $valorTotal > (float) $rubrica['saldo_disponivel']) {
            return redirect()->back()->withInput()->with('erro', 'Saldo insuficiente na rubrica selecionada! Saldo disponível: R$ ' . number_format($rubrica['saldo_disponivel'], 2, ',', '.'));
        }



        $dadosAtualizacao = [
            'id_projeto'       => $this->request->getPost('id_projeto'),
            'id_rubrica'       => $this->request->getPost('id_rubrica'),
            'cnpj_fornecedor'  => $this->request->getPost('cnpj_fornecedor'),
            'nome_fornecedor'  => $this->request->getPost('nome_fornecedor'),
            'numero_nota'      => $this->request->getPost('numero_nota'),
            'data_emissao'     => $this->request->getPost('data_emissao'),
            'valor_total'      => (float) $this->request->getPost('valor_total'),
            'descricao_itens'  => $this->request->getPost('descricao_itens')
        ];

        $db = \Config\Database::connect();
        $db->transStart();
        $this->despesaModel->update($id, $dadosAtualizacao);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar despesa.');
        }

        // Upload de novo arquivo, se houver

        $arquivo = $this->request->getFile('comprovante');

        if ($arquivo && $arquivo->isValid() && !$arquivo->hasMoved()) {

            $novoNome = $arquivo->getRandomName();

            $caminhoUpload = FCPATH . 'uploads/despesas/';


            if (!is_dir($caminhoUpload)) {

                mkdir($caminhoUpload, 0777, true);

            }


            $arquivo->move($caminhoUpload, $novoNome);


            $this->anexoModel->insert([

                'id_despesa'   => $id,

                'nome_arquivo' => $arquivo->getClientName(),

                'tipo'         => 'NOTA_FISCAL',

                'url'          => 'uploads/despesas/' . $novoNome

            ]);

        }
        //-- Fim do upload



        return redirect()->to('/despesas/editar/' . $id)->with('sucesso', 'Despesa atualizada!');
    }

    /**
     * Exclui a despesa
     * (O Trigger do SQLite gera a linha de ESTORNO e devolve o saldo automaticamente)
     */
    public function delete($id)
    {
        $despesa = $this->despesaModel->find($id);

        if (!$despesa) {
            return redirect()->to('/despesas')->with('erro', 'Despesa não encontrada.');
        }

        if ($this->despesaModel->delete($id)) {
            return redirect()->to('/despesas')->with('sucesso', 'Despesa excluída! O valor foi estornado ao saldo da rubrica.');
        }

        return redirect()->to('/despesas')->with('erro', 'Erro ao excluir a despesa.');
    }

    /**
     * Processa a mudança de status da despesa com justificativa
     */
    public function mudarStatus($idDespesa)
    {
        // 1. Validação estrita
        $rules = [
            'status_novo'   => 'required|in_list[EM_ANALISE,APROVADO,REJEITADO]',
            'justificativa' => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $despesa = $this->despesaModel->find($idDespesa);
        
        if (!$despesa) {
            return redirect()->back()->with('erro', 'Despesa não encontrada.');
        }

        $statusAnterior = $despesa['status_aprovacao'];
        $statusNovo     = $this->request->getPost('status_novo');

        if ($statusAnterior === $statusNovo) {
            return redirect()->back()->with('erro', 'O novo status não pode ser igual ao atual.');
        }

        $db = \Config\Database::connect();
        
        // 2. Inicia a Transação para garantir integridade
        $db->transStart();

        // Atualiza a tabela principal
        $this->despesaModel->update($idDespesa, ['status_aprovacao' => $statusNovo]);

        // Insere o registro na tabela de auditoria
        $historicoStatusModel = new \App\Models\HistoricoStatusDespesaModel();
        $historicoStatusModel->insert([
            'id_despesa'      => $idDespesa,
            'status_anterior' => $statusAnterior,
            'status_novo'     => $statusNovo,
            'justificativa'   => $this->request->getPost('justificativa')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erro', 'Erro interno ao processar a mudança de status.');
        }

        return redirect()->back()->with('sucesso', 'Status da despesa atualizado e registrado na auditoria!');
    }

    /**
     * Endpoint AJAX para carregar as rubricas dinamicamente ao selecionar o projeto
     */
    public function getRubricasPorProjeto($idProjeto)
    {
        $rubricas = $this->rubricaModel->where('id_projeto', $idProjeto)->findAll();
        return $this->response->setJSON($rubricas);
    }
}
