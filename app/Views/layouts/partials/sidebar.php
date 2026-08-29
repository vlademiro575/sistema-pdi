<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('dashboard') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-atom"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Sistema <sup>PDI</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= url_is('dashboard*') || url_is('/') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Módulos de Gestão</div>

    <li class="nav-item <?= url_is('bolsistas*') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('bolsistas') ?>">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Bolsistas</span>
        </a>
    </li>

    <li class="nav-item <?= url_is('professores*') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('professores') ?>">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Professores</span>
        </a>
    </li>

    <li class="nav-item <?= url_is('projetos*') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('projetos') ?>">
            <i class="fas fa-fw fa-project-diagram"></i>
            <span>Projetos & Rubricas</span>
        </a>
    </li>

    <li class="nav-item <?= url_is('fundacoes*') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('fundacoes') ?>">
            <i class="fas fa-fw fa-building"></i>
            <span>Fundações</span>
        </a>
    </li>

    <li class="nav-item <?= url_is('despesas*') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('despesas') ?>">
            <i class="fas fa-fw fa-file-invoice-dollar"></i>
            <span>Despesas</span>
        </a>
    </li>

    <li class="nav-item <?= url_is('auditoria*') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('auditoria') ?>">
            <i class="fas fa-fw fa-shield-alt"></i>
            <span>Verificar Pendências</span>
        </a>
    </li>

    <?php if (session()->get('perfil') === 'ADMIN'): ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Administração</div>

        <li class="nav-item <?= url_is('usuarios*') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('usuarios') ?>">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Usuários</span>
            </a>
        </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>