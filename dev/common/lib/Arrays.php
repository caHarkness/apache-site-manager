<?php
    /*
        Arrays.php

        A utility class for common operation on arrays.
    */

    class Arrays
    {
        // Used to safely get a key value from an array without PHP barking about trying to access either a null value or a key that doesn't exist. Passing a third, optional argument will return that value if something "falsy" is found. This should really only be used when displaying text on a page.
        public static function get($varArray, $strKey)
        {
            $strDefault = null;

            if (func_num_args() > 2)
                $strDefault = func_get_arg(2);

            if ($varArray == null)
                return $strDefault;

            if (array_key_exists($strKey, $varArray))
            {
                $varOutput = $varArray[$strKey];

                if ($varOutput == null || strlen($varOutput) < 1)
                    $varOutput = $strDefault;

                return $varOutput;
            }

            return $strDefault;
        }

        // Used to combine a mixture of values and arrays of values as one flat array of values in the order they arrive in. For example: Arrays::flatten("a", ["b", "c", ["d", "e", "f"], "g"], "h") should return an array of ["a", "b", "c", "d", "e", "f", "g", "h"].
        public static function flatten(...$args)
        {
            if (is_null($args))
                return [];

            if (count($args) > 0)
            {
                $varOutput = [];
                $varFirst = array_shift($args);

                if (is_array($varFirst))
                    foreach ($varFirst as $varItem)
                        $varOutput = array_merge($varOutput, self::flatten($varItem));
                else
                array_push($varOutput, $varFirst);

                if (count($args) > 0)
                    $varOutput = array_merge($varOutput, self::flatten($args));

                return $varOutput;
            }
            else
            return [];
        }
    }
?>