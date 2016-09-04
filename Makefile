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

schema:
	docker exec autoq_mysql_1 /bin/bash -c "mysqldump autoq -B --no-data -u root -pdev" | sed 's/ AUTO_INCREMENT=[0-9]*//g' > ./infrastructure/mysql/data/base.sql

compose:
	docker-compose $(COMPOSE_CONFIG_FILES) run --rm --no-deps utils composer install

deploy:
	$(MAKE) stop
	$(MAKE) build
	$(MAKE) push
	./deploy.sh

scheduler-logs:
	-$(call show_logs,scheduler)

runner-logs:
	-$(call show_logs,runner)

sender-logs:
	-$(call show_logs,sender)

logs:
	-$(call show_logs)

#function for showing logs in a container
define show_logs
	docker-compose $(COMPOSE_CONFIG_FILES) logs $1
endef

.PHONY: up stop build push deploy cli tests compose logs scheduler-logs

