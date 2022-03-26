#!/bin/bash

source common.sh
about_this "This script should be the very first thing you run (once) after installing and/or cloning apache-site-manager to your filesystem."



warn_user "Apply the template project across all other projects"

if [[ $DO_SCRIPT -eq 1 ]];
then
    ./apply-template.sh
fi



warn_user "Apply the template project across all other projects"

if [[ $DO_SCRIPT -eq 1 ]];
then
    ./apply-template.sh
fi


source apply-template.sh
source backup-mysql.sh
source backup-www.sh
source initialize-apache.sh
source initialize-php.sh
source deploy-all.sh

update_permissions