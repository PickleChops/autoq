
up:
	$(MAKE) build
	docker-compose up -f base.yml -d

stop:
	docker-compose up -f base.yml stop

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

.PHONY: up stop logs build push deploy

