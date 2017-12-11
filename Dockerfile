# please build alpine-php image first

# git clone https://github.com/kimkit/alpine-php.git
# ./alpine-php/build.sh

FROM alpine-php

COPY . /app
CMD ["/app/bin/serverd", "start"]
EXPOSE 8000
