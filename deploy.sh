#! /bin/bash

#A basic deploy to AWS ECS of a single container

#The ECS task 
TASK_FAMILY=nginx-placeholder

#The ECS service
SERVICE_NAME=holding-page-web

#The AWS credentials to use

if [[ -z "${1}" ]]
then
    AWS_PROFILE='ou'
else
    AWS_PROFILE="${1}"
fi

echo "Deploying to AWS profile: ${AWS_PROFILE}"

echo -e "\tGetting running task definition"

TASK_DEFINITION=$(aws --profile ${AWS_PROFILE} ecs describe-task-definition --task-definition ${TASK_FAMILY} | jq '.taskDefinition.containerDefinitions')
echo -e "\tFound task definition with image $(echo ${TASK_DEFINITION} | jq -r '.[0].image')"

echo -e "\tRegistering new task definition for task family ${TASK_FAMILY}"
TASK_REGISTRATION=$(aws --profile ${AWS_PROFILE} ecs register-task-definition --family ${TASK_FAMILY} --container-definitions "${TASK_DEFINITION}")

TASK_REVISION=$(echo ${TASK_REGISTRATION} | jq .taskDefinition.revision)
echo -e "\tNew registered task ${TASK_FAMILY}:${TASK_REVISION}"

echo -e "\tUpdating service"
SERVICE_UPDATE=$(aws --profile ${AWS_PROFILE} ecs update-service --service ${SERVICE_NAME} --task-definition "${TASK_FAMILY}:${TASK_REVISION}")

echo ${SERVICE_UPDATE} | jq '.service.status'


