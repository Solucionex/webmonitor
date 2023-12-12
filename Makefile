.PHONY = ps up down restart ssh docker logs cleanall console composer require

# docker commands
ps:
	@docker-compose -f docker-compose.dev.yml ps -a
up:
	@docker-compose -f docker-compose.dev.yml up -d --build --force-recreate --remove-orphans
	@docker-compose -f docker-compose.dev.yml exec app service php8.2-fpm start
down:
	@docker-compose -f docker-compose.dev.yml down
restart:
	@docker-compose -f docker-compose.dev.yml restart
	@docker-compose -f docker-compose.dev.yml exec app service php8.2-fpm start
ssh:
	@docker-compose -f docker-compose.dev.yml exec $(filter-out $@,$(MAKECMDGOALS)) bash
docker:
	@docker-compose -f docker-compose.dev.yml $(filter-out $@,$(MAKECMDGOALS))
logs:
	@docker-compose -f docker-compose.dev.yml logs $(filter-out $@,$(MAKECMDGOALS))
cleanall:
	@docker-compose -f docker-compose.dev.yml down --remove-orphans
	@docker ps -aq | xargs -r docker rm
	@docker volume ls -q | xargs -r docker volume rm


# app service commands
console:
	@docker-compose -f docker-compose.dev.yml exec app php bin/console $(filter-out $@,$(MAKECMDGOALS))

composer:
	@docker-compose -f docker-compose.dev.yml exec app composer $(filter-out $@,$(MAKECMDGOALS))

require:
	@docker-compose -f docker-compose.dev.yml exec app composer require $(filter-out $@,$(MAKECMDGOALS))

create:
	@docker-compose -f docker-compose.dev.yml exec app php bin/console make:$(filter-out $@,$(MAKECMDGOALS))

%:
	@: