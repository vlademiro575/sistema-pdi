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
        <a href="<?= site_url('professores/novo') ?>" class="btn btn-primary">Novo Professor</a>
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
                    <td colspan="7" class="text-center">Nenhum professor encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?= $this->endSection() ?>