<?php
    /*
        Post.php

        A utility class for handling POST requests and getting the information from them.
    */

    class Post
    {
        // Returns a boolean on whether or not a POST request has all the supplied arguments as keys of the $_POST variable.
        public static function has()
        {
            if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] != "POST")
                return false;

            if (func_num_args() > 0)
            {
                foreach (func_get_args() as $strKey)
                    if (isset($_POST[$strKey]) == false)
                        return false;

                return true;
            }

            return false;
        }

        // Returns the size of the POST request made to the server.
        public static function size()
        {
            $intSize = -1;

            if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST")
                $intSize = $_SERVER["CONTENT_LENGTH"];

            return $intSize;
        }

        // Returns the value of a POST field or otherwise return null if it doesn't exist.
        public static function value($strField)
        {
            if (isset($_POST[$strField]))
                    return $_POST[$strField];
            else    return null;
        }

        public static function values()
        {
            if (isset($_POST))
                    return $_POST;
            else    return null;
        }

        public static function merge($varArray)
        {
            return
            array_merge(
                Post::values(),
                $varArray);
        }
    }
?>