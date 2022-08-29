<?php
    /*
        Cookies.php

        A static class library used to handle the "getting" and "setting" of cookies for cross-session communication. This library does not actually get or set standard cookies, it only reads and writes to one cookie named "Persistent" which is encoded as a JSON string when accessed.
    */

    class Cookies
    {
        private static $arr = null;

        // Called by the framework prior to any of the logic that determines the page to render.
        public static function load()
        {
            self::$arr = [];

            try
            {
                if (isset($_COOKIE) && $_COOKIE != null)
                    if (isset($_COOKIE["Persistent"]))
                        self::$arr = json_decode($_COOKIE["Persistent"], true);
            }
            catch (Exception $x) {}
        }

        public static function get($strKey)
        {
            if (self::has($strKey))
                return self::$arr[$strKey];
            else
            return null;
        }

        public static function has($strKey)
        {
            if (self::$arr == null)
                self::$arr = array();

            if (isset(self::$arr[$strKey]))
                if (strlen(self::$arr[$strKey]) > 0)
                    return true;

            return false;
        }

        public static function pop($strKey)
        {
            $val = self::get($strKey);
            self::unset($strKey);
            return $val;
        }

        public static function set($strKey, $strValue)
        {
            if (self::$arr == null)
                self::$arr = array();

            self::$arr[$strKey] = $strValue;
        }

        public static function unset($strKey)
        {
            if (self::has($strKey))
            {
                self::$arr[$strKey] = null;
                unset(self::$arr[$strKey]);
            }
        }

        // Called by the framework right before the output buffer is flushed in a normal request that hasn't tampered with the output buffer in some way.
        public static function finalize()
        {
            setcookie(
                "Persistent",
                json_encode(self::$arr, JSON_UNESCAPED_SLASHES),
                time() + 60 * 60 * 24 * 30,
                "/");
        }
    }
?>