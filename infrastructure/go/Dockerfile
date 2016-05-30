FROM golang:latest

#Add tree it helps me understand Go paths
RUN apt-get update && apt-get install -y tree \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*