[Requirements](README.txt)

## Installation

Clone the repository:

```shell
git clone https://github.com/anwsonwsymous/simple-sql-builder.git && cd simple-sql-builder
```

Copy `.env.example`:

```shell
cp .env.example .env
```

Install composer:

```shell
docker run --rm -u $(id -u):$(id -g) -v $(pwd):/app composer install --ignore-platform-reqs
```

Build and start the Docker containers:

```shell
docker compose up --build -d
```

## Running Tests

```shell
docker compose exec app composer run test
```

or 

```shell
docker compose exec app vendor/bin/phpunit --configuration phpunit.xml
```

## Running Real Queries

```shell
docker compose exec app php src/real_test.php
```
