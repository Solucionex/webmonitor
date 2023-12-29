.PHONY = ps up down restart ssh docker logs cleanall console composer require

include .env

DOCKER_COMPOSE := $(shell command -v docker-compose 2> /dev/null)
ifeq ($(DOCKER_COMPOSE),)
    DOCKER_COMPOSE := docker compose -f docker-compose.$(ENV).yml
else
    DOCKER_COMPOSE := docker-compose -f docker-compose.$(ENV).yml
endif

# docker commands
ps:
	@$(DOCKER_COMPOSE)  ps -a
up:
	@$(DOCKER_COMPOSE) up -d --build --force-recreate --remove-orphans
	@$(DOCKER_COMPOSE) exec app service php8.2-fpm start
down:
	@$(DOCKER_COMPOSE) down
restart:
	@$(DOCKER_COMPOSE) restart
	@$(DOCKER_COMPOSE) exec app service php8.2-fpm start
ssh:
	@$(DOCKER_COMPOSE) exec $(filter-out $@,$(MAKECMDGOALS)) bash
docker:
	@$(DOCKER_COMPOSE) $(filter-out $@,$(MAKECMDGOALS))
logs:
	@$(DOCKER_COMPOSE) logs $(filter-out $@,$(MAKECMDGOALS))
cleanall:
	@$(DOCKER_COMPOSE) down --remove-orphans
	@docker ps -aq | xargs -r docker rm
	@docker volume ls -q | xargs -r docker volume rm


# app service commands
console:
	@$(DOCKER_COMPOSE) exec app php bin/console $(filter-out $@,$(MAKECMDGOALS))

composer:
	@$(DOCKER_COMPOSE) exec app composer $(filter-out $@,$(MAKECMDGOALS))

require:
	@$(DOCKER_COMPOSE) exec app composer require $(filter-out $@,$(MAKECMDGOALS))

create:
	@$(DOCKER_COMPOSE) exec app php bin/console make:$(filter-out $@,$(MAKECMDGOALS))

%:
	@: