
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Coffee Shop - realizar pedido </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    
    <!-- Navbar -->
    <?php 
        include_once __DIR__ . '/includes/header.php'; 
    ?>

    <!-- Banner -->
    <section class="banner bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold">Bienvenido a Coffee-Shop</h1>
                    <p class="lead">Disfruta de los mejores platillos y bebidas en un ambiente agradable y acogedor.</p>
                    <div class="mt-4">
                            <a href="/public/views/auth/logIn.php" class="btn btn-light btn-lg me-3">Iniciar Sesión</a>
                            <a href="/public/views/auth/signUp.php" class="btn btn-outline-light btn-lg">Registrarse</a>
                    </div>    
                </div>
                <div class="col-lg-6">
                    <img src="/public/assets/img/barista.jpg" alt="Restaurante" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nuestros Servicios</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-cup-hot display-4 text-primary mb-3"></i>
                            <h4 class="card-title">Cafetería</h4>
                            <p class="card-text">Disfruta de nuestro café de especialidad y deliciosos postres.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-egg-fried display-4 text-primary mb-3"></i>
                            <h4 class="card-title">Restaurante</h4>
                            <p class="card-text">Platillos gourmet preparados con ingredientes frescos y de calidad.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-calendar-check display-4 text-primary mb-3"></i>
                            <h4 class="card-title">Reservaciones</h4>
                            <p class="card-text">Reserva tu mesa con anticipación para garantizar tu lugar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php 
        include_once __DIR__ . '/includes/footer.php';
    ?>
</body>
</html>