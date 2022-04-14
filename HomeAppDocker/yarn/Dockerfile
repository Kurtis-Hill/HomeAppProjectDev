FROM node:12

COPY docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

WORKDIR /home/node/app/src

ENTRYPOINT ["docker-entrypoint"]

CMD ["yarn"]
