# Blaz Movie

## Symfony API Requirements

- PHP >= 8.1
- Composer
- Symfony CLI
- PostgreSQL

## Installation

1. Clone the repository
2. Install dependencies
3. Configure the `.env.local` file
4. Create a database
5. Fill the database
6. Run the fixtures
7. Run the server
8. Open the API documentation

### 1. Clone the repository

```bash
git clone https://github.com/eristich/blaz-movie.git
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure the `.env.local` file

```bash
cp .env .env.local
```

```bash
# .env.local

APP_ENV=dev
APP_SECRET=<your_secret>
DATABASE_URL="postgresql://<USER>:<PASSWORD>@127.0.0.1:5432/blaz_movie?serverVersion=15&charset=utf8"
```

### 4. Create a database

```bash
php console doctrine:database:create
```

### 5. Fill the database

```bash
php bin/console doctrine:schema:update --force
```

### 6. Run the fixtures

```bash
php bin/console doctrine:fixtures:load
```

### 7. Run the server

```bash
symfony server:start
```

### 8. Open the API documentation

Go to http://localhost:8000/api/v1/doc