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
        <a href="<?= site_url('fundacoes/nova') ?>" class="btn btn-primary">Nova Fundação</a>
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
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhuma fundação encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?= $this->endSection() ?>