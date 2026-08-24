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
        $isEdit = isset($professor) && !empty($professor);
        $action = $isEdit ? site_url('professores/atualizar/' . $professor['id_professor']) : site_url('professores/salvar');
    ?>

    <form action="<?= $action ?>" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome Completo *</label>
                <input type="text" name="nome" class="form-control" value="<?= old('nome', $professor['nome'] ?? '') ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="cpf" class="form-label">CPF *</label>
                <input type="text" name="cpf" class="form-control" value="<?= old('cpf', $professor['cpf'] ?? '') ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= old('telefone', $professor['telefone'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" name="email" class="form-control" value="<?= old('email', $professor['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-2 mb-3">
                <label for="siape" class="form-label">SIAPE</label>
                <input type="text" name="siape" class="form-control" value="<?= old('siape', $professor['siape'] ?? '') ?>">
            </div>
           
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="<?= site_url('professores') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

<?= $this->endSection() ?>