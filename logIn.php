<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Caffe shop </title>
    <link rel="stylesheet" href="./css/logIn.css">
    <link rel="stylesheet" href="./css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <!-- Formulario de Log In -->
    <div class="container">
        <form action="/public/index.php">
            <div class="form-group">
                <label class="form-label" for="user"> User: </label>
                <input type="text" placeholder="Enter user" class="form-control" id="user" name="user">
            </div>
            <div class="form-group">
                <label class="form-label" for="pwd"> Password: </label>
                <input type="password" placeholder="Enter password" id="pwd" name="pwd" >
            </div>
            <button type="submit" class="btn btn-default"> Send </button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>