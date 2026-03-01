# Отчет по лабораторной работе №4

### Voronetchii Stanislav IA2404 USM

## Описание лабораторной работы
Лабораторная работа посвящена массивам и функциям в PHP.  
В рамках работы реализованы:
- система управления банковскими транзакциями (добавление, поиск, подсчет суммы, сортировка, вывод в HTML-таблицу);
- вывод галереи изображений из директории `image/`.

Цель: закрепить работу с массивами, `foreach`, функциями, строгой типизацией и базовыми операциями с файловой системой.

## Инструкции по запуску проекта
1. Убедиться, что установлен PHP 8+:
```bash
php -v
```
2. Перейти в директорию проекта:
```bash
cd /Users/MAC/VSC/php_labs/lab04
```
3. Запустить встроенный веб-сервер PHP:
```bash
php -S localhost:8000
```
4. Открыть в браузере:
- `http://localhost:8000/index.php` — транзакции и сортировки;
- `http://localhost:8000/cats.php` — галерея изображений.

## Краткая документация к проекту
Структура проекта:
- `index.php` — реализация задания 1 (массив транзакций, функции, таблицы, сортировки).
- `cats.php` — реализация задания 2 (чтение каталога `image/` и вывод изображений 3 в ряд).
- `image/` — каталог с изображениями.
- `task.md` — формулировка лабораторной работы.
- `report-requirements.md` — требования к отчету.

Основные функции в `index.php`:
- `calculateTotalAmount(array $transactions): float` — считает общую сумму транзакций.
- `findTransactionByDescription(string $descriptionPart): ?array` — поиск транзакции по описанию.
- `findTransactionById(int $id): ?array` — поиск по `id` через `foreach`.
- `findTransactionByIdV2(int $id): ?array` — поиск по `id` через `array_filter`.
- `daysSinceTransaction(string $date): int` — количество дней с даты транзакции до текущего дня.
- `getNextTransId(array $transactions): int` — генерация следующего `id`.
- `addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void` — добавление транзакции.
- `renderTransactionsTable(array $items, string $title): void` — рендер HTML-таблицы транзакций.

## Примеры использования проекта (фрагменты кода)
Добавление транзакции:
```php
$id = getNextTransId($transactions);
addTransaction($id, '2026-03-01', 200.37, 'Stas', 'Stas shop');
```

Подсчет общей суммы:
```php
$total = calculateTotalAmount($transactions);
echo $total;
```

Поиск транзакции по ID:
```php
$tx = findTransactionById(2);
```

Сортировка по дате и по сумме:
```php
$byDate = $transactions;
usort($byDate, fn($a, $b) => strcmp($a['date'], $b['date']));

$byAmountDesc = $transactions;
usort($byAmountDesc, fn($a, $b) => $b['amount'] <=> $a['amount']);
```

Вывод галереи изображений:
```php
foreach ($images as $path) {
    echo "<img src=\"$path\" alt=\"cat\">";
}
```

## Ответы на контрольные вопросы
1. Что такое массивы в PHP?  
Массив в PHP — это структура данных, которая хранит набор значений. Массивы могут быть индексными (с числовыми ключами), ассоциативными (с текстовыми ключами) и многомерными.

2. Каким образом можно создать массив в PHP?  
Через короткий синтаксис `[]` или через `array()`.  
Примеры:
```php
$a = [1, 2, 3];
$b = array('id' => 1, 'name' => 'Test');
```

3. Для чего используется цикл `foreach`?  
`foreach` используется для перебора элементов массива или объектов. Он удобен тем, что не требует ручного управления индексами и позволяет быстро получить текущий элемент (и при необходимости ключ).

## Список использованных источников
- [PHP Manual: Arrays](https://www.php.net/manual/ru/language.types.array.php)
- [PHP Manual: foreach](https://www.php.net/manual/ru/control-structures.foreach.php)
- [PHP Manual: usort](https://www.php.net/manual/ru/function.usort.php)
- [PHP Manual: array_filter](https://www.php.net/manual/ru/function.array-filter.php)
- [PHP Manual: scandir](https://www.php.net/manual/ru/function.scandir.php)
- [PHP Manual: Type declarations](https://www.php.net/manual/ru/language.types.declarations.php)
- [Metanit: Руководство по PHP](https://metanit.com/php/tutorial/)
- [Habr: статьи по тегу PHP](https://habr.com/ru/tags/php/articles/)
- [PHP The Right Way (русский перевод)](https://getjump.github.io/ru-php-the-right-way/)
- Материалы задания: `task.md`, `report-requirements.md`

## Дополнительные важные аспекты
- В проекте используется `declare(strict_types=1);`, что делает проверку типов строже.
- Формат даты транзакции в проекте: `YYYY-MM-DD`.
- Каталог `image/` должен содержать изображения для корректной работы галереи.
- Реализация ориентирована на учебные цели и не использует базу данных.
