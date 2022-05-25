#!/bin/bash

user-warn () {
    export DO_SCRIPT=0
    clear

    echo "WARNING:"
    echo "This script is about to: $*"
    echo ""

    read -p "Type 'YES' if you wish to continue: " PROMPT
    if [[ $PROMPT == "YES" ]]
    then
        echo "OK."
        export DO_SCRIPT=1
    else
        echo "Aborted."
    fi

    echo ""
    sleep 1
}

user-return () {
    cd $WEB_ROOT/sh
}