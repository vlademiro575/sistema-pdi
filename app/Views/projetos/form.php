<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?= isset($projeto['id_projeto']) ? 'Editar Projeto' : 'Novo Projeto' ?>
        </h1>
        <a href="<?= base_url('projetos') ?>" class="btn btn-secondary btn-icon-split shadow-sm">
            <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
            <span class="text">Voltar para a Lista</span>
        </a>
    </div>

    <!-- Exibição de Erros de Validação -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados Principais do Projeto</h6>
        </div>
        <div class="card-body">
            
            <?php 
                $isEdit = isset($projeto['id_projeto']);
                $action = $isEdit ? base_url('projetos/update/' . $projeto['id_projeto']) : base_url('projetos/create');
            ?>

            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="titulo" class="font-weight-bold">Título do Projeto <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="titulo" class="form-control" 
                               value="<?= old('titulo', $projeto['titulo'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="codigo_projeto_fundacao" class="font-weight-bold">Código na Fundação <span class="text-danger">*</span></label>
                        <input type="text" name="codigo_projeto_fundacao" id="codigo_projeto_fundacao" class="form-control" 
                               value="<?= old('codigo_projeto_fundacao', $projeto['codigo_projeto_fundacao'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Dropdown de Professores -->
                    <div class="col-md-6 mb-3">
                        <label for="id_professor" class="font-weight-bold">Professor Coordenador <span class="text-danger">*</span></label>
                        <select name="id_professor" id="id_professor" class="form-control" required>
                            <option value="">Selecione o Professor...</option>
                            <?php foreach ($professores as $prof): ?>
                                <option value="<?= $prof['id_professor'] ?>" 
                                    <?= set_select('id_professor', $prof['id_professor'], (isset($projeto['id_professor']) && $projeto['id_professor'] == $prof['id_professor'])) ?>>
                                    <?= esc($prof['nome']) ?> (CPF: <?= esc($prof['cpf']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dropdown de Fundações -->
                    <div class="col-md-6 mb-3">
                        <label for="id_fundacao" class="font-weight-bold">Fundação Gestora <span class="text-danger">*</span></label>
                        <select name="id_fundacao" id="id_fundacao" class="form-control" required>
                            <option value="">Selecione a Fundação...</option>
                            <?php foreach ($fundacoes as $fund): ?>
                                <!-- Supondo que a tabela fundações tenha as colunas id_fundacao e sigla/nome -->
                                <option value="<?= $fund['id_fundacao'] ?>" 
                                    <?= set_select('id_fundacao', $fund['id_fundacao'], (isset($projeto['id_fundacao']) && $projeto['id_fundacao'] == $fund['id_fundacao'])) ?>>
                                    <?= esc($fund['sigla'] ?? $fund['id_fundacao']) ?> 
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="orcamento_total" class="font-weight-bold">Orçamento Total (R$) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="orcamento_total" id="orcamento_total" class="form-control" 
                               value="<?= old('orcamento_total', $projeto['orcamento_total'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="data_inicio" class="font-weight-bold">Data de Início <span class="text-danger">*</span></label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" 
                               value="<?= old('data_inicio', $projeto['data_inicio'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="data_fim" class="font-weight-bold">Data de Fim <span class="text-danger">*</span></label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control" 
                               value="<?= old('data_fim', $projeto['data_fim'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="mt-4 text-right">
                    <a href="<?= base_url('projetos') ?>" class="btn btn-secondary mr-2">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save mr-1"></i> <?= $isEdit ? 'Atualizar Projeto' : 'Avançar para Rubricas' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>