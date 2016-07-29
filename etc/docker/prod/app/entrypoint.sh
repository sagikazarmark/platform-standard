#!/bin/sh

set -e

export SYMFONY__INSTALLED=$(php -r "echo date('c');")
bin/console cache:clear
chmod -R 777 var/cache/ var/logs/ var/sessions/ web/media/ web/uploads/
rm -rf public/*
cp -r web/* public/

case "$1" in
    "php-fpm")
        exec php-fpm ;;
    "")
        exec php-fpm ;;
    "migrate")
        exec bin/console doctrine:migrations:migrate ;;
    "worker")
        exec bin/console hotfix:jms-job-queue:run ;;
    *)
        exec "$@" ;;
esac
