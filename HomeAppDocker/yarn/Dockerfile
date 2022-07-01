FROM node:18-alpine3.15

COPY docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

RUN apk add python3

WORKDIR /home/node/app/src

ENTRYPOINT ["docker-entrypoint"]

CMD ["yarn"]
