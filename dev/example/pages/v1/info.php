<?php
    // For all applications, create an API for getting the information on this application, e.g. https://my.app/v1/info will return select application-specific values specified in template/pages/v1/info.php
    $varOutput = array();

    $varOutput["server"]    = $_SERVER;
    $varOutput["config"]    = Config::$value;
    $varOutput["cookie"]    = $_COOKIE;
    $varOutput["request"]   = $_REQUEST;
    $varOutput["get"]       = $_GET;
    $varOutput["post"]      = $_POST;

    Respond::json($varOutput);
?>