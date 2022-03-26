#!/bin/bash

source common.sh

cd $APACHE_DIR

rm -rfv "sites-available/*"
rm -rfv "sites-enabled/*"
rm -rfv ports.conf

mkdir sites-available
mkdir sites-enabled

cp "$WEB_ROOT/sh/conf/apache2.conf" .

chown -R www-data:www-data $APACHE_DIR
chmod -R 0755 $APACHE_DIR

a2enmod rewrite
a2enmod ssl

go_back
remind_user "Run ./index-sites.sh && ./install-sites.sh"