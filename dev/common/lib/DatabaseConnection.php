<?php
    /*
        DatabaseConnection.php

        An object-class designed to make opening database connections and querying uniform across all implementations of database access, whether it is SQL Server or MySQL Server.
    */

    class DatabaseConnection
    {
        private $strEngine;
        private $strHost;
        private $strDatabaseName;
        private $strUsername;
        private $strPassword;
        private $pdo;

        public function __construct($strEngine = null, $strHost = null, $strDatabaseName = null, $strUsername = null, $strPassword = null)
        {
            if ($strEngine == null)
                if (defined("DBC_DEFAULT_ENGINE"))
                    $strEngine = DBC_DEFAULT_ENGINE;
                else throw new Exception("DBC_DEFAULT_ENGINE undefined.");

            if ($strHost == null)
                if (defined("DBC_DEFAULT_HOST"))
                    $strHost = DBC_DEFAULT_HOST;
                else throw new Exception("DBC_DEFAULT_HOST undefined.");

            if ($strDatabaseName == null)
                if (defined("DBC_DEFAULT_DATABASE_NAME"))
                    $strDatabaseName = DBC_DEFAULT_DATABASE_NAME;
                else throw new Exception("DBC_DEFAULT_DATABASE_NAME undefined.");

            if ($strUsername == null)
                if (defined("DBC_DEFAULT_USERNAME"))
                    $strUsername = DBC_DEFAULT_USERNAME;
                else throw new Exception("DBC_DEFAULT_USERNAME undefined.");

            if ($strPassword == null)
                if (defined("DBC_DEFAULT_PASSWORD"))
                    $strPassword = DBC_DEFAULT_PASSWORD;
                else throw new Exception("DBC_DEFAULT_PASSWORD undefined.");

            $this
                ->setEngine($strEngine)
                ->setHost($strHost)
                ->setDatabaseName($strDatabaseName)
                ->setUsername($strUsername)
                ->setPassword($strPassword);
        }

        public function setEngine($strEngine)
        {
            $this->strEngine = $strEngine;
            return $this;
        }

        public function setHost($strHost)
        {
            $this->strHost = $strHost;
            return $this;
        }

        public function setDatabaseName($strDatabaseName)
        {
            $this->strDatabaseName = $strDatabaseName;
            return $this;
        }

        public function setUsername($strUsername)
        {
            $this->strUsername = $strUsername;
            return $this;
        }

        public function setPassword($strPassword)
        {
            $this->strPassword = $strPassword;
            return $this;
        }

        public function connect()
        {
            switch ($this->strEngine)
            {
                case "sqlsrv":
                    $this->pdo = new PDO(
                        "sqlsrv:Server={$this->strHost};Database={$this->strDatabaseName}",
                        $this->strUsername,
                        $this->strPassword);
                    break;

                case "mysql":
                    $this->pdo = new PDO(
                        "mysql:host={$this->strHost};dbname={$this->strDatabaseName}",
                        $this->strUsername,
                        $this->strPassword);
                    break;

                default:
                    throw new Exception("Unknown database engine {$this->strEngine}.");
            }

            $this->pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION);

            return $this;
        }

        // If the first argument is a file, use its contents as the query to be prepared. All question marks in the query are replaced with the subsequent arguments in the order they are read. (This is the default behavior of the PDO object's prepare() method.)
        public function query($input)
        {
            $output = [];

            if (file_exists("db/{$input}"))
                $input = file_get_contents("db/{$input}");

            $query      = $this->pdo->prepare($input);
            $success    = null;

            if (func_num_args() > 1)
            {
                $args   = [];
                $_args  = func_get_args();

                array_shift($_args);

                foreach ($_args as $a)
                {
                    if (is_array($a))
                    {
                        foreach ($a as $aa)
                            $args[] = $aa;
                    }
                    else
                    $args[] = $a;
                }

                $query->execute($args);
            }
            else
            $query->execute();

            try
            {
                while ($row = $query->fetch())
                {
                    $tmp    = array();
                    $c      = 0;

                    foreach ($row as $k => $v)
                    {
                        if (!is_numeric($k))
                        {
                            $nk = $k;

                            if (strlen($nk) < 1)
                            {
                                $nk = $c;
                                $c++;
                            }

                            $tmp[$nk] = $v;
                        }
                    }

                    array_push($output, $tmp);
                }
            }
            catch (Exception $x) {}

            return
            array_values($output);
        }

        public static function toSQLServer($name)
        {
            return
            (new DatabaseConnection(
                "sqlsrv",
                "ip address",
                $name,
                "username",
                "password"))->connect();
        }

        public static function toMySQLServer()
        {
            $strDatabaseName = DATABASE_NAME . DATABASE_NAME_SUFFIX;

            if (func_num_args() == 1)
                $strDatabaseName = func_get_args()[0];

            return
            (new DatabaseConnection(
                "mysql",
                "localhost",
                $strDatabaseName,
                "root",
                "password"))->connect();
        }
    }
?>