<?php
// templates/sidebar.php
$current_page = $_SERVER['REQUEST_URI'];
function isActive($url) {
    global $current_page;
    return strpos($current_page, $url) !== false ? 'active' : '';
}
$role = $_SESSION['role'] ?? '';
?>


<!-- SIDEBAR -->
<nav id="sidebarMenu" class="sidebar">
    <div class="sidebar-brand d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <!-- Logo Kinerja Pengawas Jombang -->
            <img src="<?php echo base_url('assets/img/image.jpg'); ?>" alt="Logo" width="45" class="rounded-circle">
            <div class="lh-1">
                <div class="small fw-normal text-white-50">Kinerja</div>
                <span class="fw-bold">Pengawas</span>
            </div>
        </div>
        <button class="btn btn-link text-white d-lg-none p-0" onclick="toggleSidebar()">
            <i class="fas fa-times fa-lg"></i>
        </button>
    </div>
    
    <div class="sidebar-menu">
        <small class="text-uppercase text-white-50 fw-bold mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Menu Utama</small>
        
        <a class="nav-link <?php echo isActive('dashboard'); ?>" href="<?php echo base_url('modules/dashboard/index.php'); ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <?php if ($role === 'pengawas'): ?>
        <a class="nav-link <?php echo isActive('kinerja/index.php'); ?>" href="<?php echo base_url('modules/kinerja/index.php'); ?>">
            <i class="fas fa-clipboard-list"></i> Data Kinerja
        </a>
        <a class="nav-link <?php echo isActive('kinerja/tambah.php'); ?>" href="<?php echo base_url('modules/kinerja/tambah.php'); ?>">
            <i class="fas fa-plus-circle"></i> Input Kinerja
        </a>
        <?php endif; ?>

        <?php if ($role === 'pendamping'): ?>
        <a class="nav-link <?php echo isActive('kinerja/index.php'); ?>" href="<?php echo base_url('modules/kinerja/index.php'); ?>">
            <i class="fas fa-clipboard-list"></i> Data Kinerja
        </a>
        <a class="nav-link <?php echo isActive('kinerja/tambah.php'); ?>" href="<?php echo base_url('modules/kinerja/tambah.php'); ?>">
            <i class="fas fa-plus-circle"></i> Input Kinerja
        </a>
        <a class="nav-link <?php echo isActive('laporan'); ?>" href="<?php echo base_url('modules/laporan/index.php'); ?>">
            <i class="fas fa-file-alt"></i> Laporan Saya
        </a>
        <?php endif; ?>

        <?php if ($role === 'pengawas'): ?>
        <a class="nav-link <?php echo isActive('madrasah/madrasah_saya'); ?>" href="<?php echo base_url('modules/madrasah/madrasah_saya.php'); ?>">
            <i class="fas fa-school"></i> Madrasah Binaan Saya
        </a>
        <a class="nav-link <?php echo isActive('laporan'); ?>" href="<?php echo base_url('modules/laporan/index.php'); ?>">
            <i class="fas fa-file-alt"></i> Laporan Saya
        </a>
        <?php endif; ?>

        <?php if ($role === 'pimpinan' || $role === 'admin'): ?>
        <!-- <a class="nav-link <?php echo isActive('pimpinan/pengawas'); ?>" href="<?php echo base_url('modules/pimpinan/pengawas/index.php'); ?>">
            <i class="fas fa-users-cog"></i> Monitoring Pengawas
        </a> -->
        <a class="nav-link <?php echo isActive('laporan'); ?>" href="<?php echo base_url('modules/laporan/index.php'); ?>">
            <i class="fas fa-chart-pie"></i> Laporan & Rekap
        </a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <div class="mt-4 mb-2">
            <small class="text-uppercase text-white-50 fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Administrator</small>
        </div>
        <a class="nav-link <?php echo isActive('admin/users'); ?>" href="<?php echo base_url('modules/admin/users/index.php'); ?>">
            <i class="fas fa-users-cog"></i> Kelola User
        </a>
        <a class="nav-link <?php echo isActive('admin/madrasah'); ?>" href="<?php echo base_url('modules/admin/madrasah/index.php'); ?>">
            <i class="fas fa-school"></i> Data Madrasah
        </a>
        <a class="nav-link <?php echo isActive('admin/pengawasan'); ?>" href="<?php echo base_url('modules/admin/pengawasan/index.php'); ?>">
            <i class="fas fa-users-cog"></i> Kelola Pengawasan
        </a>
        <?php endif; ?>
    </div>
</nav>

<!-- MAIN WRAPPER (Content Right) -->
<div class="main-wrapper">
    <!-- TOP HEADER -->
    <header class="top-header">
        <button class="header-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="d-flex align-items-center ms-auto">
            <div class="dropdown">
                <div class="user-profile" data-bs-toggle="dropdown">
                    <div class="text-end d-none d-md-block">
                        <div class="fw-bold small mb-0 text-dark"><?php echo $user['nama']; ?></div>
                        <div class="text-muted small" style="font-size: 0.75rem;"><?php echo ucfirst($user['role']); ?></div>
                    </div>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['nama'], 0, 1)); ?>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 p-2 rounded-3">
                    <li><a class="dropdown-item rounded-2" href="#"><i class="fas fa-user-circle me-2 text-primary"></i> Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item rounded-2 text-danger" href="<?php echo base_url('modules/auth/logout.php'); ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- CONTENT BODY (Started here, closed in footer) -->
    <div class="content-body">
