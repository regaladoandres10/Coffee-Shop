<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/signUp.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h4> Registrarse </h4>
                </div>
                <div class="card-body">
                    <form id="signupForm">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="nameUser" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nameUser" name="nameUser" required 
                                   placeholder="Ej: andres_luna">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Teléfono (10 dígitos)</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required 
                                   pattern="[0-9]{10}" title="El teléfono debe tener 10 dígitos.">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <!--Seleccionar rol-->
                        <div class="mb-3">
                            <label for="role" class="form-label">Rol a Asignar</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="Cliente" selected>Cliente</option>
                                <option value="Staff">Staff</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Registrarme</button>
                        </div>
                    </form>
                    
                    <p id="signupMessage" class="mt-3 text-center fw-bold"></p>
                    
                    <p class="text-center mt-3">
                        ¿Ya tienes cuenta? <a href="login.php">Inicia Sesión aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>