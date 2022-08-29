<?php
    class SQLPrepare
    {
        /*
            Turns an array of strings into one string like: ('abc', 'def', 'ghi')
        */
        public static function set($arrValues)
        {
            $strSet = "";

            foreach ($arrValues as $v)
                $strSet .= "'$v', ";

            $strSet = trim($strSet, ", ");
            return "($strSet)";
        }
    }
?>