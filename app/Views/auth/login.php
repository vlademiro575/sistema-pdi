<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sistema PDI - Login</title>

    <!-- Fontes e ícones do template -->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- CSS principal do SB Admin 2 -->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <!-- Imagem lateral (pode ser a logo do seu projeto ou instituição) -->
                            <div class="col-lg-6 d-none d-lg-block bg-login-image" style="background: url('https://source.unsplash.com/K4mSJ7kc0As/600x800') center / cover;"></div>
                            
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Bem-vindo ao Sistema PDI!</h1>
                                    </div>

                                    <!-- Alerta de Erro -->
                                    <?php if (session()->getFlashdata('erro')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= session()->getFlashdata('erro') ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Formulário com a sua rota corrigida -->
                                    <form class="user" action="<?= base_url('login/autenticar') ?>" method="post">
                                        <?= csrf_field() ?>
                                        
                                        <div class="form-group">
                                            <input type="text" name="login" class="form-control form-control-user" 
                                                id="inputLogin" placeholder="Digite seu usuário..." required autofocus>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="password" name="senha" class="form-control form-control-user" 
                                                id="inputSenha" placeholder="Senha" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Entrar
                                        </button>
                                    </form>
                                    
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="#">Esqueceu a senha? Contate o administrador.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts do template -->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>
</body>
</html>