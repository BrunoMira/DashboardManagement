# Makefile

RED=\033[0;31m
GREEN=\033[0;32m
YELLOW=\033[0;33m
NC=\033[0m # No Color

CONTAINER_NAME := $(shell docker ps --format '{{.Names}}' | grep 'main-app')

default: help

help:
	@echo ""
	@echo "${RED}Uso: make [comando]${NC}"
	@echo ""
	@echo "Comandos disponíveis:"
	@echo ""
	@echo "${YELLOW}up${NC}           	: Inicia os containers"
	@echo "${YELLOW}down${NC}         	: Para os containers"
	@echo "${YELLOW}restart${NC}      	: Reinicia os containers"
	@echo "${YELLOW}console${NC}      	: Executa o comando 'attach' no container 'abra-app_main-app_1'"
	@echo "${YELLOW}build${NC}        	: Executa 'npm run build'"
	@echo ""
	@echo "${GREEN}migrate${NC}      	: Executa as migrações do Laravel"
	@echo "${GREEN}db-seed${NC}         : Executa as seeds do Laravel"
	@echo "${GREEN}db-seed-fresh${NC}   : Executa as migrações frescas e as seeds do Laravel"
	@echo "${GREEN}migration${NC}    	: Cria uma nova migração usando 'php artisan make:migration'"
	@echo "${GREEN}model${NC}        	: Cria um novo model usando 'php artisan make:model'"
	@echo "${GREEN}controller${NC}   	: Cria um novo controller usando 'php artisan make:controller'"
	@echo "${GREEN}route${NC}        	: Cria uma nova rota usando 'php artisan make:controller'"
	@echo "${GREEN}view${NC}         	: Cria uma nova view usando 'php artisan make:view'"
	@echo "${GREEN}seeder${NC}       	: Cria um novo seeder usando 'php artisan make:seeder'"
	@echo "${GREEN}cache-clear${NC}  	: Limpa o cache do Laravel"
	@echo "${GREEN}config-clear${NC} 	: Limpa o cache de configuração do Laravel"
	@echo "${GREEN}optimize-clear${NC}	: Limpa o cache de otimização do Laravel"
	@echo "${GREEN}route-list${NC}   	: Lista as rotas do Laravel"
	@echo "${GREEN}request${NC}      	: Cria um novo request usando 'php artisan make:request'"
	@echo ""
	@echo ""


build:
	@docker-compose build --no-cache

up:
	@docker-compose up

down:
	@docker-compose down

restart:
	@docker-compose down
	@docker-compose up

console:
	@docker exec -it $(CONTAINER_NAME) bash

console-root:
	@docker exec -u root -it $(CONTAINER_NAME) bash

migrate:
	@docker exec -it $(CONTAINER_NAME) php artisan migrate

db-seed:
	@docker exec -it $(CONTAINER_NAME) php artisan db:seed

migrate-fresh:
	@docker exec -it $(CONTAINER_NAME) php artisan migrate:fresh

migrate-fresh-seed:
	@docker exec -it $(CONTAINER_NAME) php artisan migrate:fresh --seed

migration:
	@docker exec -it $(CONTAINER_NAME) php artisan make:migration

model:
	@docker exec -it $(CONTAINER_NAME) php artisan make:model

controller:
	@docker exec -it $(CONTAINER_NAME) php artisan make:controller

seeder:
	@docker exec -it $(CONTAINER_NAME) php artisan make:seeder

route:
	@docker exec -it $(CONTAINER_NAME) php artisan make:route

view:
	@docker exec -it $(CONTAINER_NAME) php artisan make:view

npm-build:
	@docker exec -it $(CONTAINER_NAME) npm run build

cache-clear:
	@docker exec -it $(CONTAINER_NAME) php artisan cache:clear

config-clear:
	@docker exec -it $(CONTAINER_NAME) php artisan config:clear

optimize-clear:
	@docker exec -it $(CONTAINER_NAME) php artisan optimize:clear

route-list:
	@docker exec -it $(CONTAINER_NAME) php artisan route:list

request:
	@docker exec -it $(CONTAINER_NAME) php artisan make:request

test:
	@docker exec -it $(CONTAINER_NAME) php artisan test
