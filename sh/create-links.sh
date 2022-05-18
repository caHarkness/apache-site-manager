#!/bin/bash

source common.sh



PAGE_DATA="$(<conf/APPS.html)"

cd "$WEB_ROOT"

find . | grep 'APP_LINK' > sites.tmp

DEV_APPS=""
LIVE_APPS=""

while read LINE
do
    DATA=$(<$LINE)

    _IFS=$IFS
    IFS="/"

    read -ra NEW_ARRAY <<< "$LINE"
    KIND="${NEW_ARRAY[1]}"
    NAME="${NEW_ARRAY[2]}"

    IFS="$_IFS"
    DATA=$(cat $LINE)

    echo "$KIND  $NAME  $FPATH  $DATA"

    if [[ "$KIND" == "dev" ]]
    then
        DEV_APPS="$DEV_APPS<li><a href=\"$DATA\">$NAME</a> (dev)</li>"
    fi

    if [[ "$KIND" == "live" ]]
    then
        LIVE_APPS="$LIVE_APPS<li><a href=\"$DATA\">$NAME</a></li>"
    fi

done < sites.tmp
rm sites.tmp

TIMESTAMP=$(date +"%Y%m%d%H%M")

# Edit the template HTML
PAGE_DATA=${PAGE_DATA//__TIMESTAMP__/$TIMESTAMP}
PAGE_DATA=${PAGE_DATA//__LIVE_APPS__/$LIVE_APPS}
PAGE_DATA=${PAGE_DATA//__DEV_APPS__/$DEV_APPS}

echo $PAGE_DATA > APPS.html
go_back