#!/usr/bin/env bash

VERSION=latest
AWS_ACCOUNT_ID=243992253407
AWS_REGION=eu-west-1
SOURCE_NAMESPACE=skytsar
DEST_NAMESPACE=autoq

declare -a images=("nginx" "phpfpm-phalcon" "autoq-basephp" "postfix" "postgres" "mysql")

aws configure set default.region ${AWS_REGION}

# Authenticate against our Docker registry
eval $(aws ecr get-login)

# Push images to ECR

for image in "${images[@]}"
do
  echo "Pushing: ${image}..."
  docker tag ${SOURCE_NAMESPACE}/${image}:${VERSION} ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${DEST_NAMESPACE}/${image}:${VERSION}
  docker push ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${DEST_NAMESPACE}/${image}:${VERSION}
  echo -e "DONE!\n"

done

