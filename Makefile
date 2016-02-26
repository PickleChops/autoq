project_name=autoq

up:
	$(MAKE) build
	docker run -p 80:80 -d --name ${project_name} skytsar/ou-project-placeholder
	
stop:
	docker stop ${project_name} 2>/dev/null; true
	docker rm ${project_name} 2>/dev/null; true

clean:
	$(MAKE) stop
	docker rmi skytsar/ou-project-placeholder

logs:
	docker logs --tail=50 ${project_name}

build:
	docker rmi skytsar/ou-project-placeholder 2>/dev/null; true
	docker build -t="skytsar/ou-project-placeholder" .

push:
	docker login --username=skytsar
	docker push skytsar/ou-project-placeholder

deploy:
	$(MAKE) stop
	$(MAKE) build
	$(MAKE) push
	./deploy.sh

.PHONY: up stop logs build push deploy clean

