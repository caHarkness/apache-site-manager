# apache-site-manager
Rapidly configure an Apache web server for multiple SSL/TLS side-by-side web applications

#### Installation

Execute the following, single-liner to begin installing this repository to `/var/www`. If `/var/www` exists, it will be renamed with a timestamp.

    cd /var; cp -rfva www "www-$(date +'%Y%m%d%H%M')"; rm -rf www/*; git clone https://github.com/caHarkness/apache-site-manager.git www; cd /var/www/sh; ./begin-setup.sh