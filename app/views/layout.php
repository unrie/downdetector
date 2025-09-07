<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downdetector - Мониторинг сайтов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card { transition: all 0.3s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .border-success { border: 2px solid #28a745 !important; }
        .border-danger { border: 2px solid #dc3545 !important; }
        .site-card { transition: all 0.3s ease; }
        .site-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-signal"></i> Downdetector
            </a>
        </div>
    </nav>

    <?php 
    // Включаем основной контент
    $contentFile = __DIR__ . '/' . $viewName . '.php';
    if (file_exists($contentFile)) {
        require_once $contentFile;
    } else {
        echo '<div class="container mt-4"><div class="alert alert-danger">View not found</div></div>';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>