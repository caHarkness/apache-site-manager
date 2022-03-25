<?php
    Config::$value["PROJECTS_DIR"]  = "/var/www/live/";
    Config::$value["IS_DEV"]        = false;
    Config::$value["IS_LIVE"]       = true;
    
    Config::loadLibrariesFrom("common");
?>