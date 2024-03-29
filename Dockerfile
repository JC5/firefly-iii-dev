# Container image that runs your code
FROM php:8.3-cli

RUN apt update && apt install -y git zip unzip ca-certificates curl gnupg
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
RUN apt update && apt install nodejs -y

RUN curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarnkey.gpg >/dev/null
RUN echo "deb [signed-by=/usr/share/keyrings/yarnkey.gpg] https://dl.yarnpkg.com/debian stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt update
RUN apt -y install yarn


# Copies your code file from your action repository to the filesystem path `/` of the container
COPY . /usr/src/dev-tools
WORKDIR /usr/src/dev-tools

RUN ["chmod", "+x", "./entrypoint.sh"]

# Code file to execute when the docker container starts up (`entrypoint.sh`)
ENTRYPOINT ["/usr/src/dev-tools/entrypoint.sh"]
