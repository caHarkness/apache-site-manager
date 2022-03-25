<?php
    // We only want to write this section once, so this is our library loader
    class Config
    {
        //  Global configuration accessible via Config::$value["key"] ...returns "value." Do not edit this directly. Add your configuration key value pairs in the config.php files with Config::$value["key"] = "value."
        public static $value = array(
            "KEY" => "Value"
        );

        private static $lib = array();

        // Load all the .php scripts from the specified project's lib directory
        static function loadLibrariesFrom($strProjectName)
        {
            $strDirectory = Config::$value["PROJECTS_DIR"] . $strProjectName . "/lib";

            $arrDirectory = scandir($strDirectory);
            unset($arrDirectory[0]);
            unset($arrDirectory[1]);
            $arrDirectory = array_values($arrDirectory);

            foreach ($arrDirectory as $strFile)
                if (preg_match("#[A-Za-z0-9_\-]*.php#", $strFile))
                {
                    if (!(in_array($strFile, Config::getLoadedLibraries())))
                    {
                        require "$strDirectory/$strFile";
                        array_push(Config::$lib, $strFile);
                    }
                }
        }

        static function getLoadedLibraries()
        {
            return Config::$lib;
        }

        // This is called to declare all config key-value pairs as PHP constants. It's a workaround for the inability to "define()" constants more than once. We only want them to be constants when all the configuration is processed and the application is ready to start performing logic and rendering views.
        static function finalize()
        {
            foreach (Config::$value as $strKey => $varValue)
            {
                try
                {
                    if (!defined($strKey))
                        define($strKey, $varValue);
                }
                catch (Exception $x) {}
            }
        }
    }
?>