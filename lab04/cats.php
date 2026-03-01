<?php
declare(strict_types=1);

$dir = 'image/';
$files = scandir($dir);

if ($files === false) {
    exit('Не удалось прочитать папку image/');
}

$images = [];
foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $path = $dir . $file;
    if (is_file($path)) {
        $images[] = $path;
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Зоопарк Котов</title>
    <style>
        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .gallery img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<h1>Зоопарк котов</h1>
<div class="gallery">
    <?php
    foreach ($images as $path) {
    echo "<img src=\"$path\" alt=\"cat\">";
    }
    ?>
</div>
</body>
</html>
