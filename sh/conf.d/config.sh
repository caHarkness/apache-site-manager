#!/bin/bash

# Where apache-site-manager is installed or cloned to
export WEB_ROOT="/var/www"

# Where projects in development reside
export DEV_DIR="/var/www/dev"

# Where projects that are deployed reside
export LIVE_DIR="/var/www/live"

# Where the apache2 configuration resides
export APACHE_DIR="/etc/apache2"

# Where the php.ini file resides
export PHP_DIR="/etc/php"

# Where the php.ini file resides
export PHP_INI="$PHP_DIR/7.4/apache2/php.ini"

# Where the OpenSSL files live
export SSL_DIR="/etc/ssl"

# The host name you would use in the address bar
export MACHINE_NAME="my.website.com"

# The user for backing up MySQL databases
export MYSQL_USER="root"

# The password of the user for backing up MySQL databases
export MYSQL_PASSWORD="password"
