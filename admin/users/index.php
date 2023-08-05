<!DOCTYPE html>
<html>

<head>
    <title>Administración</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Administración</h1>
    <h2>Usuarios</h2>
    <table id="userTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th>Email</th>
                <th>Fecha de creación</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'read.php'; ?>
        </tbody>
    </table>
</body>

</html>