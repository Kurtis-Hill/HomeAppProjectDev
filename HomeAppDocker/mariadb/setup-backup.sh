#!/bin/bash
set -e

# Create cron job for daily backup at 3 AM
echo "0 3 * * * root mysqldump -u root -p${MYSQL_ROOT_PASSWORD} --all-databases | gzip > /backup/backup-\$(date +\%Y\%m\%d).sql.gz" > /etc/cron.d/mysql-backup
chmod 0644 /etc/cron.d/mysql-backup

# Install cron if needed
apt-get update && apt-get -y install cron

# Start cron service
service cron start || echo "Could not start cron, it might be running already"
