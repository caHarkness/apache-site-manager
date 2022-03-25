<?php
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
            
            ob_end_flush();
            exit;
        }

        public static function exception($x)
        {
            ob_clean();
            header("Content-Type: text/plain");

            echo json_encode(
                array(
                    "error"         => $x->getMessage(),
                    "error_code"    => $x->getCode(),
                    "error_file"    => $x->getFile(),
                    "error_line"    => $x->getLine(),
                    "path"          => Request::getPath()),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

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
            echo json_encode($varInput, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            ob_end_flush();
            exit;
        }

        public static function redirect($strLocation)
        {
            ob_clean();

            if (func_num_args() === 3)
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

            ob_clean();
            Respond::commonHeaders();
            header("Content-Type: " . mime_content_type($strPath));
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