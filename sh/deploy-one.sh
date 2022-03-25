#!/bin/bash

source common.sh

if [[ -z "$1" ]];
then
    echo "No project name was supplied"
    exit 0
fi

PROJECT_NAME=$1
cd $WEB_ROOT

if [[ ! -d "dev/$PROJECT_NAME" ]];
then
    echo "$PROJECT_NAME is not a project directory in dev"
    exit 0
fi

if [[ ! -d "live" ]];
then
    echo "The live folder does not exist, please run deploy-all.sh first"
    exit 0
fi

warn_user "Deploy project named $PROJECT_NAME"

if [[ $DO_SCRIPT -eq 1 ]];
then

    cd $WEB_ROOT

    rm -rfv live/$PROJECT_NAME
    cp -rfva dev/$PROJECT_NAME live

    update_permissions
    go_back

fi

go_back