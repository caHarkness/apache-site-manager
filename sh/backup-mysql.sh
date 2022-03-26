#!/bin/bash

source common.sh

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