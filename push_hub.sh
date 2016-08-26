#!/usr/bin/env bash

VERSION=latest
SOURCE_NAMESPACE=skytsar

declare -a images=("nginx" "phpfpm-phalcon" "autoq-basephp" "postfix" "postgres" "mysql")

# Push images to docker hub

for image in "${images[@]}"
do
  echo "Pushing: ${image}..."
  docker push ${SOURCE_NAMESPACE}/${image}:${VERSION}
  echo -e "DONE!\n"

done

