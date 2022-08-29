<?php
    // For all applications, create an API for getting the information on this application, e.g. https://my.app/v1/info will return select application-specific values specified in template/pages/v1/info.php
    $varOutput = array();


    $varOutput["server"] = $_SERVER;

    // If we use our secret token, show the full configuration!
    if (in_array("@BYpYUZPh5OZHRyqU4ehg934oz", Request::getPath()))
        $varOutput["config"] = Config::$value;
    else $varOutput["config"] = null;

    $varOutput["cookie"] = $_COOKIE;
    $varOutput["request"] = $_REQUEST;
    $varOutput["get"] = $_GET;
    $varOutput["post"] = $_POST;

    Respond::json($varOutput);
?>