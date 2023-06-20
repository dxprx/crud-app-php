<!DOCTYPE html>
<html>
<head>
    <title>CRUD PHP-JavaScript</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>CRUD PHP-JavaScript</h1>
    <form id="userForm" method="post">
        <input type="hidden" id="userId" name="userId">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Guardar</button>
    </form>
    <table id="userTable">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'read.php'; ?>
        </tbody>
    </table>
</body>
</html>
