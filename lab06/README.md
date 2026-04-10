# Отчет по лабораторной работе №6

### Voronetchii Stanislav IA2404 USM

## Описание лабораторной работы
Лабораторная работа посвящена обработке и валидации HTML-форм в PHP.  
В проекте реализована система добавления банковских транзакций через веб-форму с серверной проверкой данных, сохранением в JSON-файл и выводом записей в HTML-таблице с сортировкой.

Цель работы: освоить отправку данных методом `POST`, работу с `$_POST`, серверную валидацию, чтение и запись файлов, а также безопасный вывод пользовательских данных.

Для дополнительной оценки реализованы:
- ООП-структура приложения;
- интерфейс `ValidatorInterface` для унификации валидаторов.

## Инструкции по запуску проекта
1. Проверить, что установлен PHP 8+:
```bash
php -v
```
2. Перейти в каталог лабораторной:
```bash
cd /Users/MAC/VSC/php_labs/lab06
```
3. Запустить встроенный сервер PHP:
```bash
php -S localhost:8000
```
4. Открыть страницу в браузере:
- `http://localhost:8000/index.php`

## Краткая документация к проекту
Структура проекта:
- `index.php` — точка входа, настройка правил валидации, создание объектов и HTML-страница.
- `src/Form/TransactionForm.php` — класс управления формой.
- `src/Storage/TransactionStorage.php` — класс для чтения и записи `JSON`.
- `src/Validation/ValidatorInterface.php` — интерфейс валидаторов.
- `src/Validation/FormValidator.php` — класс запуска валидации по правилам.
- `src/Validation/RequiredValidator.php` — проверка обязательного поля.
- `src/Validation/StringLengthValidator.php` — проверка длины строки.
- `src/Validation/DateValidator.php` — проверка даты.
- `src/Validation/NumericRangeValidator.php` — проверка числового диапазона.
- `src/Validation/InArrayValidator.php` — проверка значения по списку.
- `data/transactions.json` — файл хранения транзакций.

Модель данных транзакции:
- `id` — уникальный идентификатор;
- `transaction_date` — дата транзакции;
- `amount` — сумма;
- `merchant` — контрагент;
- `category` — категория;
- `type` — тип транзакции: `income` или `expense`;
- `description` — описание;
- `is_recurring` — регулярная транзакция (`checkbox`);
- `created_at` — дата и время создания записи.

Основные классы:
- `TransactionForm` — принимает `POST`, нормализует поля, валидирует данные, сохраняет ошибки в сессию и добавляет запись.
- `FormValidator` — последовательно запускает валидаторы для каждого поля.
- `TransactionStorage` — загружает все транзакции, сохраняет их и вычисляет следующий `id`.
- `ValidatorInterface` — общий контракт для всех валидаторов.

## Примеры использования проекта
Создание валидатора формы:
```php
$validator = new FormValidator([
    'transaction_date' => [
        new RequiredValidator('Укажите дату транзакции.'),
        new DateValidator('Дата должна быть в формате YYYY-MM-DD.'),
    ],
    'amount' => [
        new RequiredValidator('Укажите сумму транзакции.'),
        new NumericRangeValidator(0.01, 1000000.0, 'Сумма должна быть числом от 0.01 до 1000000.'),
    ],
]);
```

Создание объектов приложения:
```php
$storage = new TransactionStorage(__DIR__ . '/data/transactions.json');
$form = new TransactionForm($validator, $storage);
$form->handleRequest();
```

Добавление записи в JSON:
```php
$storage->add([
    'id' => $storage->nextId(),
    'transaction_date' => $input['transaction_date'],
    'amount' => round((float) $input['amount'], 2),
    'merchant' => $input['merchant'],
    'category' => $input['category'],
    'type' => $input['type'],
    'description' => $input['description'],
    'is_recurring' => $input['is_recurring'] === '1',
    'created_at' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
]);
```

Получение отсортированного списка:
```php
$transactions = $form->sortedTransactions($sortField, $sortDirection);
```

Пример ссылки сортировки:
```text
http://localhost:8000/index.php?sort=amount&direction=desc
```

Пример записи в `data/transactions.json`:
```json
{
    "id": 1,
    "transaction_date": "2026-04-10",
    "amount": 250.75,
    "merchant": "Test Shop",
    "category": "food",
    "type": "expense",
    "description": "Тестовая транзакция для проверки ООП-версии.",
    "is_recurring": true,
    "created_at": "2026-04-10T07:17:18+00:00"
}
```

## Ответы на контрольные вопросы
1. Какие существуют методы отправки данных из формы на сервер? Какие методы поддерживает HTML-форма?

HTML-форма обычно поддерживает методы GET и POST. 

Метод GET тоже отправляет данные на сервер, 
но передает их через URL, поэтому он подходит для поиска, фильтрации и сортировки. 

Метод POST передает данные в теле запроса и обычно
используется для создания или изменения данных

2. Какие глобальные переменные используются для доступа к данным формы в PHP?

В PHP для доступа к данным формы используются суперглобальные массивы `$_GET`, `$_POST` и `$_REQUEST`. В этой лабораторной данные формы читаются через `$_POST`, а параметры сортировки таблицы — через `$_GET`.

3. Как обеспечить безопасность при обработке данных из формы (например, защититься от XSS)?

Для защиты от XSS необходимо экранировать пользовательские данные перед выводом в HTML. В проекте для этого используется функция `htmlspecialchars`. Также важно валидировать входные данные на сервере, проверять формат даты, диапазон числа и допустимые значения полей с ограниченным набором вариантов.

## Список использованных источников
- https://www.php.net/manual/ru/
- https://www.php.net/manual/ru/reserved.variables.post.php
- https://www.php.net/manual/ru/function.htmlspecialchars.php
- https://www.php.net/manual/ru/book.session.php
- https://metanit.com/php/tutorial/

## Дополнительные важные аспекты
- В проекте используется `declare(strict_types=1);`.
- Обработка формы реализована по схеме Post/Redirect/Get.
- Ошибки валидации и старые значения формы сохраняются в `$_SESSION`.
- Все пользовательские значения выводятся через `htmlspecialchars`.
- Данные сохраняются в читаемом формате `JSON`.
- Код задокументирован с помощью `PHPDoc`.
