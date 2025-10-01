FROM mariadb:10.8

RUN chmod 0444 -R /etc/mysql/mariadb.conf.d/

RUN mkdir /etc/dev

COPY ./50config/50-client.cnf /etc/mysql/mariadb.conf.d/50-client.cnf

COPY ./50config/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf 
RUN chmod 0444 -R /etc/mysql/mariadb.conf.d/

COPY ./my.cnf /etc/mysql/conf.d/my.cnf 
RUN chmod 0444 -R /etc/mysql/conf.d/

RUN mkdir -p /backups

COPY ./docker-entrypoint.sh /docker-entrypoint-initdb.d/entrypoint.sh

RUN echo "0 3 * * * root mysqldump -u root -p\$MYSQL_ROOT_PASSWORD --all-databases | gzip > /backup/backup-\$(date +\%Y\%m\%d).sql.gz" > /etc/cron.d/mysql-backup \
    && chmod 0644 /etc/cron.d/mysql-backup

RUN apt-get update && apt-get -y install cron

CMD ["mysqld"]
