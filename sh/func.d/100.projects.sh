#!/bin/bash

project-flags () {
    cd $WEB_ROOT

    PDIR=$1
    PNAME=$2

    if [[ -d $PDIR/$PNAME/flags ]]
    then
        echo "$PDIR/$PNAME flags:"

        for f in $p/flags/[A-Z0-9_]*
        do
            FLAG_NAME=$(basename $f)
            FLAG_VALUE=$(cat $f)
            FLAG_SET_FUNCTION="export $FLAG_NAME=\"$FLAG_VALUE\""

            eval $FLAG_SET_FUNCTION
            echo "* $FLAG_SET_FUNCTION"
        done
    else
        return
    fi
}

project-index () {
    cd $WEB_ROOT

    rm sh/gen/sites.list
    touch sh/gen/sites.list

    PORT_CRAWL=44300

    for p in live/*
    do
        if [[ -d $p && -f $p/flags/VHOST_CONFIG ]]
        then
            PDIR=$(basename $(dirname $p))
            PNAME=$(basename $p)

            echo "$PDIR/$PNAME found"
            project-flags $PDIR $PNAME

            if [[ "$PORT" == "" ]]
            then
                PORT=$((PORT_CRAWL))
                PORT_CRAWL=$((PORT_CRAWL+1))
            fi

            PROTOCOL="https"

            if [[ "$VHOST_CONFIG" == "" ]]
            then
                VHOST_CONFIG="vhost.ssl.conf"

                if [[ "$PORT" == "80" ]]
                then
                    VHOST_CONFIG="vhost.http.conf"
                    PROTOCOL="http"
                fi
            fi

            LINE_PORT="$PORT"
            LINE_NAME="$PDIR-$PNAME"
            LINE_PATH="$WEB_ROOT/$p"
            LINE_VHOST_CONFIG="$VHOST_CONFIG"
            LINE="$LINE_PORT $LINE_NAME $LINE_PATH $LINE_VHOST_CONFIG"

            echo "$LINE" >> sh/gen/sites.list
            echo "$LINE"

            LINK="${PROTOCOL}://${MACHINE_NAME}:${PORT}"
            LINK_LOCAL="${PROTOCOL}://localhost:${PORT}"

            echo "$LINK" > $p/var/APP_LINK
            echo "$LINK_LOCAL" > $p/var/APP_LINK_LOCAL
        fi
    done

    for p in dev/*
    do
        if [[ -d $p && -f $p/flags/VHOST_CONFIG ]]
        then
            PDIR=$(basename $(dirname $p))
            PNAME=$(basename $p)

            echo "$PDIR/$PNAME found"
            project-flags $PDIR $PNAME

            PORT=$((PORT_CRAWL))
            PORT_CRAWL=$((PORT_CRAWL+1))

            PROTOCOL="https"

            if [[ "$VHOST_CONFIG" == "" ]]
            then
                VHOST_CONFIG="vhost.ssl.conf"
            fi

            LINE_PORT="$PORT"
            LINE_NAME="$PDIR-$PNAME"
            LINE_PATH="$WEB_ROOT/$p"
            LINE_VHOST_CONFIG="$VHOST_CONFIG"
            LINE="$LINE_PORT $LINE_NAME $LINE_PATH $LINE_VHOST_CONFIG"

            echo "$LINE" >> sh/gen/sites.list
            echo "$LINE"

            LINK="${PROTOCOL}://${MACHINE_NAME}:${PORT}"
            LINK_LOCAL="${PROTOCOL}://localhost:${PORT}"

            echo "$LINK" > $p/var/APP_LINK
            echo "$LINK_LOCAL" > $p/var/APP_LINK_LOCAL
        fi
    done

    PAGE_DATA=$(<sh/res/APPS.md)

    find . | grep -e "APP_LINK$" > sh/gen/sites.tmp

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
        NL=$'\n'

        echo "$KIND  $NAME  $FPATH  $DATA"

        if [[ "$KIND" == "dev" ]]
        then
            DEV_APPS="${DEV_APPS}* [$NAME]($DATA) (dev)$NL"
        fi

        if [[ "$KIND" == "live" ]]
        then
            LIVE_APPS="${LIVE_APPS}* [$NAME]($DATA)$NL"
        fi

    done < sh/gen/sites.tmp
    rm sh/gen/sites.tmp

    TIMESTAMP=$(date +"%Y-%m-%d %H:%M")

    # Edit the template HTML
    PAGE_DATA="${PAGE_DATA//__TIMESTAMP__/$TIMESTAMP}"
    PAGE_DATA="${PAGE_DATA//__LIVE_APPS__/$LIVE_APPS}"
    PAGE_DATA="${PAGE_DATA//__DEV_APPS__/$DEV_APPS}"

    echo "$PAGE_DATA" > APPS.md
}

project-rebase () {
    cd $WEB_ROOT

    if [[ -z $1 ]];
    then
        for p in dev/*
        do
            if [[ -d $p ]]
            then
                PROJECT_NAME=$(basename $p)
                project-rebase $PROJECT_NAME
            fi
        done
        return
    fi

    if [[ ! -e dev/$1 ]];
    then
        echo "Project $1 does not exist"
        return
    fi

    project-flags "dev" $1

    if [[ "$DISABLE_REBASE" != "" ]]
    then
        return
    fi

    cp -rfva pre/base/* dev/$1
}

project-create () {
    cd $WEB_ROOT

    if [[ -z $1 ]];
    then
        echo "No project name was supplied"
        return
    fi

    if [[ -e dev/$1 ]]
    then
        echo "Project $1 already exists"
        return
    fi

    cp -rfva pre/template dev/$1
    project-rebase $1
}