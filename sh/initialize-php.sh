#!/bin/bash

source common.sh
warn_user "Initialize PHP"

if [[ $DO_SCRIPT -eq 1 ]];
then

    cd $PHP_DIR

    rm -rf php.ini

    cp "$WEB_ROOT/sh/conf/php.ini" .

    chown -R www-data:www-data $PHP_DIR
    chmod -R 0755 $PHP_DIR

    go_back

fi