#!/bin/sh

if [ ${APP_ENV} = 'dev' ]; then
    echo "dev container build"
    echo "Creating test Database and test user..."
    mysql -u root -p"$MYSQL_ROOT_PASSWORD" --execute \
    "CREATE DATABASE IF NOT EXISTS $MARIADB_TEST_DATABASE;
    CREATE USER IF NOT EXISTS $MARIADB_TEST_USER IDENTIFIED BY '$MARIADB_TEST_PASSWORD';
    GRANT ALL PRIVILEGES ON $MARIADB_TEST_DATABASE.* TO '$MARIADB_TEST_USER'@'%';
    FLUSH PRIVILEGES;"
    echo "...Finished creating test Database and test user"
fi
