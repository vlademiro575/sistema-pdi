<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Extrato da Rubrica <span class="text-primary">#<?= esc($rubrica['id_rubrica']) ?></span>
        </h1>
        <a href="<?= base_url('projetos/gerenciar/' . $rubrica['id_projeto']) ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar ao Projeto
        </a>
    </div>

    <!-- Tabela de Leitura Rigorosa -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Histórico de Movimentações</h6>
            <span class="h5 m-0 font-weight-bold text-success">Saldo Atual: R$ <?= number_format($rubrica['saldo_disponivel'], 2, ',', '.') ?></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th class="text-right">Valor Movimentado</th>
                            <th class="text-right">Saldo Anterior</th>
                            <th class="text-right">Saldo Posterior</th>
                            <th>Auditoria (Usuário)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimentacoes) && is_array($movimentacoes)): ?>
                            <?php foreach ($movimentacoes as $mov): ?>
                                <tr>
                                    <td><?= formatar_data_hora($mov['_criado_em']) ?></td>
                                    <td>
                                        <?php 
                                            $badge = match($mov['tipo']) {
                                                'DESPESA' => 'badge-danger',
                                                'ESTORNO' => 'badge-success',
                                                'AJUSTE'  => 'badge-warning text-dark',
                                                'TRANSFERENCIA' => 'badge-info',
                                                default => 'badge-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= esc($mov['tipo']) ?></span>
                                    </td>
                                    <td>
                                        <?= esc($mov['descricao']) ?>
                                        <?php if($mov['id_despesa']): ?>
                                            <br><small class="text-muted">Ref. Despesa #<?= $mov['id_despesa'] ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right font-weight-bold <?= $mov['valor'] < 0 || $mov['tipo'] == 'DESPESA' ? 'text-danger' : 'text-success' ?>">
                                        R$ <?= number_format($mov['valor'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-right text-muted">R$ <?= number_format($mov['saldo_anterior'], 2, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold text-dark">R$ <?= number_format($mov['saldo_posterior'], 2, ',', '.') ?></td>
                                    <td class="small"><?= esc($mov['_criado_por']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Nenhuma movimentação registrada nesta rubrica.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>