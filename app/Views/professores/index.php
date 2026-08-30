<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Professores</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listagem de Professores</h2>
        <a href="<?= site_url('professores/novo') ?>" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus mr-1"></i> Novo Professor
        </a>
    </div>

    <!-- Filtro de Busca -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="get" action="<?= site_url('professores') ?>" class="form-inline">
                <div class="input-group mr-2 my-1">
                    <input type="text" name="nome" class="form-control" placeholder="Buscar por nome do professor..." 
                           value="<?= esc($termo ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm mr-1"></i> Filtrar
                        </button>
                    </div>
                </div>

                <?php if (!empty($termo)): ?>
                    <a href="<?= site_url('professores') ?>" class="btn btn-outline-secondary my-1">
                        <i class="fas fa-times fa-sm mr-1"></i> Limpar Filtro
                    </a>
                    <span class="ml-3 text-muted small">
                        Filtrando por: <strong>"<?= esc($termo) ?>"</strong>
                    </span>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>SIAPE</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($professores)): ?>
                <?php foreach ($professores as $p): ?>
                    <tr>
                        <td><?= $p['id_professor'] ?></td>
                        <td><?= esc($p['nome']) ?></td>
                        <td><?= esc($p['cpf']) ?></td>
                        <td><?= esc($p['email']) ?></td>
                        <td><?= esc($p['telefone']) ?></td>
                        <td><?= esc($p['siape']) ?></td>
                        <td>
                            <a href="<?= site_url('professores/editar/' . $p['id_professor']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="<?= site_url('professores/deletar/' . $p['id_professor']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este professor?')">Excluir</a>
                            <button type="button" class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#modalHistorico<?= $p['id_professor'] ?>">
                                <i class="fas fa-history mr-1"></i> Histórico
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        <?= !empty($termo) ? 'Nenhum professor encontrado com o termo "' . esc($termo) . '".' : 'Nenhum professor encontrado.' ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- MODAIS DE HISTÓRICO (Renderizados a partir de professores_historico) -->
    <?php if (!empty($professores)): ?>
        <?php foreach ($professores as $p): ?>
            <?php 
                $historicoProfessor = $historicosPorProfessor[$p['id_professor']] ?? [];
            ?>
            <div class="modal fade" id="modalHistorico<?= $p['id_professor'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHistoricoLabel<?= $p['id_professor'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title font-weight-bold" id="modalHistoricoLabel<?= $p['id_professor'] ?>">
                                <i class="fas fa-history mr-2"></i> Histórico de Alterações: <span class="text-warning"><?= esc($p['nome']) ?></span>
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
                                        <i class="fas fa-check-circle mr-1"></i> Estado Atual do Professor (Tabela Principal)
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">NOME COMPLETO</small>
                                            <span class="font-weight-bold text-dark"><?= esc($p['nome']) ?></span>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">CPF</small>
                                            <span class="text-dark"><?= esc($p['cpf']) ?></span>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">E-MAIL</small>
                                            <span class="text-dark"><?= esc($p['email']) ?></span>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">TELEFONE</small>
                                            <span class="text-dark"><?= esc($p['telefone'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">SIAPE</small>
                                            <span class="text-dark"><?= esc($p['siape'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted font-weight-bold d-block">ÚLTIMA ALTERAÇÃO</small>
                                            <?php if (!empty($p['_atualizado_em'])): ?>
                                                <span class="text-dark font-weight-bold">
                                                    <i class="fas fa-clock mr-1 text-muted"></i><?= date('d/m/Y \à\s H:i:s', strtotime($p['_atualizado_em'])) ?>
                                                    <br><small class="text-muted"><i class="fas fa-user-edit mr-1"></i>Por: <?= esc($p['_atualizado_por'] ?? 'sistema') ?></small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Nenhuma alteração realizada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO: Versões Anteriores (Shadow Table professores_historico ordenada por _atualizado_em DESC) -->
                            <h6 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-layer-group text-info mr-1"></i> Linha do Tempo de Modificações Anteriores (Trilha de Auditoria)
                            </h6>

                            <?php if (empty($historicoProfessor)): ?>
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
                                            <?php foreach ($historicoProfessor as $h): ?>
                                                <?php
                                                    $badgeClass = match($h['_operacao'] ?? '') {
                                                        'UPDATE' => 'badge-warning text-dark',
                                                        'DELETE' => 'badge-danger',
                                                        'INSERT' => 'badge-success',
                                                        default  => 'badge-info'
                                                    };
                                                    $dataOp = $h['_atualizado_em'] ?? $h['_deletado_em'] ?? $h['_criado_em'] ?? null;
                                                    $dataOpFormatada = $dataOp ? date('d/m/Y \à\s H:i:s', strtotime($dataOp)) : '-';
                                                    $usuarioOp = $h['_atualizado_por'] ?? $h['_deletado_por'] ?? $h['_criado_por'] ?? 'sistema';
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
                                                        <strong>CPF:</strong> <?= esc($h['cpf'] ?? '-') ?> &bull; <strong>E-mail:</strong> <?= esc($h['email'] ?? '-') ?><br>
                                                        <strong>Telefone:</strong> <?= esc($h['telefone'] ?? '-') ?><br>
                                                        <strong>SIAPE:</strong> <?= esc($h['siape'] ?? '-') ?>
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