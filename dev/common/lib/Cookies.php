<?php
    class Cookies
    {
        private static $arr = null;

        public static function load()
        {
            Cookies::$arr = array();

            try
            {
                if (isset($_COOKIE) && $_COOKIE != null)
                    if (isset($_COOKIE["Persistent"]))
                        Cookies::$arr = json_decode($_COOKIE["Persistent"], true);
            }
            catch (Exception $x) {}
        }

        public static function get($strKey)
        {
            if (Cookies::has($strKey))
                return Cookies::$arr[$strKey];
            else
            return null;
        }

        public static function has($strKey)
        {
            if (Cookies::$arr == null)
                Cookies::$arr = array();

            if (isset(Cookies::$arr[$strKey]))
                if (strlen(Cookies::$arr[$strKey]) > 0)
                    return true;

            return false;
        }

        public static function pop($strKey)
        {
            $val = Cookies::get($strKey);
            Cookies::unset($strKey);
            return $val;
        }

        public static function set($strKey, $strValue)
        {
            if (Cookies::$arr == null)
                Cookies::$arr = array();

            Cookies::$arr[$strKey] = $strValue;
        }

        public static function unset($strKey)
        {
            if (Cookies::has($strKey))
            {
                Cookies::$arr[$strKey] = null;
                unset(Cookies::$arr[$strKey]);
            }
        }

        public static function finalize()
        {
            setcookie(
                "Persistent",
                json_encode(Cookies::$arr, JSON_UNESCAPED_SLASHES),
                time() + 60 * 60 * 24 * 30,
                "/");
        }
    }
?>