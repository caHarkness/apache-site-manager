#!/bin/bash

backup-apache () {
    cd $APACHE_DIR

    TIME=$(date +"%Y%m%d%H%M")
    FILE_NAME="$TIME-apache2-backup.zip"

    zip -r "$FILE_NAME" .

    if [[ -d /mnt/backup ]];
    then
        cp $FILE_NAME /mnt/backup
    fi

    mv "$FILE_NAME" ~
}

backup-crontab () {
    TIME=$(date +"%Y%m%d%H%M")
    FILE_NAME="$TIME-crontab"

    cp /etc/crontab $FILE_NAME

    if [[ -d /mnt/backup ]];
    then
        cp $FILE_NAME /mnt/backup
    fi
}

backup-mysql () {
    cd $WEB_ROOT

    TIME=$(date +"%Y%m%d%H%M")
    FILE_NAME="$TIME-mysqldump-backup.sql"

    mysqldump \
        --user=$MYSQL_USER \
        --password=$MYSQL_PASSWORD \
        --all-databases \
        --routines > $FILE_NAME

    if [[ -d /mnt/backup ]];
    then
        cp $FILE_NAME /mnt/backup
    fi

    mv "$FILE_NAME" ~
}

backup-openssl () {
    cd $SSL_DIR

    TIME=$(date +"%Y%m%d%H%M")
    FILE_NAME="$TIME-openssl-backup.zip"

    zip -r "$FILE_NAME" .

    if [[ -d /mnt/backup ]];
    then
        cp $FILE_NAME /mnt/backup
    fi

    mv "$FILE_NAME" ~
}

backup-php () {
    cd $PHP_DIR

    TIME=$(date +"%Y%m%d%H%M")
    FILE_NAME="$TIME-php-backup.zip"

    zip -r "$FILE_NAME" .

    if [[ -d /mnt/backup ]];
    then
        cp $FILE_NAME /mnt/backup
    fi

    mv "$FILE_NAME" ~
}

backup-www () {
    cd $WEB_ROOT

    TIME=$(date +"%Y%m%d%H%M")
    FILE_NAME="$TIME-www-backup.zip"

    zip -r "$FILE_NAME" .

    if [[ -d /mnt/backup ]];
    then
        cp $FILE_NAME /mnt/backup
    fi

    mv "$FILE_NAME" ~
}

backup-all () {
    backup-apache
    backup-crontab
    backup-mysql
    backup-openssl
    backup-php
    backup-www
}