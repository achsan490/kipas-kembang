<?php
// templates/footer.php
?>
    </div> <!-- End .content-body -->
</div> <!-- End .main-wrapper -->
</div> <!-- End .layout-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Konfirmasi hapus universal
    function confirmDelete(url) {
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            window.location.href = url;
        }
    }

    // Toggle Sidebar Mobile
    function toggleSidebar() {
        document.getElementById('sidebarMenu').classList.toggle('show');
        document.getElementById('mobileOverlay').classList.toggle('show');
    }
</script>
</body>
</html>
