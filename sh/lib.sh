#!/bin/bash

if [ "$EUID" -ne 0 ]
then
    echo "This script must be ran as root."
    exit 1
fi

for s in conf.d/*.sh
do
    echo "Found $s"
    source $s
done

for s in func.d/*.sh
do
    echo "Found $s"
    source $s
done

eval "$*"
user-return