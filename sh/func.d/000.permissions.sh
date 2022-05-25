#!/bin/bash

permissions-update () {
    cd $WEB_ROOT
    chmod -R 0755 *
    chown -R www-data:www-data *
}

permissions-update