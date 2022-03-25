#!/bin/bash

source common.sh
warn_user "Initialize the Apache webserver"

if [[ $DO_SCRIPT -eq 1 ]];
then

    cd $APACHE_DIR

    rm -rf sites-available
    rm -rf sites-enabled
    rm -rf ports.conf

    mkdir sites-available
    mkdir sites-enabled

    cp "$WEB_ROOT/sh/conf/apache2.conf" .

    chown -R www-data:www-data $APACHE_DIR
    chmod -R 0755 $APACHE_DIR

    a2enmod rewrite
    a2enmod ssl

    go_back
    remind_user "Run ./index-sites.sh && ./install-sites.sh"

fi