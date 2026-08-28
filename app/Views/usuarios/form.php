<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('usuarios') ?>">Usuários</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= isset($usuario) ? 'Editar Usuário' : 'Novo Usuário' ?>
            </li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($titulo) ?></h1>
        <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar para a Lista
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Corrija os erros abaixo:</h6>
            <ul class="mb-0 pl-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php 
        $isEdit = isset($usuario) && !empty($usuario);
        $action = $isEdit ? site_url('usuarios/atualizar/' . $usuario['id_usuario']) : site_url('usuarios/salvar');
    ?>

    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= $isEdit ? 'Atualizar Dados do Usuário #' . $usuario['id_usuario'] : 'Dados do Novo Usuário' ?>
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nome" class="font-weight-bold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome" class="form-control" 
                               placeholder="Ex: Carlos Eduardo da Silva" 
                               value="<?= old('nome', $usuario['nome'] ?? '') ?>" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="email" class="font-weight-bold">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Ex: carlos@universidade.br" 
                               value="<?= old('email', $usuario['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="login" class="font-weight-bold">Login de Acesso <span class="text-danger">*</span></label>
                        <input type="text" name="login" id="login" class="form-control" 
                               placeholder="Ex: carloseduardo" 
                               value="<?= old('login', $usuario['login'] ?? '') ?>" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="senha" class="font-weight-bold">
                            Senha <?= $isEdit ? '<small class="text-muted font-weight-normal">(Deixe em branco para manter)</small>' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" name="senha" id="senha" class="form-control" 
                               placeholder="<?= $isEdit ? 'Nova senha (opcional)' : 'Senha de acesso (mínimo 6 caracteres)' ?>" 
                               <?= $isEdit ? '' : 'required' ?>>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="perfil" class="font-weight-bold">Perfil de Acesso <span class="text-danger">*</span></label>
                        <select name="perfil" id="perfil" class="form-control" required>
                            <option value="" disabled <?= empty($usuario['perfil']) ? 'selected' : '' ?>>Selecione o perfil...</option>
                            <?php foreach ($perfis as $p): ?>
                                <option value="<?= $p ?>" 
                                    <?= set_select('perfil', $p, (isset($usuario['perfil']) && $usuario['perfil'] === $p)) ?>>
                                    <?= esc($p) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="ativo" class="font-weight-bold">Status do Usuário</label>
                        <select name="ativo" id="ativo" class="form-control">
                            <option value="1" <?= set_select('ativo', '1', (!isset($usuario['ativo']) || (int)$usuario['ativo'] === 1)) ?>>Ativo</option>
                            <option value="0" <?= set_select('ativo', '0', (isset($usuario['ativo']) && (int)$usuario['ativo'] === 0)) ?>>Inativo (Bloqueado)</option>
                        </select>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> <?= $isEdit ? 'Salvar Alterações' : 'Cadastrar Usuário' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>

