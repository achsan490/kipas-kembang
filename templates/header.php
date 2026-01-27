<?php
// templates/header.php
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/functions.php';

checkLogin();
$user = user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinerja Pengawas Jombang - Kemenag</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/image.jpg'); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url('assets/img/image.jpg'); ?>" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for Dashboard Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>

<div class="layout-wrapper">
    <!-- Sidebar akan di-include setelah ini di file view, tapi biasanya struktur php include header dulu baru sidebar. 
         Untuk struktur layout-wrapper yang flex, sidebar harus di dalam wrapper. 
         Mari kita asumsikan urutan include: header.php -> sidebar.php -> content -> footer.php.
         Jadi header.php membuka html & body, dan mungkin membuka wrapper?
         
         Jika sidebar.php di-include *setelah* header.php, maka header.php sebaiknya JANGAN menutup wrapper.
         Tapi tunggu, struktur sebelumnya adalah: Header (Navbar) -> Sidebar -> Content.
         Navbar di atas, Sidebar di kiri.
         
         Di desain baru, Sidebar full height kiri, Header (Topbar) di kanan atas.
         Jadi strukturnya: 
         <div layout-wrapper>
            <include sidebar>
            <div main-wrapper>
                <include header/topbar>
                <div content-body>
    -->
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar()"></div>
