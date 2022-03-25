source common.sh
warn_user "Install dependency software for this package"

if [[ $DO_SCRIPT -eq 1 ]];
then

    apt install apache2
    # apt install mysql-server
    apt install php7.4
    # apt install php7.4-mysql
    go_back

fi