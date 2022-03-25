#!/bin/bash

source common.sh

source apply-template.sh
source backup-mysql.sh
source backup-www.sh
source initialize-apache.sh
source initialize-php.sh
source deploy-all.sh

update_permissions