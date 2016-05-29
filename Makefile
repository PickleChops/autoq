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
	docker-compose ${COMPOSE_CONFIG_FILES} run --rm --no-deps util ./vendor/phpunit/phpunit/phpunit --bootstrap ./bootstrap/testsStart.php Tests/

util:
	docker-compose $(COMPOSE_CONFIG_FILES) run --rm --no-deps util bash

deploy:
	$(MAKE) stop
	$(MAKE) build
	$(MAKE) push
	./deploy.sh

.PHONY: up stop build push deploy cli tests

