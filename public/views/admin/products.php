<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Products|Dashboard </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <h1>Gestión de Productos</h1>

    <form id="productForm" onsubmit="event.preventDefault(); handleSubmit('products', 'productForm');">
        <input type="hidden" name="id" id="productId"> 

        <label for="name">Nombre:</label>
        <input type="text" name="name" required>
        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <div id="formMessage" class="mt-3"></div>
    </form>

    <hr>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Disponible</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            </tbody>
    </table>

    <script src="../../assets/js/product.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        //Cargar los datos al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            loadData('products', 'productsTableBody');
        });
    </script>
</body>
</html>