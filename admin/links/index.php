<!DOCTYPE html>
<html>

<head>
    <title>Admin - URL Shortener</title>
    <link href="style.css" rel="stylesheet">
    <!-- Incluye los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2 class="mt-4">Admin - URL Shortener</h2>

        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Long URL</th>
                    <th>Short URL</th>
                    <th>Created</th>
                    <th>Hits</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database configuration file
                require_once '../../dbConfig.php';

                // Include URL Shortener library file
                require_once '../../Shortener.class.php';

                // Initialize Shortener class and pass PDO object
                $shortener = new Shortener($db);

                try {
                    // Fetch all URLs from the database
                    $urls = $shortener->fetchAllUrls();

                    // Loop through the URLs and display them in the table
                    foreach ($urls as $url) {
                        echo "<tr>";
                        echo "<td>" . $url['id'] . "</td>";
                        echo "<td>" . $url['long_url'] . "</td>";
                        echo "<td>" . $url['short_code'] . "</td>";
                        echo "<td>" . $url['created'] . "</td>";
                        echo "<td>" . $url['hits'] . "</td>";
                        echo "</tr>";
                    }
                } catch (Exception $e) {
                    // Display error if there's a problem
                    echo "<tr><td colspan='5'>Error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Incluye los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
