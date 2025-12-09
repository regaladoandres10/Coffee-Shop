<?php
$currentYear = date('Y');
?>
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-cup-hot me-2"></i> Coffee-Shop
                </h5>
                <p class="mb-3">Disfruta de los mejores platillos y bebidas en un ambiente agradable.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <a href="index.php" class="text-white text-decoration-none me-3">Inicio</a>
                    <a href="menu.php" class="text-white text-decoration-none me-3">Menú</a>
                    <a href="reservaciones.php" class="text-white text-decoration-none me-3">Reservaciones</a>
                </p>
            </div>
        </div>
        <hr class="bg-light my-3">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?php echo $currentYear; ?> Coffee-Shop. Todos los derechos reservados.</p>
            </div>
            
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>

