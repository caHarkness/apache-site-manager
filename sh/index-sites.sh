#!/bin/bash

source common.sh

rm -rf sites.list
touch sites.list

echo "443 base /var/www default" >> sites.list

PORT_CRAWL=44300
PORT_HTTP_SET=0

for d in "$WEB_ROOT/live/"*
do
    if [[ -d "$d" && ! -e "$d/.skip" ]];
    then
        BASE_NAME=$(basename $d)
        PORT=$((PORT_CRAWL))
        SECURITY="default"

        if [[ -e "$d/.security" ]];
        then
            SECURITY=$(cat "$d/.security")
        fi

        # If the .port file exists, use it as the port value instead of the auto incrementing port number used in this loop
        if [[ -e "$d/.port" ]];
        then
            PORT=$(cat "$d/.port")

            # If the application is requesting port 80, only give it out once
            if [[ "$PORT" == "80" ]];
            then
                if [[ $PORT_HTTP_SET -eq 0 ]];
                then
                    PORT_HTTP_SET=1
                    SECURITY="none"
                else
                    PORT=$((PORT_CRAWL))
                fi
            fi
        fi

        echo "$PORT live-$BASE_NAME $d $SECURITY" >> sites.list

        # Create text files that PHP can read into constants for these values
        if [[ ! -e "$d/var" ]]; then mkdir "$d/var"; fi
        
        PROTOCOL="https"
        if [[ "$PORT" == "80" ]];
        then
            PROTOCOL="http"
        fi

        echo "$PROTOCOL://$MACHINE_NAME:$PORT/" > "$d/var/APP_LINK"
        echo "$MACHINE_NAME"                    > "$d/var/APP_HOST"
        echo "$PORT"                            > "$d/var/APP_PORT"
        
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
        SECURITY="default"

        if [[ -e "$d/.security" ]];
        then
            SECURITY=$(cat "$d/.security")
        fi
        
        echo "$PORT_CRAWL dev-$BASE_NAME $d $SECURITY" >> sites.list

        # Create text files that PHP can read into constants for these values
        if [[ ! -e "$d/var" ]]; then mkdir "$d/var"; fi

        PROTOCOL="https"

        echo "$PROTOCOL://$MACHINE_NAME:$PORT_CRAWL/"   > "$d/var/APP_LINK"
        echo "$MACHINE_NAME"                            > "$d/var/APP_HOST"
        echo "$PORT_CRAWL"                              > "$d/var/APP_PORT"

        PORT_CRAWL=$((PORT_CRAWL+1))
    fi
done

go_back