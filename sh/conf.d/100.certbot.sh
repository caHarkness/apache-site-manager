#!/bin/bash

export CERTBOT_EMAIL="caharkness@gmail.com"

export CERTBOT_OUTPUT_DIR="$WEB_ROOT/sh/gen/ssl"

export CERTBOT_CSR="$CERTBOT_OUTPUT_DIR/server.csr"

export CERTBOT_KEY="$CERTBOT_OUTPUT_DIR/server.key"

export CERTBOT_CRT="$CERTBOT_OUTPUT_DIR/server.crt"

export CERTBOT_FCH="$CERTBOT_OUTPUT_DIR/server.fch"

export CERTBOT_CH="$CERTBOT_OUTPUT_DIR/server.ch"

export CERTBOT_CONFIG_DIR="$CERTBOT_OUTPUT_DIR/conf"