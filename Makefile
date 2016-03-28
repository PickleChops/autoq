
up:
	$(MAKE) build
	docker-compose -f base.yml up -d

stop:
	docker-compose -f base.yml stop

clean:
	$(MAKE) stop
	docker-compose -f base.yml rm -f

build:
	docker-compose -f base.yml build

push:
	docker login --username=skytsar
	docker push skytsar/nginx
	docker push skytsar/phpfpm-phalcon

deploy:
	$(MAKE) stop
	$(MAKE) build
	$(MAKE) push
	./deploy.sh

.PHONY: up stop build push deploy

