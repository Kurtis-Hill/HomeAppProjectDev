FROM elasticsearch:8.4.3

COPY ./docker-entrypoint.sh /docker-entrypoint

ENTRYPOINT ["/docker-entrypoint", "-D", "FOREGROUND"]
