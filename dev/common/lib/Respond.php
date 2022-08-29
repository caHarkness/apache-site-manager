<?php
    /*
        Respond.php

        A static class library for handing common responses in the framework.
    */
    class Respond
    {
        private static function commonHeaders()
        {
            $strDate = gmdate("D, d M Y H:i:s");

            header("Expires: Tue, 15 Jun 1995 01:09:00 GMT");
            header("Last-Modified: $strDate GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }

        public static function status($intCode, $strReason)
        {
            $intCode = intval($intCode);
            ob_clean();

            header(trim("HTTP/1.0 {$intCode} {$strReason}"));
            header("Content-Type: text/plain");
            echo "{$intCode} {$strReason}";

            Logger::log("Status {$intCode} {$strReason} returned.");

            ob_end_flush();
            exit;
        }

        public static function exception($x)
        {
            ob_clean();
            header("Content-Type: text/plain");

            $varHelper =
                array(
                    "error" => $x->getMessage(),
                    "error_code" => $x->getCode(),
                    "error_file" => $x->getFile(),
                    "error_line" => $x->getLine(),
                    "path" => Request::getPath());

            $strFormatted           = json_encode($varHelper, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $strNotFormatted        = json_encode($varHelper, JSON_UNESCAPED_SLASHES);
            $strExceptionMessage    = $x->getMessage();

            echo $strFormatted;
            Logger::log("Exception thrown: $strExceptionMessage $strNotFormatted");

            ob_end_flush();
            exit;
        }

        public static function text($varInput)
        {
            ob_clean();
            Respond::commonHeaders();
            header("Content-Type: text/plain");

            echo $varInput;
            ob_end_flush();
            exit;
        }

        public static function json($varInput)
        {
            ob_clean();
            Respond::commonHeaders();
            header("Content-Type: application/json");
            $strOutput = trim(json_encode($varInput, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE));

            echo $strOutput;
            ob_end_flush();
            exit;
        }

        // If at some point during the execution of our logic we need to send the user elsewhere, we can call this function to stop what we are doing, empty the response, and apply a redirect header to inform the user of their new destination. If we pass two additional arguments, we will set the cookie "argument 2" to the value of "argument 3." This is how we pass messages along to the user if we want to notify them of something that happened during their request, e.g. Respond::redirect("/", "AlertWarning", "You are not allowed to do that");
        public static function redirect($strLocation)
        {
            ob_clean();

            if (func_num_args() > 2)
            {
                Cookies::set(
                    func_get_arg(1),
                    func_get_arg(2));

                Cookies::finalize();
            }

            header("Location: $strLocation");

            ob_end_flush();
            exit;
        }

        public static function blank()
        {
            ob_clean();
            ob_end_flush();
            exit;
        }

        public static function file($strPath)
        {
            $varPathParts    = explode("/", $strPath);
            $strLastPathPart = $varPathParts[count($varPathParts) - 1];
            $strFileName     = $strLastPathPart;
            $strContentType  = mime_content_type($strPath);

            ob_clean();
            Respond::commonHeaders();
            header("Content-Type: $strContentType");
            header("Content-Disposition: attachment; filename={$strFileName}");
            define("CHUNK_SIZE", 1024 * 1024);

            $varBuffer = "";
            $varFile   = fopen($strPath, "rb");

            if ($varFile === false)
                return false;

            while (!feof($varFile))
            {
                $varBuffer = fread($varFile, CHUNK_SIZE);
                echo $varBuffer;

                ob_flush();
                flush();
            }

            $varStatus = fclose($varFile);

            ob_end_flush();
            exit;
        }

        public static function download($strPath)
        {
            ob_clean();
            Respond::commonHeaders();
            header("Content-Type: application/force-download");

            echo file_get_contents($strPath);

            ob_end_flush();
            exit;
        }
    }
?>