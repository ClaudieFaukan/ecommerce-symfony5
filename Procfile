web: heroku-php-apache2 public/
release: php bin/console doctrine:migrations:migrate --no-interraction
release: php bin/console doctrine:fixtures:load --no-interaction