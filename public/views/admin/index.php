<?php

    include_once __DIR__ . '/../../../app/models/User/UserModel.php';
    include_once __DIR__ . '/../../../app/models/Product/ProductModel.php';
    include_once __DIR__ . '/../../../app/models/Order/OrderModel.php';
    $userModel = new UserModel();
    $productModel = new ProductModel();
    $orderModel = new OrderModel();
    $totalUsers = $userModel->countUser();
    $totalProducts = $productModel->countProducts();
    $totalOrdersToday = $orderModel->getOrdersToday();
    $sellsToday = $orderModel->getSellsToday();
    $ordenes = $orderModel->getRecentOrders(10);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Panel de administraci%ntilde;n | Coffee Shop </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../public/assets/css/dashboard.css">
</head>

<body>

    <!--Navbar-->
    <?php require_once __DIR__ . '/admin-navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <!-- Slidebar -->
             <?php require_once __DIR__ . '/admin-slidebar.php'; ?>

            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <h2 class="mb-4">Dashboard</h2>

                <!-- Estadísticas -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Usuarios</h6>
                                        <h3 class="card-title"><?php echo $totalUsers ?></h3>
                                    </div>
                                    <i class="bi bi-people display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Productos</h6>
                                        <h3 class="card-title"><?php echo $totalProducts ?></h3>
                                    </div>
                                    <i class="bi bi-cup display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Órdenes Hoy</h6>
                                        <h3 class="card-title"><?php echo $totalOrdersToday; ?></h3>
                                    </div>
                                    <i class="bi bi-cart display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Ventas Hoy</h6>
                                        <h3 class="card-title">$<?php echo number_format($sellsToday, 2); ?></h3>
                                    </div>
                                    <i class="bi bi-currency-dollar display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Acciones Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <a href="usuarios.php" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-person-plus me-2"></i>Agregar Usuario
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="productos.php" class="btn btn-outline-success w-100">
                                            <i class="bi bi-plus-circle me-2"></i>Agregar Producto
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="categorias.php" class="btn btn-outline-warning w-100">
                                            <i class="bi bi-tag me-2"></i>Gestionar Categorías
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="reportes.php" class="btn btn-outline-info w-100">
                                            <i class="bi bi-file-earmark-bar-graph me-2"></i>Ver Reportes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimas órdenes -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Órdenes Recientes</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th> Numero Orden </th>
                                                <th>Mesa</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($ordenes)):
                                                foreach ($ordenes as $orden):
                                            ?>
                                                    <tr>
                                                        <td>#<?php echo htmlspecialchars($orden['idOrder'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($orden['cliente'] ?? 'No asignado'); ?></td>
                                                        <td>#<?php echo htmlspecialchars($orden['orderNumber'] ?? ''); ?></td>
                                                        <td>$<?php echo number_format($orden['totalAmount'] ?? 0, 2); ?></td>
                                                        <td><?php echo htmlspecialchars($orden['mesa'] ?? 'No asignada'); ?></td>
                                                        
                                                        <td>
                                                            <span class="badge bg-<?php
                                                                                    switch ($orden['status']) {
                                                                                        case 'pendiente':
                                                                                            echo 'warning';
                                                                                            break;
                                                                                        case 'preparando':
                                                                                            echo 'info';
                                                                                            break;
                                                                                        case 'lista':
                                                                                            echo 'primary';
                                                                                            break;
                                                                                        case 'entregada':
                                                                                            echo 'success';
                                                                                            break;
                                                                                        default:
                                                                                            echo 'secondary';
                                                                                    }
                                                                                    ?>">
                                                                <?php echo ucfirst($orden['status']) ?? 'desconocido'; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($orden['createdAt'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No hay órdenes recientes</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>