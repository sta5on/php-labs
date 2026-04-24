<?php

declare(strict_types=1);

require_once __DIR__ . '/src/app.php';

session_start();

$pageContext = buildTransactionPageContext(__DIR__ . '/data/transactions.json', 'index.php');
$pageContext['formAction'] = 'index.php';
$pageContext['activeView'] = 'php';
$pageContext['title'] = 'Lab 07 | PHP Templates';

$content = renderPhpTemplate(__DIR__ . '/templates/php/page.php', $pageContext);

echo renderPhpTemplate(
    __DIR__ . '/templates/php/layout.php',
    [
        'title' => $pageContext['title'],
        'activeView' => $pageContext['activeView'],
        'content' => $content,
    ]
);
