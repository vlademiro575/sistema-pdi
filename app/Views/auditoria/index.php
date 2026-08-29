<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Auditoria & Pendências</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-shield-alt text-primary mr-2"></i>Verificação de Pendências e Auditoria
            </h1>
            <p class="text-muted mb-0 small">
                Diagnóstico automatizado de segurança financeira, conformidade orçamentária e integridade de prestações de contas (Malha Fina PDI).
            </p>
        </div>
        <a href="<?= base_url('auditoria') ?>" class="btn btn-sm btn-outline-primary shadow-sm mt-2 mt-sm-0">
            <i class="fas fa-sync-alt fa-sm mr-1"></i> Reexecutar Verificação
        </a>
    </div>

    <!-- CARDS DE RESUMO (KPIS DE AUDITORIA) -->
    <div class="row">
        <!-- Total de Projetos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Projetos Auditados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $resumo['total_projetos'] ?>
                            </div>
                            <div class="small text-muted mt-1">
                                <?= $resumo['total_projetos_regulares'] ?> em conformidade total
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Erros Críticos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Inconsistências Críticas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-danger">
                                <?= $resumo['total_erros'] ?>
                            </div>
                            <div class="small text-muted mt-1">
                                Violações de regras financeiras
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-danger opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avisos e Alertas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Alertas & Atenção
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-warning">
                                <?= $resumo['total_avisos'] ?>
                            </div>
                            <div class="small text-muted mt-1">
                                Pendências administrativas
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taxa de Conformidade -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Índice de Regularidade
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-success">
                                        <?= $resumo['percentual_conformidade'] ?>%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= $resumo['percentual_conformidade'] ?>%" 
                                             aria-valuenow="<?= $resumo['percentual_conformidade'] ?>" aria-valuemin="0" aria-valuemew="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATUS GERAL -->
    <?php if ($resumo['total_erros'] === 0 && $resumo['total_avisos'] === 0): ?>
        <div class="alert alert-success shadow-sm p-4 text-center my-4" role="alert">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h4 class="alert-heading font-weight-bold">Parabéns! Nenhum erro ou pendência encontrada.</h4>
            <p class="mb-0">
                Todos os orçamentos, rubricas, despesas e vigências de bolsas dos projetos estão consistentes e em total conformidade com as regras institucionais.
            </p>
        </div>
    <?php else: ?>
        <div class="alert alert-light border shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-info-circle text-info mr-2"></i>
                Foram identificados <strong><?= $resumo['total_erros'] ?> erro(s) crítico(s)</strong> e <strong><?= $resumo['total_avisos'] ?> alerta(s) de atenção</strong>.
                Revise os apontamentos abaixo e utilize os botões de ação para regularizar os lançamentos.
            </div>
        </div>
    <?php endif; ?>

    <!-- LISTA DETALHADA DE PENDÊNCIAS POR PROJETO -->
    <?php if (!empty($pendencias_por_projeto)): ?>
        <?php foreach ($pendencias_por_projeto as $nomeProjeto => $listaPendencias): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-light">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-folder-open text-primary mr-2"></i><?= esc($nomeProjeto) ?>
                    </h6>
                    <div>
                        <?php 
                            $qtdErros = count(array_filter($listaPendencias, fn($p) => $p['tipo'] === 'ERRO'));
                            $qtdAvisos = count(array_filter($listaPendencias, fn($p) => $p['tipo'] === 'AVISO'));
                            $qtdInfo = count(array_filter($listaPendencias, fn($p) => $p['tipo'] === 'INFO'));
                        ?>
                        <?php if ($qtdErros > 0): ?>
                            <span class="badge badge-danger mr-1"><?= $qtdErros ?> Inconsistência(s)</span>
                        <?php endif; ?>
                        <?php if ($qtdAvisos > 0): ?>
                            <span class="badge badge-warning text-dark mr-1"><?= $qtdAvisos ?> Alerta(s)</span>
                        <?php endif; ?>
                        <?php if ($qtdInfo > 0): ?>
                            <span class="badge badge-info"><?= $qtdInfo ?> Informativo(s)</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 110px;" class="text-center">Gravidade</th>
                                    <th style="width: 180px;">Categoria</th>
                                    <th>Regra / Diagnóstico da Inconsistência</th>
                                    <th class="text-center" style="width: 170px;">Ação Recomendada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listaPendencias as $item): ?>
                                    <?php 
                                        $badgeClass = match($item['tipo']) {
                                            'ERRO'  => 'badge-danger',
                                            'AVISO' => 'badge-warning text-dark',
                                            'INFO'  => 'badge-info',
                                            default => 'badge-secondary'
                                        };
                                        $icone = match($item['tipo']) {
                                            'ERRO'  => 'fa-times-circle',
                                            'AVISO' => 'fa-exclamation-triangle',
                                            'INFO'  => 'fa-info-circle',
                                            default => 'fa-circle'
                                        };
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <span class="badge <?= $badgeClass ?> px-2 py-1">
                                                <i class="fas <?= $icone ?> mr-1"></i><?= $item['tipo'] ?>
                                            </span>
                                        </td>
                                        <td class="align-middle font-weight-bold text-gray-700">
                                            <?= esc($item['categoria']) ?>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-dark mb-1">
                                                <?= esc($item['regra']) ?>
                                            </div>
                                            <div class="text-muted small">
                                                <?= esc($item['mensagem']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="<?= $item['acao_url'] ?>" class="btn btn-sm btn-outline-primary btn-block shadow-sm">
                                                <i class="fas fa-external-link-alt fa-sm mr-1"></i> <?= esc($item['acao_texto']) ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?= $this->endSection() ?>

