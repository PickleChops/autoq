FROM golang:latest

#Add tree it helps me understand Go paths
#Add phalcon from source
RUN apt-get update && apt-get install -y tree \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*