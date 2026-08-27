<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>
<?php //  Bug registrado em: 32 - Correção de Bug em Despesas ?>
<script>
$(document).ready(function() {
        // Se já houver um projeto selecionado (ex: após um erro de validação/saldo), dispara o change para garantir sincronia
        var projetoSelecionado = $('#id_projeto').val();
        var rubricaAntiga = '<?= old('id_rubrica') ?>';

        // Carrega dinamicamente as rubricas ao selecionar o projeto
        $('#id_projeto').on('change', function() {
            var idProjeto = $(this).val();
            var $selectRubrica = $('#id_rubrica');

            $selectRubrica.html('<option value="" disabled selected>Carregando rubricas...</option>');

            if (idProjeto) {
                fetch('<?= base_url('despesas/rubricas-por-projeto') ?>/' + idProjeto)
                    .then(response => response.json())
                    .then(data => {
                        $selectRubrica.empty();
                        if (data.length > 0) {
                            $selectRubrica.append('<option value="" disabled selected>Selecione a rubrica...</option>');
                            $.each(data, function(index, rubrica) {
                                var saldoFormatado = parseFloat(rubrica.saldo_disponivel).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                
                                // Verifica se é a rubrica que estava selecionada antes do erro
                                var selectedAttr = (rubrica.id_rubrica == rubricaAntiga) ? 'selected' : '';
                                
                                $selectRubrica.append('<option value="' + rubrica.id_rubrica + '" ' + selectedAttr + '>' + rubrica.nome + ' (Saldo: R$ ' + saldoFormatado + ')</option>');
                            });
                        } else {
                            $selectRubrica.append('<option value="" disabled selected>Nenhuma rubrica cadastrada neste projeto</option>');
                        }
                    })
                    .catch(error => {
                        $selectRubrica.html('<option value="" disabled selected>Erro ao carregar rubricas</option>');
                    });
            }
        });
    });
