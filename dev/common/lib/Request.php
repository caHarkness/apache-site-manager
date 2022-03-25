<?php
    class Request
    {
        static $varPath;

        static function getPath()
        {
            return
            self::$varPath;
        }

        static function process()
        {
            // Completely ignore favicon.ico requests
            if (isset($_GET["path"]))
                if (strpos($_GET["path"], "favicon.ico"))
                    return;

            try
            {
                if (isset($_GET["path"]))
                {
                    self::$varPath = explode(
                        "/",
                        substr($_GET["path"], 1));
                }
                else
                self::$varPath = array();


                if (count(self::getPath()) > 1)
                {
                    $strFile =
                        "pages/" . 
                        self::getPath()[0] . "/" .
                        self::getPath()[1] . ".php";

                    if (file_exists($strFile)) { require $strFile; }
                    else
                    {
                        $strFile =
                            "pages/" . 
                            self::getPath()[0] .
                            "/index.php";

                        if (file_exists($strFile)) { require $strFile; }
                        else
                        {
                            $strFile =
                                "pages/" . 
                                self::getPath()[0] .
                                ".php";

                            if (file_exists($strFile))  { require $strFile; }
                            else                        { throw new Exception("Controller action not found."); }
                        }
                    }
                }
                else if (count(self::getPath()) == 1)
                {
                    $strFile =
                        "pages/" . 
                        self::getPath()[0] .
                        "/index.php";

                    if (file_exists($strFile)) { require $strFile; }
                    else
                    {
                        $strFile =
                            "pages/" . 
                            self::getPath()[0] .
                            ".php";

                        if (file_exists($strFile))  { require $strFile; }
                        else                        { throw new Exception("Action '$strFile' not found."); }
                    }
                }
                else
                {
                    if (file_exists("pages/index.php")) { require "pages/index.php"; }
                    else                                { throw new Exception("Default action not found."); }
                }
            }
            catch (Exception $x)
            {
                Respond::exception($x);
            }
        }
    }
?>