#!/bin/bash

source common.sh
about_this "This script is for copying the contents of /var/www/dev/template to all other projects in the /var/www/dev directory. This is how we maintain the same look and feel across all apps."

warn_user "Apply template in dev to all projects in dev"

if [[ $DO_SCRIPT -eq 1 ]];
then

    cd "$DEV_DIR"

    for d in "./"*
    do
        BASE_NAME=$(basename $d)

        if [[ "$BASE_NAME" == "template" ]];
        then
            continue
        fi

        if [[ -d "$d" ]];
        then
            rsync -av \
                --exclude="*/.skip" \
                template/* \
                "$d/"
        fi
    done

    update_permissions
    go_back

fi