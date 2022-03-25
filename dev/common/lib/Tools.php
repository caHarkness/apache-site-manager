<?php
    class Tools
    {
        // Get all the keys of every object in the order they come in.
        public static function keys(...$args)
        {
            $varKeys = array();

            foreach ($args as $varObject)
            {
                foreach ($varObject as $k => $v)
                    if (is_string($k))
                        array_push($varKeys, $k);
            }

            return $varKeys;
        }

        // Get all the values of every object in the order they come in.
        public static function values(...$args)
        {
            $varValues = array();

            foreach ($args as $varObject)
            {
                foreach ($varObject as $k => $v)
                    if (is_string($k))
                        array_push($varValues, $v);
            }

            return $varValues;
        }

        // Flatten a combination of strings and arrays of strings into a single string array.
        public static function flatten(...$args)
        {
            if (is_null($args))
                return array();

            if (count($args) > 0)
            {
                $varOutput = array();
                $varFirst = array_shift($args);

                if (is_array($varFirst))
                    foreach ($varFirst as $varItem)
                        $varOutput = array_merge($varOutput, Tools::flatten($varItem));
                else
                array_push($varOutput, $varFirst);

                if (count($args) > 0)
                    $varOutput = array_merge($varOutput, Tools::flatten($args));

                return $varOutput;
            }
            else
            return array();
        }
    }
?>