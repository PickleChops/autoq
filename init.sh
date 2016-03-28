#!/bin/bash

#Set up env for Go
export GOPATH=$(PWD)/app/autoqctl
export PATH=$PATH:${GOPATH}/bin
export GO15VENDOREXPERIMENT=1


