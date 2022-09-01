# apache-site-manager
Rapidly configure an Apache web server for multiple SSL/TLS side-by-side web applications

#### Installation

Execute the following, single-liner to begin installing this repository to `/var/www`. If `/var/www` exists, it will be renamed with a timestamp.

    cd /var; if [[ -d "www" ]]; then cd www; FNAME="www-$(date +'%Y%m%d%H%M').zip"; zip -r ../$FNAME ./{*,.[!.]*}; rm -rf *; else mkdir www; fi; cd /var/www; git clone https://github.com/caHarkness/apache-site-manager.git tmp; cp -rfva tmp/{*,.[!.]*} ./; rm -rf tmp; cd /var/www/sh