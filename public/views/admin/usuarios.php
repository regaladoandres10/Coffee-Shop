<?php
//Incluir archivos
include_once __DIR__ . '/../../../app/models/User/UserModel.php';

//Instancias a modelos
$userModel = new UserModel();

//Funciones
$totalUsers = $userModel->countUser();
$totalCustomers = $userModel->countCustomers();
$totalStaff = $userModel->countStaff();
$totalAdmins = $userModel->countAdmins();
$users = $userModel->getAllUsers();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | Panel de Administración</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .role-admin {
            background-color: #ff6b6b;
            color: white;
        }

        .role-staff {
            background-color: #4ecdc4;
            color: white;
        }

        .role-cliente {
            background-color: #45b7d1;
            color: white;
        }

        .status-active {
            color: #28a745;
        }

        .status-inactive {
            color: #dc3545;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin-right: 5px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-box input {
            padding-left: 40px;
        }
    </style>
</head>

<body>
    <!-- Navbar del Admin -->
    <?php include_once __DIR__ . '/../../../public/views/admin/admin-navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include_once __DIR__ . '/../admin/admin-slidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-2">
                            <i class="bi bi-people me-2"></i>Gestión de Usuarios
                        </h1>
                        <p class="text-muted mb-0">Administra los usuarios del sistema</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Usuario
                    </button>
                </div>

                <!-- Estadísticas Rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Total Usuarios</h6>
                                        <h3 class="card-title" id="totalUsers"> <?php echo $totalUsers; ?> </h3>
                                    </div>
                                    <i class="bi bi-people display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Clientes</h6>
                                        <h3 class="card-title" id="totalClients"> <?php echo $totalCustomers; ?> </h3>
                                    </div>
                                    <i class="bi bi-person-check display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Staff</h6>
                                        <h3 class="card-title" id="totalStaff"> <?php echo $totalStaff; ?> </h3>
                                    </div>
                                    <i class="bi bi-person-badge display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Administradores</h6>
                                        <h3 class="card-title" id="totalAdmins"> <?php echo $totalAdmins; ?> </h3>
                                    </div>
                                    <i class="bi bi-shield-check display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Usuarios -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">Lista de Usuarios</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuario...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($users)):
                                        foreach ($users as $user):
                                    ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($user['idUser'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($user['nameUser'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($user['name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($user['phone']) ?? ''; ?></td>
                                                <td><?php echo htmlspecialchars($user['RoleName'] ?? 'No encontrado'); ?></td>

                                                <td>
                                                    <span class="badge bg-<?php
                                                                            switch ($user['is_active']) {
                                                                                case 1:
                                                                                    echo 'success';
                                                                                    break;
                                                                                case 0:
                                                                                    echo 'warning';
                                                                                    break;
                                                                                default:
                                                                                    echo 'secondary';
                                                                            }
                                                                            ?>">
                                                        <?php echo ucfirst($user['is_active']) ?? 'desconocido'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <!-- Ver detalles -->
                                                        <button class="btn btn-sm btn-info mb-1 view-user-btn"
                                                            data-id="<?php echo $user['idUser']; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewUserModal"
                                                            data-bs-title="Ver detalles">
                                                            <i class="bi bi-eye me-1"></i>Ver
                                                        </button>

                                                        <!-- Editar -->
                                                        <button class="btn btn-sm btn-warning mb-1 edit-user-btn"
                                                            data-id="<?php echo $user['idUser']; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editUserModal"
                                                            data-bs-title="Editar usuario">
                                                            <i class="bi bi-pencil me-1"></i>Editar
                                                        </button>

                                                        <!-- Eliminar -->
                                                        <?php if ($user['idUser'] != ($_SESSION['user_id'] ?? 0)): ?>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger mb-1 btn-delete-user"
                                                                data-user-id="<?php echo $user['idUser']; ?>"
                                                                data-user-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-title="Eliminar">
                                                                <i class="bi bi-trash me-1"></i>Eliminar
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-people display-4 mb-3"></i>
                                                    <p class="h5">No hay usuarios registrados</p>
                                                    <p class="mb-0">Comienza creando un nuevo usuario</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para crear usuario -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">
                        <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createUserForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nameUser" class="form-label">Nombre de Usuario *</label>
                                <input type="text" class="form-control" id="nameUser" name="nameUser" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="confirmPassword" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Rol *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="Cliente">Cliente</option>
                                    <option value="Staff">Staff / Mesero</option>
                                    <option value="Administrador">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="is_active" class="form-label">Estado</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div id="formMessage" class="alert d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">
                        <i class="bi bi-person-gear me-2"></i>Editar Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editName" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editUsername" class="form-label">Nombre de Usuario *</label>
                                <input type="text" class="form-control" id="editUsername" name="nameUser" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPhone" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="editPhone" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editPassword" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="editPassword" name="password">
                                <div class="form-text">Dejar en blanco para no cambiar</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editRole" class="form-label">Rol *</label>
                                <select class="form-select" id="editRole" name="role" required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value=3>Cliente</option>
                                    <option value=2>Staff / Mesero</option>
                                    <option value=1>Administrador</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editIsActive" class="form-label">Estado</label>
                                <select class="form-select" id="editIsActive" name="is_active">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div id="editFormMessage" class="alert d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">
                        <i class="bi bi-person-circle me-2"></i>Detalles del Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <div class="user-avatar mx-auto mb-3" id="viewUserAvatar">U</div>
                            <h5 id="viewUserName" class="mb-1">Nombre</h5>
                            <span class="badge" id="viewUserRoleBadge">Rol</span>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Nombre de Usuario</label>
                                    <p class="form-control-static fw-bold" id="viewUsername">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted"> Rol </label>
                                    <p class="form-control-static fw-bold" id="viewUserEmail">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Teléfono</label>
                                    <p class="form-control-static fw-bold" id="viewUserPhone">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Estado</label>
                                    <p class="form-control-static fw-bold" id="viewUserStatus">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Fecha de Registro</label>
                                    <p class="form-control-static fw-bold" id="viewUserCreated">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts -->
     <script src="../../../public/assets/js/User/deleteUser.js"></script>
    <script src="../../../public/assets/js/User/updateUser.js"></script>
    <script src="../../../public/assets/js/User/viewUser.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
</body>

</html>