<?php
// Incluir modelo de Categorías
include_once __DIR__ . '/../../../app/models/Category/CategoryModel.php';

// Instancia del modelo
$categoryModel = new CategoryModel();

// Funciones del modelo
$totalCategories = $categoryModel->countCategories();
$categories = $categoryModel->getAllCategories();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías | Panel de Administración</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

</head>

<body>

    <!-- Navbar -->
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
                            <i class="bi bi-tags me-2"></i>Gestión de Categorías
                        </h1>
                        <p class="text-muted mb-0">Administra las categorías del sistema</p>
                    </div>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Categoría
                    </button>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">Total Categorías</h6>
                                <h3 class="card-title" id="totalCategories">
                                    <?= $totalCategories ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Categorías -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Categorías</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="categoryTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $categorie): ?>
                                            <tr>
                                                <td>#<?= $categorie['idCategorie'] ?></td>
                                                <td><?= htmlspecialchars($categorie['name']) ?></td>
                                                <td><?= htmlspecialchars($categorie['description']) ?></td>

                                                <td>
                                                    <span class="badge bg-<?= $categorie['isActive'] ? 'success' : 'danger' ?>">
                                                        <?= $categorie['isActive'] ? 'Activo' : 'Inactivo' ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <!-- Ver -->
                                                    <button class="btn btn-info btn-sm mb-1 view-category-btn"
                                                        data-id="<?= $categorie['idCategorie'] ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewCategoryModal">
                                                        Ver<i class="bi bi-eye"></i>
                                                    </button>

                                                    <!-- Editar -->
                                                    <button class="btn btn-warning btn-sm mb-1 edit-category-btn"
                                                        data-id="<?= $categorie['idCategorie'] ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editCategoryModal">
                                                        Editar<i class="bi bi-pencil"></i>
                                                    </button>

                                                    <!-- Eliminar -->
                                                    <button class="btn btn-danger btn-sm mb-1 delete-category-btn"
                                                        data-id="<?= $categorie['idCategorie'] ?>"
                                                        data-name="<?= htmlspecialchars($categorie['name']) ?>">
                                                        Eliminar<i class="bi bi-trash"></i>
                                                    </button>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-tags display-4 mb-3"></i>
                                                    <p class="h5">No hay categorías registradas</p>
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

    <!-- Modal Crear Categoría -->
    <div class="modal fade" id="createCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="createCategoryForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" name="name" required>

                        <label class="form-label mt-3">Descripción</label>
                        <textarea class="form-control" name="description"></textarea>

                        <label class="form-label mt-3">Estado</label>
                        <select class="form-select" name="isActive">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal Editar Categoría -->
    <div class="modal fade" id="editCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="editCategoryForm">
                    <input type="hidden" name="id" id="editCategoryId">

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Nombre *</label>
                        <input type="text" id="editCategoryName" name="name" class="form-control" required>

                        <label class="form-label mt-3">Descripción</label>
                        <textarea id="editCategoryDescription" name="description" class="form-control"></textarea>

                        <label class="form-label mt-3">Imagen</label>
                        <input type="file" class="form-control" name="image">

                        <label class="form-label mt-3">Estado</label>
                        <select id="editCategoryIsActive" class="form-select" name="isActive">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">Actualizar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal Ver Categoría -->
    <div class="modal fade" id="viewCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p><strong>Nombre:</strong> <span id="viewCategoryName"></span></p>
                    <p><strong>Descripción:</strong> <span id="viewCategoryDescription"></span></p>
                    <p><strong>Estado:</strong> <span id="viewCategoryState"></span></p>
                    <p><strong>Imagen:</strong></p>
                    <img id="viewCategoryImage" class="img-fluid rounded">

                </div>

            </div>
        </div>
    </div>

    <script src="../../assets/js/Categorie/deleteCategorie.js"></script>
    <script src="../../assets/js/Categorie/viewCategorie.js"></script>
    <script src="../../assets/js/Categorie/createCategorie.js"></script>
    <script src="../../assets/js/Categorie/updateCategorie.js"></script>
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
