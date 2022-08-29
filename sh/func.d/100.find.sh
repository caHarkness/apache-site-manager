#!/bin/bash

find-references () {
    if [[ -z $2 ]];
    then
        FOUND=$(grep -rl "$1" $DEV_DIR)
        echo $FOUND
    fi

    if [[ "$2" == "link" ]];
    then
        if [[ -d "link" ]];
        then
            rm -rf link
        fi

        mkdir link
        cd link

        grep -rl "$1" "../../dev/." > files.list

        CRAWL=1000

        while read LINE;
        do
            BASE=$(basename $LINE)
            ln -s "$LINE" "$CRAWL.$BASE"
            CRAWL=$((CRAWL+1))
        done < files.list

        permissions-update
        echo "You can now open the 'link' directory as a project"
    fi
}