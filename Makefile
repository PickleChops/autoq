#Default to dev environment
ifndef COMPOSE_CONFIG_FILES
	COMPOSE_CONFIG_FILES := -f base.yml -f dev.yml
endif

up:
	$(MAKE) build
	docker-compose $(COMPOSE_CONFIG_FILES) up -d

stop:
	docker-compose $(COMPOSE_CONFIG_FILES) stop

clean:
	$(MAKE) stop
	docker-compose $(COMPOSE_CONFIG_FILES) rm -f

build:
	docker-compose $(COMPOSE_CONFIG_FILES) build

push:
	docker login --username=skytsar
	docker push skytsar/nginx
	docker push skytsar/phpfpm-phalcon

tests:
	docker-compose $(COMPOSE_CONFIG_FILES) run  --rm --no-deps utils ./vendor/phpunit/phpunit/phpunit

util:
	docker-compose $(COMPOSE_CONFIG_FILES) run --rm --no-deps utils bash

compose:
	docker-compose $(COMPOSE_CONFIG_FILES) run --rm --no-deps utils composer install

deploy:
	$(MAKE) stop
	$(MAKE) build
	$(MAKE) push
	./deploy.sh

scheduler_logs:
	-$(call show_logs,scheduler)

logs:
	-$(call show_logs)

#function for showing logs in a container
define show_logs
	docker-compose $(COMPOSE_CONFIG_FILES) logs $1
endef

.PHONY: up stop build push deploy cli tests compose

