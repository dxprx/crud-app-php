<?php
// Include database configuration file
require_once 'dbConfig.php';

// Include URL Shortener library file
require_once 'Shortener.class.php';

// Initialize Shortener class and pass PDO object
$shortener = new Shortener($db);

// Variables para almacenar la URL larga y la URL acortada
$longURL = '';
$shortURL = '';
// Prefijo de la URL acortada 
$shortURL_Prefix = 'http://localhost/'; // con URL rewrite
// Comprobar si se envió el formulario
if (isset($_POST['submit'])) {
    // Obtener la URL larga ingresada por el usuario desde el formulario
    $longURL = $_POST['long_url'];

    try {
        // Obtener la URL acortada
        $shortCode = $shortener->urlToShortCode($longURL);
        $shortURL = $shortURL_Prefix . $shortCode;
    } catch (Exception $e) {
        // Mostrar error si ocurre algún problema
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>URL Shortener</title>
</head>

<body>
    <h2>URL Shortener</h2>
    <form method="post">
        <label for="long_url">URL larga:</label>
        <input type="text" name="long_url" id="long_url" value="<?php echo htmlspecialchars($longURL); ?>">
        <input type="submit" name="submit" value="Acortar URL">
    </form>
    <div class="mt-4">
    <a href="stats.php" class="btn btn-secondary">Ver Stats de Enlace Acortado</a>
</div>
    <?php if (isset($shortURL)) { ?>
        <p>URL acortada: <a href="<?php echo htmlspecialchars($shortURL); ?>" target="_blank"><?php echo htmlspecialchars($shortURL); ?></a></p>
    <?php } ?>

    <?php if (isset($error)) { ?>
        <p>Error: <?php echo htmlspecialchars($error); ?></p>
    <?php } ?>
</body>

</html>
