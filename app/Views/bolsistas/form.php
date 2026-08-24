<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <h2><?= esc($titulo) ?></h2>
    <hr>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php 
        $isEdit = isset($bolsista);
        $action = $isEdit ? site_url('bolsistas/atualizar/' . $bolsista['id_bolsista']) : site_url('bolsistas/salvar');
    ?>

    <form action="<?= $action ?>" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome Completo *</label>
                <input type="text" name="nome" class="form-control" value="<?= old('nome', $bolsista['nome'] ?? '') ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="cpf" class="form-label">CPF *</label>
                <input type="text" name="cpf" class="form-control" value="<?= old('cpf', $bolsista['cpf'] ?? '') ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= old('telefone', $bolsista['telefone'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" name="email" class="form-control" value="<?= old('email', $bolsista['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-2 mb-3">
                <label for="banco" class="form-label">Banco</label>
                <input type="text" name="banco" class="form-control" value="<?= old('banco', $bolsista['banco'] ?? '') ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label for="agencia" class="form-label">Agência</label>
                <input type="text" name="agencia" class="form-control" value="<?= old('agencia', $bolsista['agencia'] ?? '') ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label for="conta_corrente" class="form-label">Conta Corrente</label>
                <input type="text" name="conta_corrente" class="form-control" value="<?= old('conta_corrente', $bolsista['conta_corrente'] ?? '') ?>">
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="<?= site_url('bolsistas') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

<?= $this->endSection() ?>