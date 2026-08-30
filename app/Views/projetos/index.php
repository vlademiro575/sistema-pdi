<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Projetos</li>
        </ol>
    </nav>

    <!-- Cabeçalho e Botão de Ação -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Projetos de PDI</h1>
        <a href="<?= base_url('projetos/novo') ?>" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm mr-1"></i> Novo Projeto
        </a>
    </div>

    <!-- Feedbacks do Sistema -->
    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('sucesso') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger shadow-sm">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('erro') ?>
        </div>
    <?php endif; ?>

    <!-- Tabela de Listagem -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listagem Geral de Projetos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-items-center" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID / Código</th>
                            <th>Título do Projeto</th>
                            <th class="text-right">Orçamento (R$)</th>
                            <th class="text-center">Vigência</th>
                            <th class="text-center" style="width: 190px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($projetos) && is_array($projetos)): ?>
                            <?php foreach ($projetos as $proj): ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-dark">#<?= $proj['id_projeto'] ?></span><br>
                                        <small class="text-muted"><?= esc($proj['codigo_projeto_fundacao']) ?></small>
                                    </td>
                                    <td class="font-weight-bold text-gray-800"><?= esc($proj['titulo']) ?></td>
                                    <td class="text-right text-success font-weight-bold">
                                        <?= number_format($proj['orcamento_total'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-center text-sm">
                                        <?= date('d/m/Y', strtotime($proj['data_inicio'])) ?> a<br>
                                        <?= date('d/m/Y', strtotime($proj['data_fim'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- O Botão Principal do Mestre-Detalhe -->
                                        <a href="<?= base_url('projetos/gerenciar/' . $proj['id_projeto']) ?>" 
                                           class="btn btn-sm btn-success shadow-sm mb-1" title="Gerenciar Rubricas e Equipe">
                                            <i class="fas fa-cogs mr-1"></i> Painel
                                        </a>
                                        <br>
                                        <!-- Botões Secundários -->
                                        <a href="<?= base_url('projetos/editar/' . $proj['id_projeto']) ?>" 
                                           class="btn btn-sm btn-info btn-circle shadow-sm" title="Editar Dados Mestre">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="<?= base_url('projetos/delete/' . $proj['id_projeto']) ?>" 
                                           class="btn btn-sm btn-danger btn-circle shadow-sm" 
                                           onclick="return confirm('ATENÇÃO: Deseja realmente remover este projeto?');" 
                                           title="Excluir Projeto">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-secondary btn-circle shadow-sm" 
                                                data-toggle="modal" data-target="#modalHistorico<?= $proj['id_projeto'] ?>" 
                                                title="Histórico de Alterações">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i> Nenhum projeto de PDI cadastrado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAIS DE HISTÓRICO (Renderizados a partir de projetos_historico) -->
    <?php if (!empty($projetos) && is_array($projetos)): ?>
        <?php foreach ($projetos as $proj): ?>
            <?php 
                $historicoProjeto = $historicosPorProjeto[$proj['id_projeto']] ?? [];
                $nomeProfessor = $professoresMap[$proj['id_professor']] ?? ('Professor ID #' . $proj['id_professor']);
                $nomeFundacao  = $fundacoesMap[$proj['id_fundacao']] ?? ('Fundação ID #' . $proj['id_fundacao']);
            ?>
            <div class="modal fade" id="modalHistorico<?= $proj['id_projeto'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHistoricoLabel<?= $proj['id_projeto'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title font-weight-bold" id="modalHistoricoLabel<?= $proj['id_projeto'] ?>">
                                <i class="fas fa-history mr-2"></i> Histórico de Alterações: <span class="text-warning"><?= esc($proj['titulo']) ?></span>
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
                                        <i class="fas fa-check-circle mr-1"></i> Estado Atual do Projeto (Tabela Principal)
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">TÍTULO DO PROJETO</small>
                                            <span class="font-weight-bold text-dark"><?= esc($proj['titulo']) ?></span>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <small class="text-muted font-weight-bold d-block">CÓDIGO NA FUNDAÇÃO</small>
                                            <span class="badge badge-dark px-2 py-1"><?= esc($proj['codigo_projeto_fundacao']) ?></span>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <small class="text-muted font-weight-bold d-block">ORÇAMENTO TOTAL</small>
                                            <span class="text-success font-weight-bold">R$ <?= number_format($proj['orcamento_total'], 2, ',', '.') ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">COORDENADOR RESPONSÁVEL</small>
                                            <span class="text-dark"><i class="fas fa-user-tie mr-1 text-muted"></i><?= esc($nomeProfessor) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">FUNDAÇÃO DE APOIO</small>
                                            <span class="text-dark"><i class="fas fa-university mr-1 text-muted"></i><?= esc($nomeFundacao) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">VIGÊNCIA</small>
                                            <span class="text-dark"><i class="fas fa-calendar-alt mr-1 text-muted"></i><?= date('d/m/Y', strtotime($proj['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($proj['data_fim'])) ?></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted font-weight-bold d-block">ÚLTIMA ALTERAÇÃO</small>
                                            <?php if (!empty($proj['_atualizado_em'])): ?>
                                                <span class="text-dark font-weight-bold">
                                                    <i class="fas fa-clock mr-1 text-muted"></i><?= date('d/m/Y \à\s H:i:s', strtotime($proj['_atualizado_em'])) ?>
                                                    <small class="text-muted ml-2"><i class="fas fa-user-edit mr-1"></i>Por: <?= esc($proj['_atualizado_por'] ?? 'sistema') ?></small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Nenhuma alteração realizada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO: Versões Anteriores (Shadow Table projetos_historico ordenada por _atualizado_em DESC) -->
                            <h6 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-layer-group text-info mr-1"></i> Linha do Tempo de Modificações Anteriores (Trilha de Auditoria)
                            </h6>

                            <?php if (empty($historicoProjeto)): ?>
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
                                            <?php foreach ($historicoProjeto as $h): ?>
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
                                                    $coordHist = $professoresMap[$h['id_professor']] ?? ('Professor ID #' . ($h['id_professor'] ?? '-'));
                                                    $fundHist  = $fundacoesMap[$h['id_fundacao']] ?? ('Fundação ID #' . ($h['id_fundacao'] ?? '-'));
                                                    $orcamentoHist = isset($h['orcamento_total']) ? ('R$ ' . number_format($h['orcamento_total'], 2, ',', '.')) : '-';
                                                    $vigenciaHist = ($h['data_inicio'] && $h['data_fim']) ? (date('d/m/Y', strtotime($h['data_inicio'])) . ' a ' . date('d/m/Y', strtotime($h['data_fim']))) : '-';
                                                ?>
                                                <tr>
                                                    <td class="text-center font-weight-bold text-muted">#<?= $h['id_historico'] ?></td>
                                                    <td class="text-center"><span class="badge <?= $badgeClass ?> px-2 py-1"><?= esc($h['_operacao'] ?? 'UPDATE') ?></span></td>
                                                    <td class="small"><?= $dataOpFormatada ?></td>
                                                    <td class="small font-weight-bold text-gray-700">
                                                        <i class="fas fa-user-circle mr-1"></i><?= esc($usuarioOp) ?>
                                                    </td>
                                                    <td class="small">
                                                        <strong>Título:</strong> <?= esc($h['titulo'] ?? '-') ?><br>
                                                        <strong>Código:</strong> <?= esc($h['codigo_projeto_fundacao'] ?? '-') ?> &bull; <strong>Orçamento:</strong> <span class="text-success font-weight-bold"><?= $orcamentoHist ?></span><br>
                                                        <strong>Coordenador:</strong> <?= esc($coordHist) ?> &bull; <strong>Fundação:</strong> <?= esc($fundHist) ?><br>
                                                        <strong>Vigência:</strong> <?= $vigenciaHist ?>
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