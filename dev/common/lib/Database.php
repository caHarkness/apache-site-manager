<?php
    //
    //  by Conner Harkness circa 2/21/2022
    //

    class Database
    {
        private static $strDatabaseName = "";
        private static $strLastDatabaseName = "";

        public static function getDatabaseName()
        {
            if (strlen(Database::$strDatabaseName) < 1)
                Database::$strDatabaseName = Database::getDefaultDatabaseName();

            return Database::$strDatabaseName;
        }

        public static function getLastDatabaseName()
        {
            return Database::$strLastDatabaseName;
        }

        public static function getDefaultDatabaseName()
        {
            return DATABASE_NAME . DATABASE_NAME_SUFFIX;
        }

        public static function use($strDatabaseName)
        {
            Database::$strDatabaseName = $strDatabaseName . DATABASE_NAME_SUFFIX;
            Database::$strLastDatabaseName = Database::$strDatabaseName;
        }

        public static function reset()
        {
            Database::$strDatabaseName = Database::getDefaultDatabaseName();
        }

        //
        //  Access via Database::query() anywhere throughout the code base.
        //  See example usage below.
        //
        public static function query($input, ...$args)
        {
            $output = array();
            $pdo    = new PDO(
                "mysql:host=" . DATABASE_HOST . ";dbname=" . Database::getDatabaseName(),
                DATABASE_USERNAME,
                DATABASE_PASSWORD);
            
            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION);

            $path = PROJECTS_DIR . APP_NAME . "/db/";

            if (file_exists("{$path}{$input}"))
                $input = file_get_contents("{$path}{$input}");

            $query = $pdo->prepare($input);
            $args = Tools::flatten($args);

            if (count($args) > 0)
                $query->execute($args);
            else
            $query->execute();

            try
            {
                while ($row = $query->fetch())
                    array_push($output, $row);
            }
            catch (Exception $x) {}

            $pdo = null;

            foreach ($output as $i => $row)
                foreach ($row as $key => $value)
                    if (is_numeric($key))
                        unset($output[$i][$key]);

            Database::reset();

            return
            array_values($output);
        }
    }
?>