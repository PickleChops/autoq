#!/usr/bin/env bash

curl -s --data-binary "@../asap_job.yaml" http://autoq.localdev/jobs/ | jq .data.id
curl -s --data-binary "@../example_job_1.yaml" http://autoq.localdev/jobs/ | jq .data.id
curl -s --data-binary "@../example_job_2.yaml" http://autoq.localdev/jobs/ | jq .data.id