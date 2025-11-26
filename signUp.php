<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Sign Up | Caffe shop </title>
</head>
<body>
    
    <div class="mb-3 mt-3">
        <form action="logIn.php">
        <!-- Colocar combo box para seleccionar rol dentro del sistema 
        Tipos de roles: 
        - Cliente
        - Mesero
        - Administrador
        - Cajero (Encargado de cagas)
        - Encargado de inventario
        - 
        -->
            <select id="roles" class="form-control mt-3">
                <option> Administrador </option>
                <option> Cliente </option>
                <option> Mesero </option>
                <option> Encargado de inventario </option>
            </select>

            <input class="form-control mt-3" type="text" placeholder="Ingresa el nombre de usuario" id="user">

            <input type="password">

            <button type="submit" class="btn btn-success"></button>
        </form>
    </div>

</body>
</html>