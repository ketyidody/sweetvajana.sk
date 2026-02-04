.PHONY: help build up down dev logs shell migrate seed fresh test pint

# Default target
help:
	@echo "SweetVajana Docker Commands"
	@echo ""
	@echo "Setup:"
	@echo "  make build     - Build Docker images"
	@echo "  make up        - Start production containers (nginx + php-fpm)"
	@echo "  make dev       - Start development containers (hot reload)"
	@echo "  make down      - Stop all containers"
	@echo ""
	@echo "Database:"
	@echo "  make migrate   - Run database migrations"
	@echo "  make seed      - Seed the database"
	@echo "  make fresh     - Fresh migrate and seed"
	@echo ""
	@echo "Development:"
	@echo "  make shell     - Open shell in app container"
	@echo "  make logs      - View container logs"
	@echo "  make test      - Run tests"
	@echo "  make pint      - Run code formatter"

# Docker commands
build:
	docker compose build

up:
	docker compose up -d

dev:
	docker compose --profile dev up

down:
	docker compose down

logs:
	docker compose logs -f

shell:
	docker compose exec app bash

# Database commands
migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

fresh:
	docker compose exec app php artisan migrate:fresh --seed

# Development commands
test:
	docker compose exec app php artisan test

pint:
	docker compose exec app ./vendor/bin/pint
