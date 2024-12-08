# Users-Securitm

<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
    </a>
</p>

## О проекте

**Users-Securitm** — это приложение, реализующее тестовое задание компании Securitm, с использованием фреймворка Laravel 9, Vue.js и Inertia.js. Приложение предназначено для управления пользователями, обеспечивая создание, редактирование, удаление и просмотра списка пользователей с поддержкой пагинации, сортировки и поиска, как через restAPI, так и через веб-интерфейс.

## Требования

Перед установкой убедитесь, что на вашем компьютере установлены следующие компоненты:

- **PHP**: версия 8.0 или выше
- **Composer**: 1.10 или выше
- **Node.js**: 14.x или выше
- **NPM** или **Yarn**
- **База данных**: MySQL
- **Git**

## Установка

### 1. Клонирование репозитория

```bash
git clone https://github.com/ilg2005/users-securitm.git
```

### 2. Переход в директорию проекта

```bash
cd users-securitm
```

### 3. Установка PHP зависимостей


```bash
composer install
```

### 4. Установка JavaScript зависимостей


```bash
npm install
```

*или*

```bash
yarn install
```

### 5. Настройка файла окружения

Создайте копию файла `.env.example` и назовите ее `.env`:

```bash
cp .env.example .env
```

Откройте файл `.env` в текстовом редакторе и настройте параметры подключения к базе данных и другие необходимые настройки:

```
APP_NAME=Users-Securitm
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

VITE_PUSHER_APP_KEY=your_pusher_key
VITE_PUSHER_APP_CLUSTER=mt1
```

### 6. Генерация ключа приложения

Создайте ключ приложения:

```bash
php artisan key:generate
```

### 7. Выполнение миграций базы данных

Предварительно создайте базу данных Mysql, затем выполните миграции:

```bash
php artisan migrate --seed
```

### 8. Сборка фронтенда

Соберите приложение для dev:

```bash
npm run dev
```

*или для production:*

```bash
npm run build
```

### 9. Запуск сервера приложения

Запустите сервер Laravel:

```bash
php artisan serve
```

По умолчанию приложение будет доступно по адресу [http://localhost:8000](http://localhost:8000).
