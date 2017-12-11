# please build alpine-php image first

# git clone https://github.com/kimkit/alpine-php.git
# ./alpine-php/build.sh

FROM alpine-php

# please build binary first
# https://box-project.github.io/box2/

# box build

COPY serverd.phar /usr/local/bin/
CMD ["/usr/local/bin/serverd.phar", "start"]
EXPOSE 8000
