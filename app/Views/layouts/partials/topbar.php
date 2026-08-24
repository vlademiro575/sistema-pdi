<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">
                    <?= session()->get('nome') ?? session()->get('login') ?? 'Usuário' ?>
                </span>
                <i class="fas fa-user-circle fa-2x text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <a class="dropdown-menu-item text-danger dropdown-item" href="<?= base_url('logout') ?>">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Sair do Sistema
                </a>
            </div>
        </li>
    </ul>
</nav>