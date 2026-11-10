FROM redis:6.2.7-alpine

COPY ./redis.conf /usr/local/etc/redis/redis.conf

COPY ./sysctl.conf /etc/sysctl.conf

COPY ./docker-entrypoint.sh /usr/docker-entrypoint.sh
RUN chmod +x /usr/docker-entrypoint.sh

ENTRYPOINT ["/usr/docker-entrypoint.sh"]

 CMD [ "redis-server", "/usr/local/etc/redis/redis.conf" ]
