# Container image that runs your code
FROM php:8.3-cli

# Copies your code file from your action repository to the filesystem path `/` of the container
COPY . /usr/src/dev-tools
WORKDIR /usr/src/dev-tools

RUN ["chmod", "+x", "./entrypoint.sh"]

# Code file to execute when the docker container starts up (`entrypoint.sh`)
ENTRYPOINT ["./entrypoint.sh"]
