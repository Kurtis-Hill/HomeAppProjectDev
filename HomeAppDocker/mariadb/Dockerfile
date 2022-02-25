FROM mariadb:10.6.2

RUN chmod 0444 -R /etc/mysql/mariadb.conf.d/

RUN mkdir /etc/dev

COPY ./docker-entrypoint.sh /docker-entrypoint-initdb.d/entrypoint.sh

CMD ["mysqld"]