web: heroku-php-apache2 public/ 
release: heroku-php bin/console doctrine:migrations:migrate --no-interraction
release: heroku-php bin/console doctrine:fixtures:load --no-interaction