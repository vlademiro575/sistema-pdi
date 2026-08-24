<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Bolsistas</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listagem de Bolsistas</h2>
        <a href="<?= site_url('bolsistas/novo') ?>" class="btn btn-primary">Novo Bolsista</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Banco/Ag/Conta</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bolsistas)): ?>
                <?php foreach ($bolsistas as $b): ?>
                    <tr>
                        <td><?= $b['id_bolsista'] ?></td>
                        <td><?= esc($b['nome']) ?></td>
                        <td><?= esc($b['cpf']) ?></td>
                        <td><?= esc($b['email']) ?></td>
                        <td><?= esc($b['telefone']) ?></td>
                        <td><?= esc($b['banco']) ?> / <?= esc($b['agencia']) ?> / <?= esc($b['conta_corrente']) ?></td>
                        <td>
                            <a href="<?= site_url('bolsistas/editar/' . $b['id_bolsista']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="<?= site_url('bolsistas/deletar/' . $b['id_bolsista']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este bolsista?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Nenhum bolsista encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?= $this->endSection() ?>