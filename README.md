Infopaginas
===========

Start project:
--------------

composer install

php app/console doctrine:schema:update --force

php app/console doctrine:fixtures:load

php app/console assets:install --symlink

php app/console assetic:dump --env=prod

php app/console cache:clear --env=prod


Admin panel access:
-------------------
admin / admin