<?php
    Config::$value["PROJECTS_DIR"]  = "/var/www/dev/";
    Config::$value["IS_DEV"]        = true;
    Config::$value["IS_LIVE"]       = false;
    
    Config::loadLibrariesFrom("common");
?>