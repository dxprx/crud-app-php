<?php
// Include database configuration file
require_once 'dbConfig.php';

// Include URL Shortener library file
require_once 'Shortener.class.php';

// Initialize Shortener class and pass PDO object
$shortener = new Shortener($db);

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    try {
        // Obtener el número de hits del enlace acortado
        $hits = $shortener->getHits($code);

        // Obtener información sobre el enlace acortado
        $linkInfo = $shortener->getUrlFromDB($code);

        // Obtener todos los visitantes del enlace acortado
        $visitors = $shortener->getVisitors($linkInfo['id']);
    } catch (Exception $e) {
        // Mostrar error si ocurre algún problema
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Información del Enlace Acortado</title>
    <!-- Agrega aquí tus estilos CSS si los tienes -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
</head>

<body>
    <div class="container">
        <h1>Información del Enlace Acortado</h1>

        <?php if (isset($error)) : ?>
            <p>Error: <?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (isset($linkInfo)) : ?>
            <h2>Enlace Acortado: <?php echo $linkInfo['short_code']; ?></h2>
            <p>URL Original: <a href="<?php echo $linkInfo['long_url']; ?>"><?php echo $linkInfo['long_url']; ?></a></p>
            <p>Número de Hits: <?php echo $hits; ?></p>
            <h3>Visitas</h3>
            <div class="table-container">
                <table class="table table-bordered table-striped table-auto">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha de Visita</th>
                            <th>Dirección IP</th>
                            <th>User Agent</th>
                            <th>Idioma</th>
                            <th>Referencia</th>
                            <th>Página Visitada</th>
                            <?php $geoFields = ['status', 'message', 'continent', 'continentCode', 'country', 'countryCode', 'region', 'regionName', 'city', 'district', 'zip', 'lat', 'lon', 'timezone', 'offset', 'currency', 'isp', 'org', 'as', 'asname', 'reverse', 'mobile', 'proxy', 'hosting', 'query']; ?>
                            <?php foreach ($geoFields as $field) : ?>
                                <th><?php echo $field; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visitors as $visitor) : ?>
                            <tr>
                                <td><?php echo $visitor['id']; ?></td>
                                <td><?php echo $visitor['visit_timestamp']; ?></td>
                                <td><?php echo $visitor['visitor_ip']; ?></td>
                                <td><?php echo $visitor['visitor_user_agent']; ?></td>
                                <td><?php echo $visitor['visitor_language']; ?></td>
                                <td><?php echo $visitor['visitor_referrer']; ?></td>
                                <td><?php echo $visitor['visitor_page']; ?></td>
                                <?php if ($visitor['visitor_geo'] !== null) : ?>
                                    <?php $geoData = json_decode($visitor['visitor_geo'], true); ?>
                                    <?php foreach ($geoFields as $field) : ?>
                                        <td><?php echo isset($geoData[$field]) ? $geoData[$field] : ''; ?></td>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <?php // Mostrar celdas vacías para los campos de visitor_geo ?>
                                    <?php foreach ($geoFields as $field) : ?>
                                        <td></td>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <canvas id="myChart" width="800" height="400"></canvas>
        <?php endif; ?>
    </div>

    <script>
        // Obtener los datos de los user agents para el gráfico
        const userAgentLabels = <?php echo json_encode($userAgentLabels); ?>;
        const userAgentData = <?php echo json_encode($userAgentData); ?>;

        // Crear el gráfico
        const ctx = document.getElementById("myChart").getContext("2d");
        const myChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: userAgentLabels,
                datasets: [{
                    label: "Visitas por User Agent",
                    data: userAgentData,
                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: "category",
                    },
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    </script>
</body>
</html>
