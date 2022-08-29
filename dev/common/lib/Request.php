<?php
    /*
        Request.php

        A static class library that plays a major role in the framework. Its purpose is to break apart the request, determine which parts of the request exist as a PHP script & which are the supplied arguments of the controller action, and "require" the found PHP script.
    */

    class Request
    {
        static $varPath;
        static $varPathInfo;

        // Learn more about the request by breaking it apart and storing the individual, important pieces as keyed values.
        static function getPathInfo()
        {
            if (self::$varPathInfo != null)
                return self::$varPathInfo;

            $strRawPath = null;

            if (isset($_GET["path"]))
                    $strRawPath = substr($_GET["path"], 1);
            else    $strRawPath = "";

            $strPath                = $strRawPath;
            $strRelativeLocation    = "pages/$strPath";
            $varOutput              = null;

            while (strpos($strRelativeLocation, "//"))
                $strRelativeLocation    = str_replace("//", "/", $strRelativeLocation);
            $strRelativeLocation        = trim($strRelativeLocation, "/");

            while (true)
            {
                if (strlen($strRelativeLocation) < 1)
                    break;

                $strAction          = "{$strRelativeLocation}.php";
                $strControllerIndex = "{$strRelativeLocation}/index.php";

                if (file_exists("{$strAction}"))
                {
                    $varParts           = explode("/", $strRelativeLocation);
                    $strActionName      = array_pop($varParts);
                    $strControllerName  = array_pop($varParts);

                    $varOutput = [
                        "complete"      => $strPath,
                        "script"        => $strAction,
                        "resource"      => $strRelativeLocation,
                        "action"        => $strActionName,
                        "controller"    => $strControllerName
                    ];

                    break;
                }

                if (file_exists("{$strControllerIndex}"))
                {
                    $varParts           = explode("/", $strRelativeLocation);
                    $strControllerName  = array_pop($varParts);

                    $varOutput = [
                        "complete"      => $strPath,
                        "script"        => $strControllerIndex,
                        "resource"      => $strRelativeLocation,
                        "action"        => "index",
                        "controller"    => $strControllerName
                    ];

                    break;
                }

                $varParts =
                    explode("/", $strRelativeLocation);
                    array_pop($varParts);

                $strRelativeLocation = implode("/", $varParts);
            }

            // Find the site resource from the relative location string
            $s = "pages";
            if (substr($strRelativeLocation, 0, strlen($s)) === $s)
                $strRelativeLocation    = substr($strRelativeLocation, strlen($s));
            $varOutput["resource"]      = trim($strRelativeLocation, "/");

            // Find the arguments of the request
            $strArguments   = $strPath;
            $s              = $varOutput["resource"];
            if (substr($strArguments, 0, strlen($s)) === $s)
                $strArguments   = substr($strArguments, strlen($s));
            $varOutput["args"]  = trim($strArguments, "/");

            // Save and return the path info
            self::$varPathInfo = $varOutput;
            return $varOutput;
        }

        static function getPathString()
        {
            $strPathString = implode("/", self::getPath());
            return "/$strPathString";
        }

        // Returns the complete, relative path of the request as an array of strings
        static function getPath()
        {
            return
            array_filter(
            explode(
                "/",
                self::getPathInfo()["complete"]),
            "strlen");
        }

        // Returns the relative path of the requested script as a string
        // e.g. "pages/user/signin.php"
        static function getScript()
        {
            return
            self::getPathInfo()["script"];
        }

        // Returns the requested resource as a string
        // e.g. "user/signin"
        static function getResource()
        {
            return
            self::getPathInfo()["resource"];
        }

        // Returns the requested action as a string
        // e.g. "signin" from user/signin
        static function getAction()
        {
            return
            self::getPathInfo()["action"];
        }

        // Returns the requested controller as a string
        // e.g. "user" from user/signin
        static function getController()
        {
            return
            self::getPathInfo()["controller"];
        }

        // Returns the request arguments after the controller/action as an array of strings
        // e.g. ["abc", "123", "def"] from user/signin/abc/123/def
        static function getArgs()
        {
            return
            array_filter(
            explode(
                "/",
                self::getPathInfo()["args"]),
            "strlen");
        }

        // Safely returns a request argument by its index or null if it doesn't exist (without error)
        static function getArg($intIndex)
        {
            $varArgs = self::getArgs();

            if (count($varArgs) >= $intIndex + 1)
                    return $varArgs[$intIndex];
            else    return null;
        }

        static function hasParam($strKey)
        {
            if (is_array($_GET))
            {
                if (array_key_exists($strKey, $_GET))
                    return true;
            }

            return false;
        }

        // Safely returns the value of a request parameter or null if not defined (without error)
        // e.g. 12345 from getParam("id") when resource is like /users/get?id=12345
        static function getParam($strKey)
        {
            if (is_array($_GET))
            {
                if (array_key_exists($strKey, $_GET))
                    return $_GET[$strKey];
            }

            return null;
        }

        static function getReferer()
        {
            return
            $_SERVER["HTTP_REFERER"];
        }

        // To be called by the template's index.php only
        // Responsible for building knowledge on the request before render
        static function process()
        {
            self::getPathInfo();
        }

        // To be called by the template's index.php only
        // Responsible for rendering the request's script
        static function render()
        {
            // Completely ignore favicon.ico requests
            if (isset($_GET["path"]))
                if (strpos($_GET["path"], "favicon.ico"))
                {
                    ob_clean();
                    ob_end_flush();
                    exit;
                }

            try
            {
                require self::getScript();
            }
            catch (Exception $x)
            {
                Respond::exception($x);
            }
        }
    }
?>