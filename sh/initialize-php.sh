#!/bin/bash

source common.sh

rm -rf $PHP_INI
cp "$WEB_ROOT/sh/conf/php.ini" $PHP_INI

chown www-data:www-data $PHP_INI
chmod 0755 $PHP_INI

go_back