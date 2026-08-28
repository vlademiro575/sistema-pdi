<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Usuários</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gerenciamento de Usuários</h1>
        <a href="<?= site_url('usuarios/novo') ?>" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user-plus fa-sm mr-1"></i> Novo Usuário
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Usuários Cadastrados no Sistema</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-items-center" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Nome</th>
                            <th>Login</th>
                            <th>E-mail</th>
                            <th class="text-center">Perfil</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios) && is_array($usuarios)): ?>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= $u['id_usuario'] ?></td>
                                    <td class="font-weight-bold text-dark"><?= esc($u['nome']) ?></td>
                                    <td><code><?= esc($u['login']) ?></code></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $badgePerfil = match($u['perfil']) {
                                                'ADMIN'      => 'badge-danger',
                                                'PROFESSOR'  => 'badge-primary',
                                                'BOLSISTA'   => 'badge-info',
                                                'SECRETARIO' => 'badge-dark',
                                                default      => 'badge-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $badgePerfil ?> px-2 py-1"><?= esc($u['perfil']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ((int)$u['ativo'] === 1): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1"><i class="fas fa-ban mr-1"></i>Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('usuarios/editar/' . $u['id_usuario']) ?>" 
                                           class="btn btn-sm btn-info btn-circle shadow-sm" title="Editar Usuário">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <?php if ((int)$u['id_usuario'] !== (int)session()->get('id_usuario')): ?>
                                            <a href="<?= site_url('usuarios/deletar/' . $u['id_usuario']) ?>" 
                                               class="btn btn-sm btn-danger btn-circle shadow-sm" 
                                               onclick="return confirm('Tem certeza que deseja excluir o usuário <?= esc($u['nome']) ?>? Esta ação ficará registrada na auditoria.');" 
                                               title="Excluir Usuário">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary btn-circle" disabled title="Você não pode excluir seu próprio usuário">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Nenhum usuário cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

