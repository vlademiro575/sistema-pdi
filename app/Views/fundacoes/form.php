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
        $isEdit = isset($fundacao) && !empty($fundacao);
        $action = $isEdit ? site_url('fundacoes/atualizar/' . $fundacao['id_fundacao']) : site_url('fundacoes/salvar');
    ?>

    <form action="<?= $action ?>" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome da Fundação *</label>
                <input type="text" name="nome" class="form-control" value="<?= old('nome', $fundacao['nome'] ?? '') ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="cnpj" class="form-label">CNPJ *</label>
                <input type="text" name="cnpj" class="form-control" value="<?= old('cnpj', $fundacao['cnpj'] ?? '') ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="sigla" class="form-label">Sigla *</label>
                <input type="text" name="sigla" class="form-control" value="<?= old('sigla', $fundacao['sigla'] ?? '') ?>" required>
            </div>
        </div>

<div class="form-group mb-3">
    <label for="tipo" class="form-label">Tipo de Fundação <span class="text-danger">*</span></label>
    <select class="form-control" id="tipo" name="tipo" required>
        <option value="" disabled <?= empty($fundacao['tipo']) ? 'selected' : '' ?>>Selecione o tipo...</option>
        
        <option value="FUNDACAO_APOIO" 
            <?= set_select('tipo', 'FUNDACAO_APOIO', (isset($fundacao['tipo']) && $fundacao['tipo'] == 'FUNDACAO_APOIO')) ?>>
            Fundação de Apoio (Ex: FCPC, FASTEF)
        </option>
        
        <option value="FAP_ESTADUAL" 
            <?= set_select('tipo', 'FAP_ESTADUAL', (isset($fundacao['tipo']) && $fundacao['tipo'] == 'FAP_ESTADUAL')) ?>>
            FAP Estadual (Ex: FUNCAP, FAPESP)
        </option>
        
        <option value="ORGAO_FEDERAL" 
            <?= set_select('tipo', 'ORGAO_FEDERAL', (isset($fundacao['tipo']) && $fundacao['tipo'] == 'ORGAO_FEDERAL')) ?>>
            Órgão Federal (Ex: CNPq, CAPES)
        </option>
    </select>
    
    <?php if(session('errors.tipo')): ?>
        <div class="text-danger mt-1">
            <small><?= session('errors.tipo') ?></small>
        </div>
    <?php endif; ?>
</div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="<?= site_url('fundacoes') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

<?= $this->endSection() ?>