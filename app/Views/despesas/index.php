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
                            <th class="text-center" style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($despesas) && is_array($despesas)): ?>
                            <?php foreach ($despesas as $d): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($d['data_emissao'])) ?></td>
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