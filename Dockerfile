# Container image that runs your code
FROM php:8.5-cli

RUN DEBIAN_FRONTEND=noninteractive apt update && apt install -y git zip unzip ca-certificates curl gnupg

# Use bash for the shell
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

# install nvm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash

# set env
ENV NVM_DIR=/root/.nvm

# install node
RUN bash -c "source $NVM_DIR/nvm.sh && nvm install 20"

# set cmd to bash
CMD ["/bin/bash"]

# Copies your code file from your action repository to the filesystem path `/` of the container
COPY . /usr/src/dev-tools
WORKDIR /usr/src/dev-tools

RUN ["chmod", "+x", "./entrypoint.sh"]

# Code file to execute when the docker container starts up (`entrypoint.sh`)
ENTRYPOINT ["/usr/src/dev-tools/entrypoint.sh"]
