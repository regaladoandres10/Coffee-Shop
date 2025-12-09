<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-cup-hot me-2"></i>Coffee-Shop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                
                <!-- <?php if ($user): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $user['nombre_rol']; ?>/">Panel</a>
                    </li>
                    
                    <?php if ($user['nombre_rol'] === 'cliente'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cliente/menu.php">Menú</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cliente/reservar.php">Reservar</a>
                        </li>
                    <?php elseif ($user['nombre_rol'] === 'staff'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="staff/ordenes.php">Órdenes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="staff/reservaciones.php">Reservaciones</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?> -->
            </ul>
            
            <ul class="navbar-nav">

                <!-- <?php if ($user): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($user['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?> -->
                    <li class="nav-item">
                        <a class="nav-link" href="/public/views/auth/logIn.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/views/auth/signUp.php">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>