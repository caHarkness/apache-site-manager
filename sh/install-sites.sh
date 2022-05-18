#!/bin/bash

source common.sh

if [[ ! -e "sites.list" ]];
then
    echo "No sites.list file, please run index-sites.sh first!"
    exit 0
fi

while read LINE;
do
    IFS=" "
    set - $LINE
    SERVER_PORT=$1
    SERVER_NAME=$2
    DOCUMENT_ROOT=$3
    SECURITY_REQ=$4

    TEMPLATE_FILE="template-ssl.conf"

    if [[ "$SECURITY_REQ" == "none" ]]
    then
        TEMPLATE_FILE="template.conf"
    fi

    if [[ "$SECURITY_REQ" == "auth" ]]
    then
        TEMPLATE_FILE="template-ssl-auth.conf"
    fi

    DATA="$(<conf/$TEMPLATE_FILE)"
    DATA=${DATA//__SERVER_PORT__/$SERVER_PORT}
    DATA=${DATA//__SERVER_NAME__/$SERVER_NAME}
    DATA=${DATA//__DOCUMENT_ROOT__/$DOCUMENT_ROOT}

    CONF_FILE="${APACHE_DIR}/sites-available/${SERVER_NAME}-ssl.conf"
    CONF_LINK="${APACHE_DIR}/sites-enabled/${SERVER_NAME}-ssl.conf"

    echo "$DATA" > "$CONF_FILE"
    echo "Created '$CONF_FILE'"

    ln -sf "$CONF_FILE" "$CONF_LINK"
    echo "Linked '$CONF_FILE' to '$CONF_LINK'"
    
done < sites.list

go_back
service apache2 restart