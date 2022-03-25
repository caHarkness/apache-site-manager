#!/bin/bash

source common.sh
warn_user "Copy the contents of 'dev' to 'live'"

if [[ $DO_SCRIPT -eq 1 ]];
then

    cd $WEB_ROOT

    rm -rfv live
    cp -rfva dev live

    rm -rf live/config.php
    mv live/config-live.php live/config.php

    go_back

    source index-sites.sh
    source install-sites.sh
    update_permissions

fi