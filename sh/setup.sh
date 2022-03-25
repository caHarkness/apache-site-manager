#!/bin/bash

source common.sh

source backup-mysql.sh
source backup-www.sh
source initialize-apache.sh
source initialize-php.sh
source list-sites.sh
source install-sites.sh
source deploy-all.sh

chown -R www-data:www-data "$WEB_ROOT"
chmod -R 0755 "$WEB_ROOT"

service apache2 restart