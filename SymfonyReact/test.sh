#  cd /var/www/html/ && php bin/console dbal:run-sql "select * from user limit 1" --env=test
# php bin/console dbal:run-sql "select firstName from user where firstName = 'admin' limit 1" --env=test
 if ! php bin/console dbal:run-sql "select firstName from user where firstName = 'admin' limit 1" --env=test | grep -q 'array(1)'; then
  echo "matched"
fi
