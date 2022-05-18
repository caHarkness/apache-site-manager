<?php
    class Time
    {
        public static function toSeconds($strDateTime)
        {
            $varThen = new DateTime("$strDateTime");
            return intval($varThen->format("U"));
        }

        public static function since($strDateTime)
        {
            $varNow     = new DateTime();
            $varThen    = new DateTime("$strDateTime");

            $intNowSeconds      = intval($varNow->format("U"));
            $intThenSeconds     = intval($varThen->format("U"));
            $intElapsedSeconds  = $intNowSeconds - $intThenSeconds;

            return $intElapsedSeconds;
        }

        public static function getFriendlyDateTime($strDateTime)
        {
            if ($strDateTime == null || strlen($strDateTime) < 1)
                return "None";

            $varDateTime = null;

            if (is_numeric($strDateTime))
                $varDateTime = new DateTime("@$strDateTime");
            else $varDateTime = new DateTime($strDateTime);

            return $varDateTime->format("D, M j, Y \a\\t g:i a");
        }

        public static function getFriendlyDate($strDateTime)
        {
            $varDateTime = null;

            if (is_numeric($strDateTime))
                $varDateTime = new DateTime("@$strDateTime");
            else $varDateTime = new DateTime("$strDateTime");

            return $varDateTime->format("n/j/Y");
        }

        public static function getFriendlyTime($strDateTime)
        {
            $varDateTime = null;

            if (is_numeric($strDateTime))
                $varDateTime = new DateTime("@$strDateTime");
            else $varDateTime = new DateTime("$strDateTime");

            return $varDateTime->format("g:i a");
        }

        // Convert a number of seconds into something like "5d 23h 59m"
        public static function getFriendlyElapsedTime($intSeconds)
        {
            if (!is_numeric($intSeconds))
                return "NaN";

            if ($intSeconds < 1)
                return "None";

            $intMinutes = 0;

            while ($intSeconds > 59)
            {
                $intMinutes++;
                $intSeconds -= 60;
            }

            $intHours = 0;
            while ($intMinutes > 59)
            {
                $intHours++;
                $intMinutes -= 60;
            }

            $intDays = 0;
            while ($intHours > 23)
            {
                $intDays++;
                $intHours -= 24;
            }

            $intYears = 0;
            while ($intDays > 364)
            {
                $intYears++;
                $intDays -= 365;
            }

            $strOutput = "";

            if ($intYears > 0) return "{$intYears}y";

            if ($intDays > 0) $strOutput = "$strOutput {$intDays}d";

            if ($intDays > 2)
                return "{$intDays}d";

            if ($intHours > 0) $strOutput = "$strOutput {$intHours}h";
            if ($intMinutes > 0) $strOutput = "$strOutput {$intMinutes}m";

            $strOutput = trim($strOutput);

            if (strlen($strOutput) < 1)
                $strOutput = "0m";

            return $strOutput;
        }
    }
?>