</script>
    <?php //  Fim da correção ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?= isset($despesa['id_despesa']) ? 'Editar Despesa #' . $despesa['id_despesa'] : ($titulo ?? 'Nova Despesa') ?>
        </h1>
        <a href="<?= base_url('despesas') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar para a Lista
        </a>
    </div>

    <?php if (!empty($importado_xml)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="font-weight-bold mb-1">
                <i class="fas fa-file-invoice mr-1"></i> Dados importados do XML SEFAZ com sucesso!
            </h6>
            <span class="small">Os campos da nota fiscal foram pré-preenchidos abaixo. Selecione o <strong>Projeto de PDI</strong> e a <strong>Rubrica Orçamentária</strong> para vincular a despesa e clique em <strong>Salvar Despesa</strong> para concluir.</span>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados da Compra e Prestação de Contas</h6>
        </div>
        <div class="card-body">
            <form action="<?= isset($despesa['id_despesa']) ? base_url('despesas/update/' . $despesa['id_despesa']) : base_url('despesas/create') ?>" 
                  method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="id_projeto" class="font-weight-bold">Projeto de PDI <span class="text-danger">*</span></label>
                        <select name="id_projeto" id="id_projeto" class="form-control" required>
                            <option value="" disabled <?= empty($despesa['id_projeto']) ? 'selected' : '' ?>>Selecione o Projeto...</option>
                            <?php foreach ($projetos as $proj): ?>
                                <option value="<?= $proj['id_projeto'] ?>" 
                                    <?= set_select('id_projeto', $proj['id_projeto'], (isset($despesa['id_projeto']) && $despesa['id_projeto'] == $proj['id_projeto'])) ?>>
                                    <?= esc($proj['codigo_projeto_fundacao']) ?> - <?= esc($proj['titulo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="id_rubrica" class="font-weight-bold">Rubrica Orçamentária <span class="text-danger">*</span></label>
                        <select name="id_rubrica" id="id_rubrica" class="form-control" required>
                            <option value="" disabled selected>Selecione um projeto primeiro...</option>
                            <?php if (!empty($rubricas)): ?>
                                <?php foreach ($rubricas as $rub): ?>
                                    <option value="<?= $rub['id_rubrica'] ?>" 
                                        <?= set_select('id_rubrica', $rub['id_rubrica'], (isset($despesa['id_rubrica']) && $despesa['id_rubrica'] == $rub['id_rubrica'])) ?>>
                                        <?= esc($rub['nome']) ?> (Saldo: R$ <?= number_format($rub['saldo_disponivel'], 2, ',', '.') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="data_emissao" class="font-weight-bold">Data de Emissão <span class="text-danger">*</span></label>
                        <input type="date" name="data_emissao" id="data_emissao" class="form-control" 
                               value="<?= old('data_emissao', $despesa['data_emissao'] ?? date('Y-m-d')) ?>" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="valor_total" class="font-weight-bold">Valor Total (R$) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="valor_total" id="valor_total" class="form-control font-weight-bold text-danger" 
                               placeholder="0.00" value="<?= old('valor_total', $despesa['valor_total'] ?? '') ?>" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="numero_nota" class="font-weight-bold">Número do Documento / Nota Fiscal</label>
                        <input type="text" name="numero_nota" id="numero_nota" class="form-control" placeholder="Ex: NF-12345" 
                               value="<?= old('numero_nota', $despesa['numero_nota'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nome_fornecedor" class="font-weight-bold">Razão Social / Nome do Fornecedor</label>
                        <input type="text" name="nome_fornecedor" id="nome_fornecedor" class="form-control" placeholder="Ex: Dell Computadores Ltda" 
                               value="<?= old('nome_fornecedor', $despesa['nome_fornecedor'] ?? '') ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="cnpj_fornecedor" class="font-weight-bold">CNPJ do Fornecedor</label>
                        <input type="text" name="cnpj_fornecedor" id="cnpj_fornecedor" class="form-control" placeholder="00.000.000/0000-00" 
                               value="<?= old('cnpj_fornecedor', $despesa['cnpj_fornecedor'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="descricao_itens" class="font-weight-bold">Descrição dos Itens / Justificativa</label>
                    <textarea name="descricao_itens" id="descricao_itens" rows="3" class="form-control" 
                              placeholder="Detalhe os itens adquiridos..."><?= old('descricao_itens', $despesa['descricao_itens'] ?? '') ?></textarea>
                </div>

                <div class="form-row">

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Status da Prestação de Contas</label>
                    <div>
                        <?php 
                            $badgeStatus = match($despesa['status_aprovacao'] ?? 'EM_ANALISE') {
                                'APROVADO'   => 'badge-success',
                                'REJEITADO'  => 'badge-danger',
                                default      => 'badge-warning text-dark'
                            };
                        ?>
                        <span class="badge <?= $badgeStatus ?> p-2" style="font-size: 0.9rem;">
                            <i class="fas fa-info-circle mr-1"></i> <?= esc($despesa['status_aprovacao'] ?? 'EM_ANALISE') ?>
                        </span>
                        <small class="form-text text-muted mt-1">A alteração de status deve ser feita pelo painel de listagem usando o botão "Avaliar Despesa".</small>
                    </div>
                </div>
      
                    <div class="form-group col-md-6">
                        <label for="comprovante" class="font-weight-bold">Anexar Comprovante / PDF ou XML da Nota</label>
                        <input type="file" name="comprovante" id="comprovante" class="form-control-file">
                        <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, XML (Máx: 10MB)</small>
                    </div>
                </div>

                <?php if (!empty($anexos)): ?>
                    <div class="alert alert-light border mt-3">
                        <h6 class="font-weight-bold text-gray-800"><i class="fas fa-paperclip mr-1"></i> Comprovantes Anexados:</h6>
                        <ul class="mb-0">
                            <?php foreach ($anexos as $anexo): ?>
                                <li>
                                    <a href="<?= base_url($anexo['url']) ?>" target="_blank" class="font-weight-bold">
                                        <?= esc($anexo['nome_arquivo']) ?>
                                    </a> 
                                    <small class="text-muted">(Tipo: <?= esc($anexo['tipo']) ?>)</small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <hr>

                <div class="text-right">
                    <a href="<?= base_url('despesas') ?>" class="btn btn-secondary mr-2">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Salvar Despesa
                    </button>
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Carrega dinamicamente as rubricas ao selecionar o projeto
        $('#id_projeto').on('change', function() {
            var idProjeto = $(this).val();
            var $selectRubrica = $('#id_rubrica');

            $selectRubrica.html('<option value="" disabled selected>Carregando rubricas...</option>');

            if (idProjeto) {
                fetch('<?= base_url('despesas/rubricas-por-projeto') ?>/' + idProjeto)
                    .then(response => response.json())
                    .then(data => {
                        $selectRubrica.empty();
                        if (data.length > 0) {
                            $selectRubrica.append('<option value="" disabled selected>Selecione a rubrica...</option>');
                            $.each(data, function(index, rubrica) {
                                var saldoFormatado = parseFloat(rubrica.saldo_disponivel).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                $selectRubrica.append('<option value="' + rubrica.id_rubrica + '">' + rubrica.nome + ' (Saldo: R$ ' + saldoFormatado + ')</option>');
                            });
                        } else {
                            $selectRubrica.append('<option value="" disabled selected>Nenhuma rubrica cadastrada neste projeto</option>');
                        }
                    })
                    .catch(error => {
                        $selectRubrica.html('<option value="" disabled selected>Erro ao carregar rubricas</option>');
                    });
            }
        });
    });
</script>
<?= $this->endSection() ?>