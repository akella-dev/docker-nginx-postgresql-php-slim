# Docker окружение для PHP-проектов с Nginx и PostgreSQL

## 🚀 Особенности

- 🐳 Docker-контейнеры для Nginx, PHP-FPM и PostgreSQL
- 🔧 Готовые конфиги для быстрого старта
- 📦 Автоматическая установка зависимостей через Composer
- 🔒 Безопасное хранение переменных окружения

## ⚙️ Требования

- Docker 20.10+
- Docker Compose 2.0+

## 🛠 Установка

1. Склонируйте репозиторий:
   ```bash
   git clone https://github.com/akella-dev/docker-nginx-postgresql-php-slim.git
   cd docker-nginx-postgresql-php-slim
   ```
2. Настройте окружение:
   ```bash
   cp .env.example .env
   ```
3. Запустите контейнеры:
   ```bash
   docker compose up -d
   ```

## 🖥 Использование

- Доступ к приложению: http://localhost:8088
- Доступ к PostgreSQL:
  - Хост: `localhost`
  - Порт: `5432`
  - Пользователь: `root`
  - Пароль: указан в `.env`

## ⚙️ Конфигурация

Основные переменные окружения в `.env`:

```ini
PUBLIC_PORT=8088
DATABASE_NAME=akella-db
DATABASE_USER=akella-user
DATABASE_PASS=secret
```

## 🛠 Команды Makefile

- `make start` - запуск контейнеров
- `make stop` - остановка контейнеров
- `make logs` - просмотр логов

## 📂 Структура проекта

```
├── docker/             # Docker конфигурации
├── src/                # Исходный код PHP
└── public/index.php    # Входная точка приложения
```

## 🚀 REST API

---

#### Утилиты

---

##### Проверка работоспособности системы

<details>
 <summary>
 <code>GET</code>
 <code><b>/api/health</b></code>
 </summary>

###### Ответ

```json
{
  "status": true,
  "result": "ok"
}
```

</details>

---

#### Авторизация

---

##### Получение текущий сессии авторизации (юзера)

<details>
 <summary>
 <code>GET</code>
 <code><b>/api/auth/get</b></code>
 </summary>

###### Ответ

```json
{
  "status": true,
  "result": {
    "id": 2,
    "name": "user-1",
    "role": "USER",
    "avatar": null,
    "created_at": "2025-05-27 20:20:02",
    "updated_at": "2025-05-27 20:20:02"
  }
}
```

</details>

##### Войти

<details>
 <summary>
 <code>POST</code>
 <code><b>/api/auth/login</b></code>
 </summary>

###### Запрос

> | name     | type     | data type | description |
> | -------- | -------- | --------- | ----------- |
> | login    | required | string    | Логин       |
> | password | required | string    | Пароль      |

###### Ответ

```json
{
  "status": true,
  "result": {
    "id": 2,
    "name": "user-1",
    "role": "USER",
    "avatar": null,
    "created_at": "2025-05-27 20:20:02",
    "updated_at": "2025-05-27 20:20:02"
  }
}
```

</details>

##### Выйти

<details>
 <summary>
 <code>POST</code>
 <code><b>/api/auth/logout</b></code>
 </summary>

###### Ответ

```json
{
  "status": true,
  "result": "Successful logout"
}
```

</details>

---

#### Управление пользователями

---

##### Создание пользователя

<details>
 <summary>
 <code>POST</code>
 <code><b>/api/users</b></code>
 </summary>

###### Запрос

> | name     | type     | data type | description |
> | -------- | -------- | --------- | ----------- |
> | name     | required | string    | Имя         |
> | login    | required | string    | Логин       |
> | password | required | string    | Пароль      |

###### Ответ

```json
{
  "id": 1,
  "name": "New User",
  "login": "newuser",
  "created_at": "2025-05-27 20:20:02"
}
```

</details>

##### Получение списка пользователей

<details>
 <summary>
 <code>GET</code>
 <code><b>/api/users</b></code>
 </summary>

###### Ответ

```json
[
  {
    "id": 1,
    "name": "User 1",
    "login": "user1",
    "created_at": "2025-05-27 20:20:02"
  }
]
```

</details>

##### Обновление пользователя

<details>
 <summary>
 <code>PATCH</code>
 <code><b>/api/users/{id}</b></code>
 </summary>

###### Параметры

> | name | type     | data type | description     |
> | ---- | -------- | --------- | --------------- |
> | id   | required | integer   | ID пользователя |

###### Запрос

> | name     | type     | data type | description  |
> | -------- | -------- | --------- | ------------ |
> | name     | optional | string    | Новое имя    |
> | login    | optional | string    | Новый логин  |
> | password | optional | string    | Новый пароль |

###### Ответ

```json
{
  "id": 1,
  "name": "Updated Name",
  "login": "updated_login",
  "created_at": "2025-05-27 20:20:02"
}
```

</details>

##### Удаление пользователя

<details>
 <summary>
 <code>DELETE</code>
 <code><b>/api/users/{id}</b></code>
 </summary>

###### Параметры

> | name | type     | data type | description     |
> | ---- | -------- | --------- | --------------- |
> | id   | required | integer   | ID пользователя |

###### Ответ

```
204 No Content
```

</details>

---
