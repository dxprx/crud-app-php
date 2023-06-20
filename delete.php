<?php
// Conexión a la base de datos
require __DIR__ . '/env.php';

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

// Obtener ID del usuario a eliminar
$userId = $_POST['userId'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Eliminar usuario
    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
