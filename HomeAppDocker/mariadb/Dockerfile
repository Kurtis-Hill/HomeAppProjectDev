FROM mariadb:10.8

RUN chmod 0444 -R /etc/mysql/mariadb.conf.d/

RUN mkdir /etc/dev

COPY ./50config/50-client.cnf /etc/mysql/mariadb.conf.d/50-client.cnf

COPY ./50config/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf 
RUN chmod 0444 -R /etc/mysql/mariadb.conf.d/

COPY ./my.cnf /etc/mysql/conf.d/my.cnf 
RUN chmod 0444 -R /etc/mysql/conf.d/

COPY ./docker-entrypoint.sh /docker-entrypoint-initdb.d/entrypoint.sh

CMD ["mysqld"]