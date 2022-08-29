#!/bin/bash

permissions-set () {
    chmod -R 0755 $1/*
    chown -R www-data:www-data $1/*
}

permissions-update () {
    permissions-set $WEB_ROOT
    permissions-set $APACHE_DIR
    permissions-set $PHP_DIR
    permissions-set $SSL_DIR
}

permissions-update