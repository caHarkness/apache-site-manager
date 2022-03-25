#!/bin/bash

source common.sh
about_this "This script backs up the entire MySQL database."

warn_user "Make a backup of the entire MySQL database"

if [[ $DO_SCRIPT -eq 1 ]];
then

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
    
    go_back
    remind_user "Clean out the /root folder once in a while"

fi