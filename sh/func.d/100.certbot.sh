#!/bin/bash

certbot-sign () {
    if [[ -z $CERTBOT_EMAIL ]]
    then
        echo "Please define CERTBOT_EMAIL in configuration before running"
        return
    fi

    cd $WEB_ROOT

    if [[ ! -e sh/gen/sites.list ]]
    then
        echo "No sites.list file found, exiting..."
        return
    fi

    OPENSSL_DOMAINS=""
    CERTBOT_DOMAINS=""

    for p in live/*
    do
        if [[ -d $p ]]
        then
            PDIR=$(basename $(dirname $p))
            PNAME=$(basename $p)

            # Treat project name as FQDN
            if [[ -f $p/flags/PROJECT_NAME_IS_FQDN ]]
            then
                echo "$PDIR/$PNAME found"

                OPENSSL_DOMAINS="${OPENSSL_DOMAINS}DNS:${PNAME},"
                CERTBOT_DOMAINS="${CERTBOT_DOMAINS}${PNAME},"
                continue
            fi

            # Try reading the FQDN flag for a value
            if [[ -f $p/flags/FQDN ]]
            then
                OPENSSL_DOMAINS="${OPENSSL_DOMAINS}DNS:${FQDN},"
                CERTBOT_DOMAINS="${CERTBOT_DOMAINS}${FQDN},"
                continue
            fi
        fi
    done

    # Remove the trailing commas
    OPENSSL_DOMAINS="${OPENSSL_DOMAINS::-1}"
    CERTBOT_DOMAINS="${CERTBOT_DOMAINS::-1}"

    NL=$'\n'
    OPENSSL_CONFIG_DATA="$(cat /etc/ssl/openssl.cnf)"
    OPENSSL_CONFIG_DATA="${OPENSSL_CONFIG_DATA}${NL}${NL}"
    OPENSSL_CONFIG_DATA="${OPENSSL_CONFIG_DATA}[SAN]${NL}"
    OPENSSL_CONFIG_DATA="${OPENSSL_CONFIG_DATA}subjectAltName=${OPENSSL_DOMAINS}"

    service apache2 stop

    rm -rf sh/gen/ssl
    mkdir sh/gen/ssl

    openssl req \
        -new \
        -nodes \
        -subj "/" \
        -reqexts SAN \
        -config <(echo ${OPENSSL_CONFIG_DATA}) \
        -out ${CERTBOT_CSR} \
        -keyout ${CERTBOT_KEY} \
        -newkey rsa:2048 \
        -outform DER

    certbot certonly \
        --standalone \
        --non-interactive \
        --agree-tos \
        --email ${CERTBOT_EMAIL} \
        --force-renewal \
        --expand \
        --no-self-upgrade \
        -d ${CERTBOT_DOMAINS} \
        --csr ${CERTBOT_CSR} \
        --cert-path ${CERTBOT_CRT} \
        --key-path ${CERTBOT_KEY} \
        --fullchain-path ${CERTBOT_FCH} \
        --chain-path ${CERTBOT_CH} \
        --config-dir ${CERTBOT_CONFIG_DIR}

    service apache2 start
}