# Makefile for Docker Compose management
include .env
export $(shell sed 's/=.*//' .env)

.PHONY: help up down build rebuild restart \
        shell-nginx shell-php shell-db \
        logs-nginx logs-php logs-db \
        composer \
        cli

# Common Composer flags
COMPOSER_FLAGS = --no-interaction --no-ansi

help:
	@echo "Available commands:"
	@echo "  up            - Start all containers in detached mode"
	@echo "  down          - Stop and remove all containers"
	@echo "  build         - Build or rebuild services"
	@echo "  rebuild       - Force rebuild of all containers"
	@echo "  restart       - Restart all containers"
	@echo "  shell-nginx   - Access nginx container shell"
	@echo "  shell-php     - Access php-fpm container shell"
	@echo "  shell-db   - Access db container shell"
	@echo "  logs-nginx    - View nginx container logs"
	@echo "  logs-php      - View php-fpm container logs"
	@echo "  logs-db    - View db container logs"
	@echo "  composer      - Run composer command"
	@echo "  cli           - Run php cli command"
	@echo "  test          - Run tests"

## Docker commands
up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

rebuild:
	docker compose build --no-cache --force-rm

restart: down up

## Shell access
shell-nginx:
	docker compose exec nginx sh

shell-php:
	docker compose exec php-fpm sh

shell-db:
	docker compose exec -e PGPASSWORD="${DATABASE_USER_PASSWORD}" db psql -U ${DATABASE_USER} -d ${DATABASE_NAME}

## Logs
logs-nginx:
	docker compose logs -f nginx

logs-php:
	docker compose logs -f php-fpm

logs-db:
	docker compose logs -f db

## Composer commands
composer:
	docker compose exec php-fpm composer $(filter-out $@,$(MAKECMDGOALS)) $(COMPOSER_FLAGS)

## CLI commands
cli:
	docker compose exec php-fpm php cli.php $(filter-out $@,$(MAKECMDGOALS))

## Testing
test:
	docker compose exec php-fpm php vendor/bin/phpunit

## Catch-all rule
%:
	@: