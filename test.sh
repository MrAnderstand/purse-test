#!/bin/bash

# Не выполнять миграции, не пересоздавать структуру БД
noMigrate=$1

if [ -z "$noMigrate" ]; then
    # Создадим новую структуру тестовой БД
    tests/bin/yii migrate/fresh
fi

# Выполним тестирование API
vendor/bin/codecept run api
