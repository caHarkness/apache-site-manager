<?php
    class Text
    {
        public static function startsWith($haystack, $needle)
        {
            return
                $needle === "" ||
                strrpos(
                    $haystack,
                    $needle,
                    -strlen($haystack)
                ) !== false;
        }

        public static function endsWith($haystack, $needle)
        {
            return
                $needle === "" ||
                (
                    ($temp = strlen($haystack) - strlen($needle)) >= 0 &&
                    strpos(
                        $haystack,
                        $needle,
                        $temp
                    ) !== false
                );
        }

        public static function contains($strSubject, $strNeedle)
        {
            return strpos(
                $strSubject,
                $strNeedle
            ) !== false;
        }

        public static function containsAny($strSubject, $varTextArray)
        {
            $strSubject = trim($strSubject);
            $strSubject = strtolower($strSubject);

            foreach ($varTextArray as $strNeedle)
                if (Text::contains($strSubject, strtolower($strNeedle)))
                    return true;

            return false;
        }

        public static function generateGuid()
        {
            $strChars = "0123456789abcdef";
            $strOutput = "";

            for ($i = 0; $i < 8; $i++)
                $strOutput .= $strChars[rand(0, strlen($strChars) - 1)];

            $strOutput .= "-";
            for ($i = 0; $i < 4; $i++)
                $strOutput .= $strChars[rand(0, strlen($strChars) - 1)];

            $strOutput .= "-";
            for ($i = 0; $i < 4; $i++)
                $strOutput .= $strChars[rand(0, strlen($strChars) - 1)];

            $strOutput .= "-";
            for ($i = 0; $i < 4; $i++)
                $strOutput .= $strChars[rand(0, strlen($strChars) - 1)];

            $strOutput .= "-";
            for ($i = 0; $i < 12; $i++)
                $strOutput .= $strChars[rand(0, strlen($strChars) - 1)];

            return $strOutput;
        }

        public static function join(...$varParts)
        {
            $strOutput = "";
            foreach ($varParts as $p)
            try
            {
                $strOutput .= $p;
            }
            catch (Exception $x) {}
            return $strOutput;
        }
    }
?>