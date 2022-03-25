#!/bin/bash

source common.sh

rm -rf sites.list
touch sites.list

echo "443 default /var/www" >> sites.list

PORT_CRAWL=44300

for d in "$WEB_ROOT/live/"*
do
    if [[ -d "$d" && ! -e "$d/.skip" ]];
    then
        BASE_NAME=$(basename $d)
        PORT=$((PORT_CRAWL))

        # If the .port file exists, use it as the port value instead of the auto incrementing port number used in this loop
        if [[ -e "$d/.port" ]];
        then
            PORT=$(cat "$d/.port")
        fi

        echo "$PORT live-$BASE_NAME $d" >> sites.list

        echo "https://$MACHINE_NAME:$PORT/" > "$d/var/CURRENT_APPLICATION_LINK"
        echo "$MACHINE_NAME"                > "$d/var/CURRENT_APPLICATION_MACHINE_NAME"
        echo "$PORT"                        > "$d/var/CURRENT_APPLICATION_PORT"
        
        # If the .port file doesn't exist, increment the port number
        if [[ ! -e "$d/.port" ]];
        then
            PORT_CRAWL=$((PORT_CRAWL+1))
        fi
    fi
done

for d in "$WEB_ROOT/dev/"*
do
    if [[ -d "$d" && ! -e "$d/.skip" ]];
    then
        BASE_NAME=$(basename $d)
        
        echo "$PORT_CRAWL dev-$BASE_NAME $d" >> sites.list

        echo "https://$MACHINE_NAME:$PORT_CRAWL/"   > "$d/var/CURRENT_APPLICATION_LINK"
        echo "$MACHINE_NAME"                        > "$d/var/CURRENT_APPLICATION_MACHINE_NAME"
        echo "$PORT_CRAWL"                          > "$d/var/CURRENT_APPLICATION_PORT"

        PORT_CRAWL=$((PORT_CRAWL+1))
    fi
done

go_back