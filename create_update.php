<?php
// Conexión a la base de datos
require __DIR__ . '/env.php';

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

// Obtener datos del formulario
$userId = $_POST['userId'];
$name = $_POST['name'];
$email = $_POST['email'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (empty($userId)) {
        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    } else {
        // Actualizar usuario existente
        $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

