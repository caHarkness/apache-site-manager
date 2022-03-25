#!/bin/bash

source common.sh
about_this "This script backs up the entire /var/www directory recursively."

warn_user "Make a backup of the entire /var/www directory"

if [[ $DO_SCRIPT -eq 1 ]];
then

    cd $WEB_ROOT

    TIME=$(date +"%Y%m%d%H%M")
    FILE_NAME="$TIME-www-backup.zip"

    zip -r "$FILE_NAME" .

    if [[ -d /mnt/backup ]];
    then
        cp $FILE_NAME /mnt/backup
    fi

    mv "$FILE_NAME" ~
    
    go_back
    remind_user "Clean out the /root folder once in a while"
fi