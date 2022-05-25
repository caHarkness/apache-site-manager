<?php
    class Curl
    {
        private static function _internal($strAddress)
        {
            $strOutput  = "";
            $varCurl    = curl_init($strAddress);

            curl_setopt($varCurl, CURLOPT_URL, $strAddress);
            curl_setopt($varCurl, CURLOPT_HEADER, 0);
            curl_setopt($varCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($varCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($varCurl, CURLOPT_SSL_VERIFYPEER, false);

            $strOutput .= curl_exec($varCurl);
            
            curl_close($varCurl);
            return $strOutput;
        }

        private static function _async($strAddress)
        {
            try
            {
                exec("curl -k \"$strAddress\" > /dev/null 2>/dev/null &");
                return true;
            }
            catch (Exception $x) {}
            return false;
        }

        public static function trim($strPart)
        {
            $strPart = trim($strPart);
            $strPart = trim($strPart, "/");
            return $strPart;
        }

        public static function fix($strUri)
        {
            while (strpos($strUri, "//"))
                $strUri = str_replace("//", "/", $strUri);

            return $strUri;
        }

        public static function get(...$varArgs)
        {
            $strUri = "";
            $varArgs = Tools::flatten($varArgs);

            foreach ($varArgs as $strArg)
            {
                if (is_string($strArg))
                    $strUri .= trim($strArg) . "/";
            }

            $strUri         = self::trim($strUri);
            $strUri         = self::fix($strUri);
            $strResponse    = self::_internal($strUri);
            
            return
            $strResponse;
        }

        public static function getAsync(...$varArgs)
        {
            $strUri = "";
            $varArgs = Tools::flatten($varArgs);

            foreach ($varArgs as $strArg)
            {
                if (is_string($strArg))
                    $strUri .= trim($strArg) . "/";
            }

            $strUri = self::trim($strUri);
            $strUri = self::fix($strUri);
            
            return
            self::_async($strUri);
        }
    }
?>