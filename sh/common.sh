source config.sh

if [ "$EUID" -ne 0 ]
    then echo "This script must be ran as root."
    exit 0
fi

warn_user () {
    export DO_SCRIPT=0
    clear

    echo "WARNING:"
    echo "This script is about to: $1"
    echo ""

    read -p "Type 'YES' if you wish to continue: " PROMPT
    if [[ $PROMPT == "YES" ]]
    then
        echo "OK."
        export DO_SCRIPT=1
    else
        echo "Aborted."
    fi

    echo ""
    sleep 1
}

remind_user () {
    clear

    echo "REMINDER:"
    echo "This script is reminding you to: $1"
    echo ""
}

about_this () {
    clear
    echo "$1"
    echo ""
    read -p "Press ENTER to continue." PROMPT
}

read_carefully () {
    clear

    echo "READ CAREFULLY:"
    echo "$1"
    echo ""

    read -p "Type 'YES' if you wish to continue: " PROMPT
    if [[ $PROMPT == "YES" ]]
    then
        echo "OK."
        echo ""
    else
        echo "Aborted."
        echo ""
        exit
    fi

    sleep 1
}

go_back () {
    cd "$WEB_ROOT/sh"
}

update_permissions () {
    chmod -R 0755 "$WEB_ROOT"
    chown -R www-data:www-data "$WEB_ROOT"

    chmod -R 0755 "$APACHE_DIR"
    chown -R www-data:www-data "$APACHE_DIR"

    chmod -R 0755 "$SSL_DIR"
    chown -R www-data:www-data "$SSL_DIR"

    chmod -R 0755 "$PHP_DIR"
    chown -R www-data:www-data "$PHP_DIR"
}

go_back
update_permissions