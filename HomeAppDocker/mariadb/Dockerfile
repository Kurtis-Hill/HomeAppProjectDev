FROM mariadb:10.6.2

RUN chmod 0444 -R /etc/mysql/mariadb.conf.d/

RUN mkdir /etc/dev

COPY ./50config/50-client.cnf /etc/mysql/mariadb.conf.d/50-client.cnf

COPY ./50config/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf 
RUN chmod 0444 -R /etc/mysql/mariadb.conf.d/

COPY ./my.cnf /etc/mysql/conf.d/my.cnf 
RUN chmod 0444 -R /etc/mysql/conf.d/

COPY ./docker-entrypoint.sh /docker-entrypoint-initdb.d/docker-entrypoint
RUN chmod +x /docker-entrypoint-initdb.d/docker-entrypoint

# COPY ./docker-entrypoint.sh /usr/bin/docker-entrypoint.sh

# RUN chmod +x /usr/bin/docker-entrypoint.sh

# ENTRYPOINT ["docker-entrypoint"]

CMD ["mysqld"]