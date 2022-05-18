#!/bin/bash

source common.sh

cd

TIME=$(date +"%Y%m%d%H%M")
FILE_NAME="$TIME-crontab"

cp /etc/crontab $FILE_NAME

if [[ -d /mnt/backup ]];
then
    cp $FILE_NAME /mnt/backup
fi

go_back
