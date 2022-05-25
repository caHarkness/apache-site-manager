#!/bin/bash

deploy () {
    cd $WEB_ROOT

    if [[ -z $1 ]]
    then
        # Copy projects from dev to live
        rm -rfv live
        cp -rfva dev live

        # Move the live config in place of dev config
        rm -rf live/config.php
        mv live/config-live.php live/config.php
    else
        PNAME=$1

        if [[ ! -d dev/$PNAME ]]
        then
            echo "$PROJECT_NAME is not a project directory in dev"
            return
        fi

        if [[ ! -d live ]]
        then
            echo "The live folder does not exist, please run deploy-all first"
            return
        fi

        rm -rfv live/$PNAME
        cp -rfva dev/$PNAME live
    fi

    apache-initialize
    project-index
    apache-install-sites
    apache-restart

    user-return
    permissions-update
}