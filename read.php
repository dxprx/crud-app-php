<?php
// Conexión a la base de datos
require __DIR__ . '/env.php';

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta de usuarios
    $query = $conn->query("SELECT * FROM users");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>".$row['name']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>";
        echo "<button class='editButton' data-id='".$row['id']."' data-name='".$row['name']."' data-email='".$row['email']."'>Editar</button>";
        echo "<button class='deleteButton' data-id='".$row['id']."'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

