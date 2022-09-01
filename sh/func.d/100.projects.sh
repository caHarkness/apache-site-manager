#!/bin/bash

project-flags () {
    cd $WEB_ROOT

    PDIR=$1
    PNAME=$2

    if [[ -d $PDIR/$PNAME/flags ]]
    then
        echo "$PDIR/$PNAME flags:"

        for f in $PDIR/flags/[A-Z0-9_]*
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

    HTTPS_PORT_CRAWL=44300
    HTTP_PORT_CRAWL=8000

    # Index all the sites in the live folder
    # for p in live/*
    _IFS=$IFS
    IFS=$'\n'
    LISTING=`find live/* -maxdepth 0 -type d | sort`

    for p in $LISTING
    do
        if [[ -d $p ]]
        then
            PDIR=$(basename $(dirname $p))
            PNAME=$(basename $p)

            VHOST_HTTPS_LIVE_OVERRIDE=""
            VHOST_HTTP_LIVE_OVERRIDE=""
            HTTPS_PORT=""
            HTTP_PORT=""

            echo "$PDIR/$PNAME found"
            project-flags $PDIR $PNAME

            if [[ -f $p/flags/VHOST_HTTPS ]]
            then
                # Auto assign an HTTPS port for this project
                if [[ "$HTTPS_PORT" == "" ]]
                then
                    HTTPS_PORT=$((HTTPS_PORT_CRAWL))
                    HTTPS_PORT_CRAWL=$((HTTPS_PORT_CRAWL+1))
                fi

                LINE_PORT="$HTTPS_PORT"
                LINE_KIND="$PDIR"
                LINE_NAME="$PNAME"
                LINE_PATH="$WEB_ROOT/$p"
                LINE_VHOST_CONFIG="vhost.https.conf"

                if [[ "$VHOST_HTTPS_LIVE_OVERRIDE" != "" ]]; then LINE_VHOST_CONFIG="$VHOST_HTTPS_LIVE_OVERRIDE"; fi

                LINE="$LINE_PORT $LINE_KIND $LINE_NAME $LINE_PATH $LINE_VHOST_CONFIG"
                echo "$LINE" >> sh/gen/sites.list
                echo "$LINE"
                echo "https://${MACHINE_NAME}:${HTTPS_PORT}"    > $p/var/APP_LINK
                echo "https://localhost:${HTTPS_PORT}"          > $p/var/APP_LINK_LOCAL
            fi

            if [[ -f $p/flags/VHOST_HTTP ]]
            then
                # Auto assign an HTTP port for this project
                if [[ "$HTTP_PORT" == "" ]]
                then
                    HTTP_PORT=$((HTTP_PORT_CRAWL))
                    HTTP_PORT_CRAWL=$((HTTP_PORT_CRAWL+1))
                fi

                LINE_PORT="$HTTP_PORT"
                LINE_KIND="$PDIR"
                LINE_NAME="$PNAME"
                LINE_PATH="$WEB_ROOT/$p"
                LINE_VHOST_CONFIG="vhost.http.conf"

                if [[ "$VHOST_HTTP_LIVE_OVERRIDE" != "" ]]; then LINE_VHOST_CONFIG="$VHOST_HTTP_LIVE_OVERRIDE"; fi

                LINE="$LINE_PORT $LINE_KIND $LINE_NAME $LINE_PATH $LINE_VHOST_CONFIG"
                echo "$LINE" >> sh/gen/sites.list
                echo "$LINE"
            fi
        fi
    done

    # Index all the sites in the dev folder
    # for p in dev/*
    IFS=$'\n'
    LISTING=`find dev/* -maxdepth 0 -type d | sort`
    
    for p in $LISTING
    do
        echo "FOUND: $p"

        if [[ -d $p ]]
        then
            PDIR=$(basename $(dirname $p))
            PNAME=$(basename $p)

            VHOST_HTTPS_DEV_OVERRIDE=""
            VHOST_HTTP_DEV_OVERRIDE=""
            HTTPS_PORT=""
            HTTP_PORT=""

            echo "$PDIR/$PNAME found"
            project-flags $PDIR $PNAME

            if [[ -f $p/flags/VHOST_HTTPS ]]
            then
                HTTPS_PORT=$((HTTPS_PORT_CRAWL))
                HTTPS_PORT_CRAWL=$((HTTPS_PORT_CRAWL+1))

                LINE_PORT="$HTTPS_PORT"
                LINE_KIND="$PDIR"
                LINE_NAME="$PNAME"
                LINE_PATH="$WEB_ROOT/$p"
                LINE_VHOST_CONFIG="vhost.https.conf"

                if [[ "$VHOST_HTTPS_DEV_OVERRIDE" != "" ]]; then LINE_VHOST_CONFIG="$VHOST_HTTPS_DEV_OVERRIDE"; fi

                LINE="$LINE_PORT $LINE_KIND $LINE_NAME $LINE_PATH $LINE_VHOST_CONFIG"
                echo "$LINE" >> sh/gen/sites.list
                echo "$LINE"
                echo "https://${MACHINE_NAME}:${HTTPS_PORT}"    > $p/var/APP_LINK
                echo "https://localhost:${HTTPS_PORT}"          > $p/var/APP_LINK_LOCAL
            fi

            if [[ -f $p/flags/VHOST_HTTP ]]
            then
                HTTP_PORT=$((HTTP_PORT_CRAWL))
                HTTP_PORT_CRAWL=$((HTTP_PORT_CRAWL+1))

                LINE_PORT="$HTTP_PORT"
                LINE_KIND="$PDIR"
                LINE_NAME="$PNAME"
                LINE_PATH="$WEB_ROOT/$p"
                LINE_VHOST_CONFIG="vhost.http.conf"

                if [[ "$VHOST_HTTP_DEV_OVERRIDE" != "" ]]; then LINE_VHOST_CONFIG="$VHOST_HTTP_DEV_OVERRIDE"; fi

                LINE="$LINE_PORT $LINE_KIND $LINE_NAME $LINE_PATH $LINE_VHOST_CONFIG"
                echo "$LINE" >> sh/gen/sites.list
                echo "$LINE"
            fi
        fi
    done
    IFS=$_IFS

    PAGE_DATA=$(<sh/res/APPS.md)

    find . | grep -e "APP_LINK$" | sort > sh/gen/sites.tmp

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

    if [[ -e dev/$1/flags/DISABLE_REBASE ]]
    then
        return
    fi

    cp -rfva shared/base/* dev/$1

    if [[ -e dev/$1/flags/BUILD_ON_REBASE ]]
    then
        project-build $1
    fi
}

project-build () {
    cd $WEB_ROOT

    if [[ -z $1 ]];
    then
        for p in dev/*
        do
            if [[ -d $p ]]
            then
                PROJECT_NAME=$(basename $p)
                project-build $PROJECT_NAME
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

    if [[ -e dev/$1/build.sh ]]
    then
        RETURN_DIR=$(pwd)
        cd dev/$1
        source build.sh
        cd $RETURN_DIR
    fi
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

    cp -rfva shared/template dev/$1
    project-rebase $1
}
