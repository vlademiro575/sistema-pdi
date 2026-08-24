<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sistema PDI - Painel de Controle</title>

    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">

        <?= $this->include('layouts/partials/sidebar') ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <?= $this->include('layouts/partials/topbar') ?>

                <div class="container-fluid">

                    <?php if (session()->getFlashdata('sucesso')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('sucesso') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('erro')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('erro') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <?= $this->renderSection('conteudo') ?>

                </div>
            </div>

            <?= $this->include('layouts/partials/footer') ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>