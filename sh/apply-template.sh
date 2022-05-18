#!/bin/bash

source common.sh

cd "$DEV_DIR"

for d in "./"*
do
    BASE_NAME=$(basename $d)

    if [[ "$BASE_NAME" == "template" ]];
    then
        continue
    fi

    if [[ -e "$d/.pass" ]];
    then
        continue
    fi

    if [[ -d "$d" ]];
    then
        rsync -av \
            --exclude="*.skip" \
            template/.[^.]* \
            "$d/"

        rsync -av \
            --exclude="*.skip" \
            template/* \
            "$d/"
    fi
done

update_permissions
go_back