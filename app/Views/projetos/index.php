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
                            <th class="text-center" style="width: 180px;">Ações</th>
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

<?= $this->endSection() ?>