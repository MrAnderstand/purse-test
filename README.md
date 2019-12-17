<p align="center">
    <h1 align="center">Интернет кошелек. Тестовое задание</h1>
    <br>
</p>



ТРЕБОВАНИЯ
------------

PHP >7.3

Postgresql >10

nginx


УСТАНОВКА
------------
Пример для Linux

Установка зависимостей
~~~
composer install
~~~

КОНФИГУРАЦИЯ
-------------

### Database

Отредактируйте файл `config/db.php` (`config/test_db.php` для тестового окружения) для получения доступа к базе данных

### Nginx

Пример конфига nginx для локального виртуального хоста `config/purse-test.conf`

Запуск миграций
~~~
./yii migrate
~~~

Тестирование API с пересозданием DB
~~~
./test.sh
~~~
Без пересоздания
~~~
./test.sh 1
~~~

Примеры использования API

Получение баланса:
```javascript
$.ajax({
    url: '/api/balance',
    data: {
        purseId: 1,
    },
    method: 'GET',
    dataType: 'JSON'
});
```

Изменение баланса:
```javascript
$.ajax({
    url: '/api/balance',
    data: {
        purseId: 1,
        transactionType: 'debit',
        transactionReason: 'stock',
        transactionRate: 'RUB',
        amount: 1,
    },
    method: 'POST',
    dataType: 'JSON'
});
```

SQL Для тестового задания:

```sql
SELECT SUM(
    CASE tt.name = 'debit' 
        WHEN true THEN
            round(t.amount*t.actual_rate, 2)
        ELSE
            -round(t.amount*t.actual_rate, 2)
    END
) amount
FROM public.transaction t
INNER JOIN public.transaction_reason tr ON t.reason_id = tr.id
INNER JOIN public.transaction_type tt ON t.type_id = tt.id
WHERE (t.created_at BETWEEN (now()::date - '6d'::interval) AND now()) AND tr.name = 'refund'
```
