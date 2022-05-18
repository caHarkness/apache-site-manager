#!/bin/bash

source common.sh

cd $WEB_ROOT

rm -rfv live
cp -rfva dev live

rm -rf live/config.php
mv live/config-live.php live/config.php

go_back

source initialize-apache.sh
source index-sites.sh
source install-sites.sh
source create-links.sh

update_permissions