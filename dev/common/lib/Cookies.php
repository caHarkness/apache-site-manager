<?php
    class Cookies
    {
        private static $arr = null;

        public static function load()
        {
            self::$arr = array();

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