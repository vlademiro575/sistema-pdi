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

<?= $this->endSection() ?>