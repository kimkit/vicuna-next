FROM registry.cn-hangzhou.aliyuncs.com/wuwenbin/alpine-php:0.1.0

COPY . /app
CMD ["/app/bin/serverd", "start"]
EXPOSE 8000
