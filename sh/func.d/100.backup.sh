#!/bin/bash

backup-apache () {
    TIME=$(date +"%Y%m%d%H%M")
    LINK_NAME="apache2-backup.zip"
    FILE_NAME="$TIME-$LINK_NAME"
    TO_DIR="$APACHE_DIR"
    RETURN_DIR="$(pwd)"

    cd $TO_DIR
    zip -yr "$FILE_NAME" .
    mv $FILE_NAME $RETURN_DIR
    cd $RETURN_DIR

    if [[ -e $LINK_NAME ]]; then rm $LINK_NAME; fi
    ln -s $FILE_NAME $LINK_NAME
}

restore-apache () {
    LINK_NAME="apache2-backup.zip"
    TO_DIR="$APACHE_DIR"
    RETURN_DIR="$(pwd)"

    rm -rf $TO_DIR/*
    cp $LINK_NAME $TO_DIR
    cd $TO_DIR
    unzip $LINK_NAME
    rm $LINK_NAME
    cd $RETURN_DIR
}

backup-crontab () {
    TIME=$(date +"%Y%m%d%H%M")
    LINK_NAME="crontab"
    FILE_NAME="$TIME-$LINK_NAME"
    TO_DIR=~
    RETURN_DIR="$(pwd)"

    cd $TO_DIR
    cp /etc/crontab $FILE_NAME
    mv $FILE_NAME $RETURN_DIR
    cd $RETURN_DIR

    if [[ -e $LINK_NAME ]]; then rm $LINK_NAME; fi
    ln -s $FILE_NAME $LINK_NAME
}

restore-crontab () {
    LINK_NAME="crontab"

    rm /etc/crontab
    cp $LINK_NAME /etc
}

backup-mysql () {
    TIME=$(date +"%Y%m%d%H%M")
    LINK_NAME="mysqldump-backup.sql"
    FILE_NAME="$TIME-$LINK_NAME"
    TO_DIR=~
    RETURN_DIR="$(pwd)"

    cd $TO_DIR
    mysqldump \
        --user=$MYSQL_USER \
        --password=$MYSQL_PASSWORD \
        --all-databases \
        --routines > $FILE_NAME
    mv $FILE_NAME $RETURN_DIR
    cd $RETURN_DIR

    if [[ -e $LINK_NAME ]]; then rm $LINK_NAME; fi
    ln -s $FILE_NAME $LINK_NAME
}

restore-mysql () {
    LINK_NAME="mysqldump-backup.sql"

    mysql \
        --user=$MYSQL_USER \
        --password=$MYSQL_PASSWORD < $LINK_NAME
}

backup-openssl () {
    TIME=$(date +"%Y%m%d%H%M")
    LINK_NAME="openssl-backup.zip"
    FILE_NAME="$TIME-$LINK_NAME"
    TO_DIR="$SSL_DIR"
    RETURN_DIR="$(pwd)"

    cd $TO_DIR
    zip -yr "$FILE_NAME" .
    mv $FILE_NAME $RETURN_DIR
    cd $RETURN_DIR

    if [[ -e $LINK_NAME ]]; then rm $LINK_NAME; fi
    ln -s $FILE_NAME $LINK_NAME
}

restore-openssl () {
    LINK_NAME="openssl-backup.zip"
    TO_DIR="$SSL_DIR"
    RETURN_DIR="$(pwd)"

    rm -rf $TO_DIR/*
    cp $LINK_NAME $TO_DIR
    cd $TO_DIR
    unzip $LINK_NAME
    rm $LINK_NAME
    cd $RETURN_DIR
}

backup-php () {
    TIME=$(date +"%Y%m%d%H%M")
    LINK_NAME="php-backup.zip"
    FILE_NAME="$TIME-$LINK_NAME"
    TO_DIR="$PHP_DIR"
    RETURN_DIR="$(pwd)"

    cd $TO_DIR
    zip -yr "$FILE_NAME" .
    mv $FILE_NAME $RETURN_DIR
    cd $RETURN_DIR

    if [[ -e $LINK_NAME ]]; then rm $LINK_NAME; fi
    ln -s $FILE_NAME $LINK_NAME
}

restore-php () {
    LINK_NAME="php-backup.zip"
    TO_DIR="$PHP_DIR"
    RETURN_DIR="$(pwd)"

    rm -rf $TO_DIR/*
    cp $LINK_NAME $TO_DIR
    cd $TO_DIR
    unzip $LINK_NAME
    rm $LINK_NAME
    cd $RETURN_DIR
}

backup-samba () {
    TIME=$(date +"%Y%m%d%H%M")
    LINK_NAME="smb.conf"
    FILE_NAME="$TIME-$LINK_NAME"
    TO_DIR=~
    RETURN_DIR="$(pwd)"

    cd $TO_DIR
    cp /etc/samba/smb.conf $FILE_NAME
    mv $FILE_NAME $RETURN_DIR
    cd $RETURN_DIR

    if [[ -e $LINK_NAME ]]; then rm $LINK_NAME; fi
    ln -s $FILE_NAME $LINK_NAME
}

restore-samba () {
    LINK_NAME="smb.conf"

    rm /etc/samba/smb.conf
    cp $LINK_NAME /etc/samba
}

backup-www () {
    TIME=$(date +"%Y%m%d%H%M")
    LINK_NAME="www-backup.zip"
    FILE_NAME="$TIME-$LINK_NAME"
    TO_DIR="$WEB_ROOT"
    RETURN_DIR="$(pwd)"

    cd $TO_DIR
    zip -yr "$FILE_NAME" .
    mv $FILE_NAME $RETURN_DIR
    cd $RETURN_DIR
}

backup-all () {
    cd $WEB_ROOT

    if [[ ! -d backup ]]
    then
        mkdir backup
    fi

    cd backup

    backup-apache
    backup-apache
    backup-crontab
    backup-mysql
    backup-openssl
    backup-samba
    backup-php

    cd $WEB_ROOT

    backup-www
    permissions-update

    if [[ -d backup ]]
    then
        rm -rf backup
    fi

    if [[ -d /mnt/backup ]]
    then
        cp *.zip /mnt/backup
    fi

    mv *.zip ~
}

restore-all () {
    cd $WEB_ROOT

    if [[ ! -d backup ]]
    then
        echo "Missing the backup directory."
        return
    fi

    cd backup

    restore-apache
    restore-crontab
    restore-mysql
    restore-openssl
    restore-samba
    restore-php
}