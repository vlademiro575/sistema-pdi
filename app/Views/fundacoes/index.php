<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Fundações</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listagem de Fundações</h2>
        <a href="<?= site_url('fundacoes/nova') ?>" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus mr-1"></i> Nova Fundação
        </a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CNPJ</th>
                <th>Sigla</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($fundacoes)): ?>
                <?php foreach ($fundacoes as $f): ?>
                    <tr>
                        <td><?= $f['id_fundacao'] ?></td>
                        <td><?= esc($f['nome']) ?></td>
                        <td><?= esc($f['cnpj']) ?></td>
                        <td><?= esc($f['sigla']) ?></td>
                        <td>
                            <a href="<?= site_url('fundacoes/editar/' . $f['id_fundacao']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="<?= site_url('fundacoes/deletar/' . $f['id_fundacao']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta fundação?')">Excluir</a>
                            <button type="button" class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#modalHistorico<?= $f['id_fundacao'] ?>">
                                <i class="fas fa-history mr-1"></i> Histórico
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Nenhum registro de fundação encontrado.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- MODAIS DE HISTÓRICO (Renderizados a partir de fundacoes_historico) -->
    <?php if (!empty($fundacoes)): ?>
        <?php foreach ($fundacoes as $f): ?>
            <?php 
                $historicoFundacao = $historicosPorFundacao[$f['id_fundacao']] ?? [];
                $tipoFormatado = match($f['tipo'] ?? '') {
                    'FUNDACAO_APOIO' => 'Fundação de Apoio',
                    'FAP_ESTADUAL'   => 'FAP Estadual',
                    'ORGAO_FEDERAL'  => 'Órgão Federal',
                    default          => $f['tipo'] ?? '-'
                };
            ?>
            <div class="modal fade" id="modalHistorico<?= $f['id_fundacao'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHistoricoLabel<?= $f['id_fundacao'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title font-weight-bold" id="modalHistoricoLabel<?= $f['id_fundacao'] ?>">
                                <i class="fas fa-history mr-2"></i> Histórico de Alterações: <span class="text-warning"><?= esc($f['nome']) ?></span>
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
                                        <i class="fas fa-check-circle mr-1"></i> Estado Atual da Fundação (Tabela Principal)
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">RAZÃO SOCIAL / NOME</small>
                                            <span class="font-weight-bold text-dark"><?= esc($f['nome']) ?></span>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <small class="text-muted font-weight-bold d-block">SIGLA</small>
                                            <span class="badge badge-secondary px-2 py-1"><?= esc($f['sigla']) ?></span>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <small class="text-muted font-weight-bold d-block">CNPJ</small>
                                            <span class="text-dark"><?= esc($f['cnpj'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <small class="text-muted font-weight-bold d-block">TIPO</small>
                                            <span class="text-dark"><?= esc($tipoFormatado) ?></span>
                                        </div>
                                        <div class="col-md-12 mb-2 mt-2">
                                            <small class="text-muted font-weight-bold d-block">ÚLTIMA ALTERAÇÃO</small>
                                            <?php if (!empty($f['_atualizado_em'])): ?>
                                                <span class="text-dark font-weight-bold">
                                                    <i class="fas fa-clock mr-1 text-muted"></i><?= formatar_data_hora($f['_atualizado_em']) ?>
                                                    <small class="text-muted ml-2"><i class="fas fa-user-edit mr-1"></i>Por: <?= esc($f['_atualizado_por'] ?? 'sistema') ?></small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Nenhuma alteração realizada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO: Versões Anteriores (Shadow Table fundacoes_historico ordenada por _atualizado_em DESC) -->
                            <h6 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-layer-group text-info mr-1"></i> Linha do Tempo de Modificações Anteriores (Trilha de Auditoria)
                            </h6>

                            <?php if (empty($historicoFundacao)): ?>
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
                                            <?php foreach ($historicoFundacao as $h): ?>
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
                                                    $tipoHist = match($h['tipo'] ?? '') {
                                                        'FUNDACAO_APOIO' => 'Fundação de Apoio',
                                                        'FAP_ESTADUAL'   => 'FAP Estadual',
                                                        'ORGAO_FEDERAL'  => 'Órgão Federal',
                                                        default          => $h['tipo'] ?? '-'
                                                    };
                                                ?>
                                                <tr>
                                                    <td class="text-center font-weight-bold text-muted">#<?= $h['id_historico'] ?></td>
                                                    <td class="text-center"><span class="badge <?= $badgeClass ?> px-2 py-1"><?= esc($h['_operacao'] ?? 'UPDATE') ?></span></td>
                                                    <td class="small"><?= $dataOpFormatada ?></td>
                                                    <td class="small font-weight-bold text-gray-700">
                                                        <i class="fas fa-user-circle mr-1"></i><?= esc($usuarioOp) ?>
                                                    </td>
                                                    <td class="small">
                                                        <strong>Nome:</strong> <?= esc($h['nome'] ?? '-') ?><br>
                                                        <strong>Sigla:</strong> <?= esc($h['sigla'] ?? '-') ?> &bull; <strong>CNPJ:</strong> <?= esc($h['cnpj'] ?? '-') ?><br>
                                                        <strong>Tipo:</strong> <?= esc($tipoHist) ?>
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