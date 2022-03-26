#!/bin/bash

source common.sh

cd $APACHE_DIR

TIME=$(date +"%Y%m%d%H%M")
FILE_NAME="$TIME-apache2-backup.zip"

zip -r "$FILE_NAME" .

if [[ -d /mnt/backup ]];
then
    cp $FILE_NAME /mnt/backup
fi

mv "$FILE_NAME" ~
go_back
