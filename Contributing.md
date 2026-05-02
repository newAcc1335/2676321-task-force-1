# TaskForce

## Установка

1. Клонируй репозиторий:

2. Установи зависимости:

```bash
   composer install
```

3. Создай базу данных:

```sql
   CREATE
DATABASE taskforce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Настрой подключение к БД в `config/db.php`:

5. Примени миграции (создаст все необходимые таблицы, добавит города и категории, создаст папку для хранения
   пользовательских файлов):

```bash
   php yii migrate
```

7. Запусти сервер и готово :rocket: