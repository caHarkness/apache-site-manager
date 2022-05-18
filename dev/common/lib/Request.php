<?php
    class Request
    {
        static $varPath;
        static $varPathInfo;

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
            explode(
                "/",
                self::getPathInfo()["complete"]);
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
            explode(
                "/",
                self::getPathInfo()["args"]);
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