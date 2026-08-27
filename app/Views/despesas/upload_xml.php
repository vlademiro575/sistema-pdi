<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('despesas') ?>">Despesas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Importar XML SEFAZ</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice text-primary mr-2"></i>Importar Nota Fiscal Eletrônica (XML)
        </h1>
        <a href="<?= base_url('despesas') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar para a Lista
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Erros no envio do arquivo:</h6>
            <ul class="mb-0 pl-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Seleção de Arquivo XML (Padrão SEFAZ)
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Faça o upload do arquivo <strong>.xml</strong> da Nota Fiscal Eletrônica (NF-e Modelo 55 ou NFC-e Modelo 65). 
                        O sistema fará a leitura automática das seguintes informações para pré-preencher o formulário de cadastro:
                    </p>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <ul class="list-unstyled small text-muted">
                                <li><i class="fas fa-check text-success mr-2"></i>Número do Documento / NF-e</li>
                                <li><i class="fas fa-check text-success mr-2"></i>Data de Emissão</li>
                                <li><i class="fas fa-check text-success mr-2"></i>Razão Social do Fornecedor</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled small text-muted">
                                <li><i class="fas fa-check text-success mr-2"></i>CNPJ / CPF do Fornecedor</li>
                                <li><i class="fas fa-check text-success mr-2"></i>Valor Total da Nota</li>
                                <li><i class="fas fa-check text-success mr-2"></i>Itens e Produtos Adquiridos</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Atenção:</strong> A despesa <strong>não</strong> será gravada de imediato. Você poderá revisar todos os dados, selecionar o Projeto e a Rubrica orçamentária antes de confirmar o salvamento.
                    </div>

                    <form action="<?= base_url('despesas/processar-xml') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="form-group bg-light p-4 rounded border text-center my-4">
                            <i class="fas fa-file-code fa-3x text-primary mb-3"></i>
                            <h6 class="font-weight-bold">Selecione o arquivo XML da Nota Fiscal</h6>
                            <p class="text-muted small mb-3">Formato aceito: .xml (Tamanho máximo: 5MB)</p>
                            
                            <div class="custom-file col-md-8 mx-auto text-left">
                                <input type="file" name="xml_file" id="xml_file" class="custom-file-input" accept=".xml,text/xml,application/xml" required>
                                <label class="custom-file-label text-truncate" for="xml_file" data-browse="Procurar">Escolher arquivo XML...</label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('despesas') ?>" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                                <i class="fas fa-cog mr-1"></i> Processar XML e Preencher Formulário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Atualiza o label do custom-file-input com o nome do arquivo selecionado
        $('#xml_file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Escolher arquivo XML...');
        });
    });
</script>
<?= $this->endSection() ?>

