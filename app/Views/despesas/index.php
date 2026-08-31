<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Despesas</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Lançamentos de Despesas</h1>
        <div>
            <a href="<?= base_url('despesas/importar-xml') ?>" class="btn btn-sm btn-info shadow-sm mr-2">
                <i class="fas fa-file-code fa-sm mr-1"></i> Importar XML (NF-e)
            </a>
            <a href="<?= base_url('despesas/novo') ?>" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm mr-1"></i> Nova Despesa
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Extrato Geral de Compras e Pagamentos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-items-center" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Data</th>
                            <th>Projeto / Rubrica</th>
                            <th>Fornecedor / Nota</th>
                            <th class="text-right">Valor Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 170px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($despesas) && is_array($despesas)): ?>
                            <?php foreach ($despesas as $d): ?>
                                <tr>
                                    <td><?= formatar_data($d['data_emissao']) ?></td>
                                    <td>
                                        <span class="badge badge-dark"><?= esc($d['codigo_projeto_fundacao'] ?? 'Proj #' . $d['id_projeto']) ?></span><br>
                                        <small class="text-muted"><i class="fas fa-wallet mr-1"></i><?= esc($d['rubrica_nome'] ?? 'Rubrica #' . $d['id_rubrica']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= esc($d['nome_fornecedor'] ?: 'Não informado') ?></strong><br>
                                        <small class="text-muted">Doc: <?= esc($d['numero_nota'] ?: 'S/N') ?></small>
                                    </td>
                                    <td class="text-right font-weight-bold text-danger">
                                        R$ <?= number_format($d['valor_total'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $badgeStatus = match($d['status_aprovacao']) {
                                                'APROVADO'   => 'badge-success',
                                                'REJEITADO'  => 'badge-danger',
                                                default      => 'badge-warning text-dark'
                                            };
                                        ?>
                                        <span class="badge <?= $badgeStatus ?>"><?= esc($d['status_aprovacao']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('despesas/editar/' . $d['id_despesa']) ?>" 
                                           class="btn btn-sm btn-info btn-circle shadow-sm" title="Editar Despesa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="<?= base_url('despesas/delete/' . $d['id_despesa']) ?>" 
                                           class="btn btn-sm btn-danger btn-circle shadow-sm" 
                                           onclick="return confirm('ATENÇÃO: A exclusão da despesa estornará automaticamente o saldo na rubrica. Confirmar?');" 
                                           title="Excluir / Estornar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-warning btn-circle shadow-sm btn-status" 
                                                data-toggle="modal" data-target="#modalStatusDespesa"
                                                data-iddespesa="<?= $d['id_despesa'] ?>"
                                                data-statusatual="<?= $d['status_aprovacao'] ?>"
                                                title="Avaliar Despesa">
                                            <i class="fas fa-clipboard-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary btn-circle shadow-sm" 
                                                data-toggle="modal" data-target="#modalHistorico<?= $d['id_despesa'] ?>" 
                                                title="Histórico de Alterações">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i> Nenhuma despesa lançada até o momento.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Status Despesa -->
    <div class="modal fade" id="modalStatusDespesa" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formMudarStatus" method="post" action="">
                    <?= csrf_field() ?>
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-clipboard-check mr-1"></i> Avaliar Despesa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Novo Status <span class="text-danger">*</span></label>
                            <select name="status_novo" id="status_novo" class="form-control" required>
                                <option value="EM_ANALISE">Em Análise</option>
                                <option value="APROVADO">Aprovado</option>
                                <option value="REJEITADO">Rejeitado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Justificativa da Decisão <span class="text-danger">*</span></label>
                            <textarea name="justificativa" class="form-control" rows="3" minlength="5" placeholder="Explique o motivo da aprovação ou rejeição..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning font-weight-bold text-dark">Confirmar Avaliação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAIS DE HISTÓRICO (Renderizados a partir de despesas_historico) -->
    <?php if (!empty($despesas) && is_array($despesas)): ?>
        <?php foreach ($despesas as $d): ?>
            <?php 
                $historicoDespesa = $historicosPorDespesa[$d['id_despesa']] ?? [];
                $nomeProj = $d['codigo_projeto_fundacao'] ?? ($projetosMap[$d['id_projeto']] ?? ('Projeto ID #' . $d['id_projeto']));
                $nomeRubr = $d['rubrica_nome'] ?? ($rubricasMap[$d['id_rubrica']] ?? ('Rubrica ID #' . $d['id_rubrica']));
            ?>
            <div class="modal fade" id="modalHistorico<?= $d['id_despesa'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHistoricoLabel<?= $d['id_despesa'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title font-weight-bold" id="modalHistoricoLabel<?= $d['id_despesa'] ?>">
                                <i class="fas fa-history mr-2"></i> Histórico de Alterações: Despesa #<?= $d['id_despesa'] ?> - <span class="text-warning"><?= esc($d['nome_fornecedor'] ?: 'Despesa') ?></span>
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4">
                            
                            <!-- CARD: Estado Atual do Registro -->
                            <div class="card border-left-success shadow-sm mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="m-0 font-weight-bold text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Estado Atual da Despesa (Tabela Principal)
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">FORNECEDOR</small>
                                            <span class="font-weight-bold text-dark"><?= esc($d['nome_fornecedor'] ?: '-') ?></span>
                                            <small class="text-muted d-block"><?= esc($d['cnpj_fornecedor'] ?: '-') ?></small>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <small class="text-muted font-weight-bold d-block">DOCUMENTO / NOTA</small>
                                            <span class="badge badge-dark px-2 py-1"><?= esc($d['numero_nota'] ?: 'S/N') ?></span>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <small class="text-muted font-weight-bold d-block">DATA EMISSÃO</small>
                                            <span class="text-dark"><?= formatar_data($d['data_emissao']) ?></span>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <small class="text-muted font-weight-bold d-block">VALOR TOTAL</small>
                                            <span class="text-danger font-weight-bold">R$ <?= number_format($d['valor_total'], 2, ',', '.') ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">PROJETO</small>
                                            <span class="text-dark"><i class="fas fa-project-diagram mr-1 text-muted"></i><?= esc($nomeProj) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">RUBRICA</small>
                                            <span class="text-dark"><i class="fas fa-wallet mr-1 text-muted"></i><?= esc($nomeRubr) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">STATUS DE APROVAÇÃO</small>
                                            <span class="badge <?= $badgeStatus ?>"><?= esc($d['status_aprovacao']) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">ÚLTIMA ALTERAÇÃO</small>
                                            <?php if (!empty($d['_atualizado_em'])): ?>
                                                <span class="text-dark font-weight-bold">
                                                    <i class="fas fa-clock mr-1 text-muted"></i><?= formatar_data_hora($d['_atualizado_em']) ?>
                                                    <small class="text-muted ml-2"><i class="fas fa-user-edit mr-1"></i>Por: <?= esc($d['_atualizado_por'] ?? 'sistema') ?></small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Nenhuma alteração realizada</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($d['descricao_itens'])): ?>
                                            <div class="col-md-12 mb-2 mt-1">
                                                <small class="text-muted font-weight-bold d-block">DESCRIÇÃO DOS ITENS / JUSTIFICATIVA</small>
                                                <div class="p-2 bg-light rounded text-dark small"><?= nl2br(esc($d['descricao_itens'])) ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO: Versões Anteriores (Shadow Table despesas_historico ordenada por _atualizado_em DESC) -->
                            <h6 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-layer-group text-info mr-1"></i> Linha do Tempo de Modificações Anteriores (Trilha de Auditoria)
                            </h6>

                            <?php if (empty($historicoDespesa)): ?>
                                <div class="alert alert-light border text-center py-4">
                                    <i class="fas fa-info-circle text-info fa-2x mb-2 d-block"></i>
                                    <strong>Nenhuma alteração anterior registrada.</strong>
                                    <p class="text-muted small mb-0 mt-1">Este registro ainda não possui revisões históricas no banco de dados.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover shadow-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center" style="width: 70px;">Rev #</th>
                                                <th class="text-center" style="width: 110px;">Operação</th>
                                                <th style="width: 170px;">Data/Hora da Alteração</th>
                                                <th style="width: 160px;">Alterado Por</th>
                                                <th>Dados Gravados na Versão</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($historicoDespesa as $h): ?>
                                                <?php
                                                    $badgeClass = match($h['_operacao'] ?? '') {
                                                        'UPDATE' => 'badge-warning text-dark',
                                                        'DELETE' => 'badge-danger',
                                                        'INSERT' => 'badge-success',
                                                        default  => 'badge-info'
                                                    };
                                                    $dataOp = $h['_atualizado_em'] ?? $h['_deletado_em'] ?? $h['_criado_em'] ?? null;
                                                    $dataOpFormatada = formatar_data_hora($dataOp);
                                                    $usuarioOp = $h['_atualizado_por'] ?? $h['_deletado_por'] ?? $h['_criado_por'] ?? 'sistema';
                                                    $valorHist = isset($h['valor_total']) ? ('R$ ' . number_format($h['valor_total'], 2, ',', '.')) : '-';
                                                    $dataEmissaoHist = formatar_data($h['data_emissao'] ?? null);
                                                    $projHist = $projetosMap[$h['id_projeto']] ?? ('Projeto ID #' . ($h['id_projeto'] ?? '-'));
                                                    $rubrHist = $rubricasMap[$h['id_rubrica']] ?? ('Rubrica ID #' . ($h['id_rubrica'] ?? '-'));
                                                ?>
                                                <tr>
                                                    <td class="text-center font-weight-bold text-muted">#<?= $h['id_historico'] ?></td>
                                                    <td class="text-center"><span class="badge <?= $badgeClass ?> px-2 py-1"><?= esc($h['_operacao'] ?? 'UPDATE') ?></span></td>
                                                    <td class="small"><?= $dataOpFormatada ?></td>
                                                    <td class="small font-weight-bold text-gray-700">
                                                        <i class="fas fa-user-circle mr-1"></i><?= esc($usuarioOp) ?>
                                                    </td>
                                                    <td class="small">
                                                        <strong>Fornecedor:</strong> <?= esc($h['nome_fornecedor'] ?? '-') ?> (CNPJ: <?= esc($h['cnpj_fornecedor'] ?? '-') ?>)<br>
                                                        <strong>Doc / NF:</strong> <?= esc($h['numero_nota'] ?? 'S/N') ?> &bull; <strong>Data Emissão:</strong> <?= $dataEmissaoHist ?> &bull; <strong>Valor:</strong> <span class="text-danger font-weight-bold"><?= $valorHist ?></span><br>
                                                        <strong>Status:</strong> <?= esc($h['status_aprovacao'] ?? '-') ?> &bull; <strong>Projeto:</strong> <?= esc($projHist) ?> &bull; <strong>Rubrica:</strong> <?= esc($rubrHist) ?>
                                                        <?php if (!empty($h['descricao_itens'])): ?>
                                                            <br><strong>Descrição:</strong> <?= esc($h['descricao_itens']) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.btn-status').on('click', function() {
            var idDespesa = $(this).data('iddespesa');
            var statusAtual = $(this).data('statusatual');
            
            // Atualiza a URL do formulário dinamicamente
            $('#formMudarStatus').attr('action', '<?= base_url('despesas/mudar-status') ?>/' + idDespesa);
            
            // Seleciona o status atual no dropdown
            $('#status_novo').val(statusAtual);
        });
    });
</script>
<?= $this->endSection() ?>