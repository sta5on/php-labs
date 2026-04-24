<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Lab 07') ?></title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; color: #222; }
        .container { max-width: 960px; margin: 0 auto; padding: 24px 16px 40px; }
        .section { background: #fff; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; }
        .field { display: grid; gap: 6px; margin-bottom: 12px; }
        input, select, textarea, button { font: inherit; }
        input[type="text"], input[type="date"], input[type="number"], select, textarea {
            width: 100%; padding: 8px; border: 1px solid #bbb; background: #fff;
            box-sizing: border-box;
        }
        button { width: fit-content; padding: 8px 14px; border: 1px solid #999; background: #f0f0f0; cursor: pointer; }
        .inline-option { display: inline-flex; align-items: center; gap: 6px; margin-right: 14px; }
        .message { padding: 10px 12px; margin-bottom: 16px; border: 1px solid #ccc; background: #fff; }
        .message.success { border-color: #5e9b6b; }
        .message.error, .required, .error-text { color: #b00020; }
        .view-switch { display: flex; gap: 10px; margin: 16px 0 20px; flex-wrap: wrap; }
        .view-switch a { color: #004a8f; text-decoration: none; }
        .view-switch .current { font-weight: 700; color: #222; }
        .sort-links { margin-bottom: 12px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; }
        @media (max-width: 640px) { .inline-option { display: flex; margin-bottom: 8px; } }
    </style>
</head>
<body>
<main class="container">
    <h1>Банковские транзакции</h1>
    <p>Лабораторная работа №7: разделение логики и представления, нативные PHP-шаблоны и Twig.</p>

    <nav class="view-switch" aria-label="Переключение представлений">
        <a class="<?= ($activeView ?? '') === 'php' ? 'current' : '' ?>" href="index.php">PHP Templates</a>
        <a class="<?= ($activeView ?? '') === 'twig' ? 'current' : '' ?>" href="twig.php">Twig Templates</a>
    </nav>

    <?= $content ?? '' ?>
</main>
</body>
</html>
