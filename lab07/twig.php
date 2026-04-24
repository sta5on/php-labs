<?php

declare(strict_types=1);

require_once __DIR__ . '/src/app.php';

session_start();

$pageContext = buildTransactionPageContext(__DIR__ . '/data/transactions.json', 'twig.php');
$pageContext['formAction'] = 'twig.php';
$pageContext['activeView'] = 'twig';
$pageContext['title'] = 'Lab 07 | Twig';

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    $content = <<<HTML
<section class="section">
    <h2>Twig пока недоступен</h2>
    <p>Для страницы <code>twig.php</code> нужно установить зависимость <code>twig/twig</code> через Composer.</p>
    <p>Когда <code>composer</code> будет доступен, выполните в каталоге <code>lab07</code> команду <code>composer install</code>.</p>
</section>
HTML;

    echo renderPhpTemplate(
        __DIR__ . '/templates/php/layout.php',
        [
            'title' => $pageContext['title'],
            'activeView' => $pageContext['activeView'],
            'content' => $content,
        ]
    );

    return;
}

require_once $autoloadPath;
require_once __DIR__ . '/src/Twig/TransactionTwigExtension.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates/twig');
$twig = new \Twig\Environment(
    $loader,
    [
        'autoescape' => 'html',
    ]
);
$twig->addExtension(new TransactionTwigExtension());

echo $twig->render('index.twig', $pageContext);
