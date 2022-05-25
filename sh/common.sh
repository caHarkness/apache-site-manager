source config.sh

if [ "$EUID" -ne 0 ]
then
    echo "This script must be ran as root."
    exit 0
fi

for s in $WEB_ROOT/sh/conf.d/*.sh
do
    source $s
done

for s in $WEB_ROOT/sh/func.d/*.sh
do
    source $s
done

eval "$*"
user-return