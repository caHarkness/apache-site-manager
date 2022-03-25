#!/bin/bash

source common.sh
about_this "This script should be the very first thing you run (once) after installing and/or cloning apache-site-manager to your filesystem."

read_carefully "Review the file located at apache-site-manager/sh/config.sh and make any necessary changes before proceeding."
read_carefully "You must install Apache for this tool to do its job. Proceed when apache2 is installed."

warn_user "Install all the dependencies using apt"

if [[ $DO_SCRIPT -eq 1 ]];
then
    apt install apache2 php7.4
fi