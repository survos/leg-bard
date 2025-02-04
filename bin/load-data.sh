dbname=bard

#heroku pg:reset --confirm jufj-scorecard  && echo "CREATE EXTENSION pg_trgm; CREATE EXTENSION \"uuid-ossp\";  CREATE EXTENSION hstore;" | heroku pg:psql
#echo "create user main WITH ENCRYPTED PASSWORD 'main';" | sudo -u postgres psql

echo "drop database if exists $dbname; create database $dbname; grant all privileges on database $dbname to main; " | sudo -u postgres psql
#echo "CREATE EXTENSION pg_trgm;" | sudo -u postgres psql -d $dbname
#echo "CREATE EXTENSION hstore;" | sudo -u postgres psql -d $dbname
echo "database $dbname now ready for migrations or restore"

git clean -f migrations/V*.php
bin/console make:migration && bin/console doctrine:migrations:migrate
#bin/console doctrine:migrations:diff  --configuration config/migrations/app_migrations.yaml  --em=default
#bin/console doctrine:migrations:migrate  --configuration config/migrations/app_migrations.yaml  --em=default

#bin/console doctrine:schema:update --dump-sql --em=default
#bin/console make:migration
#bin/console doctrine:migrations:migrate -n
#bin/console hautelook:fixtures:load -n
#bin/create-admins.sh


