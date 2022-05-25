<?php
    class Render
    {
        public static function partial($strPath, $varObject)
        {
            $varModel = $varObject;
            require "parts/{$strPath}.php";
        }
    }
?>