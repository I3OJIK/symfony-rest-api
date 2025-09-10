# User API (Symfony)

REST API для работы с пользователями на Symfony 7.

---

## Установка

1. Клонируем репозиторий:

```bash
git clone https://github.com/I3OJIK/symfony-rest-api.git
cd symfony-rest-api
```

2. Запуск контейнеров:

```bash
docker compose up -d --build
```

3. Устанавливаем зависимости внутри PHP-контейнера:

```bash
docker exec -it symfony-php bash
composer install
```

4. Создаем файл `.env.local` и настраиваем подключение к базе данных:

```php
DATABASE_URL="mysql://symfony:password@mysql:3306/user_api?serverVersion=8.0&charset=utf8mb4"
```

5. Применяем миграции:

```bash
php bin/console doctrine:migrations:migrate
```

API будет доступен по адресу: `http://127.0.0.1:8080/api/users`

---

## Методы API

### 1. Создать пользователя

**POST** `/api/users`

Тело запроса (JSON):

```json
{
  "username": "user1",
  "password": "12345",
  "email": "user1@example.com"
}
```

Ответ:

```json
{
  "status": "success",
  "id": 1
}
```

---

### 2. Получить пользователя по ID

**GET** `/api/users/{id}`

Пример запроса:

```bash
curl -X GET http://127.0.0.1:8080/api/users/1
```

Ответ (успех):

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "username": "user1",
    "email": "user1@example.com"
  }
}
```

Ответ (ошибка):

```json
{
  "error": "Пользователь не найден"
}
```

---

### 3. Обновить пользователя

**PUT** `/api/users/{id}`

Тело запроса (JSON):

```json
{
  "username": "newuser",
  "password": "newpass",
  "email": "new@example.com"
}
```

Ответ:

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "username": "newuser",
    "email": "new@example.com"
  }
}
```

---

### 4. Удалить пользователя

**DELETE** `/api/users/{id}`

Пример запроса:

```bash
curl -X DELETE http://127.0.0.1:8080/api/users/1
```

Ответ (успех):

```json
{
  "status": "success"
}
```

Ответ (ошибка):

```json
{
  "error": "Пользователь не найден"
}
```

---

### 5. Логин пользователя

**POST** `/api/users/login`

Тело запроса (JSON):

```json
{
  "username": "user1",
  "password": "12345"
}
```

Ответ (успех):

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "username": "user1",
    "email": "user1@example.com"
  }
}
```

Ответ (ошибка):

```json
{
  "errors": "Неверный логин или пароль"
}
```

---
