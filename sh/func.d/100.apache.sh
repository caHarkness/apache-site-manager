#!/bin/bash

apache-initialize () {
    cd $APACHE_DIR

    if [[ ! -d ports-enabled ]]
    then
        mkdir ports-enabled
    fi

    rm -rfv sites-available/*
    rm -rfv sites-enabled/*
    rm -rfv ports-enabled/*
    rm -rfv ports.conf

    cp $WEB_ROOT/sh/res/apache2.conf .

    chown -R www-data:www-data $APACHE_DIR
    chmod -R 0755 $APACHE_DIR

    a2enmod rewrite
    a2enmod ssl

    cd $WEB_ROOT
}

apache-install-sites () {
    cd $WEB_ROOT

    while read LINE;
    do
        IFS=" "
        set - $LINE

        LINE_PORT=$1
        LINE_KIND=$2
        LINE_NAME=$3
        LINE_PATH=$4
        LINE_VHOST_CONFIG=$5

        DATA="$(<sh/vhost.conf.d/$LINE_VHOST_CONFIG)"
        DATA=${DATA//__SERVER_PORT__/$LINE_PORT}
        DATA=${DATA//__SERVER_NAME__/$LINE_NAME}
        DATA=${DATA//__DOCUMENT_ROOT__/$LINE_PATH}

        CONF_FILE="${APACHE_DIR}/sites-available/${LINE_PORT}-${LINE_KIND}-${LINE_NAME}.conf"
        CONF_LINK="${APACHE_DIR}/sites-enabled/${LINE_PORT}-${LINE_KIND}-${LINE_NAME}.conf"

        echo "Listen ${LINE_PORT}" > "${APACHE_DIR}/ports-enabled/${LINE_PORT}.conf"

        echo "$DATA" > "$CONF_FILE"
        echo "Created '$CONF_FILE'"

        ln -sf "$CONF_FILE" "$CONF_LINK"
        echo "Linked '$CONF_FILE' to '$CONF_LINK'"   
    done < sh/gen/sites.list
}

apache-restart () {
    service apache2 restart
}