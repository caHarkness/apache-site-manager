<?php
    /*
        SQLiteTable.php

        An object class for converting an array of key-value pairs into a SQLite table that can be queried. This class is purely for convenience and allows for further querying of information after we get data back from either SQL Server or MySQL Server.

        Usage:  (new SQLiteTable($varRows))->query("select * from tbl order by cast(`Age` as INTEGER) desc");

        Note:   The table name will always be "tbl" and all of its columns will always have the TEXT data type.
    */

    class SQLiteTable
    {
        public $arrSet;
        public $strTableName;
        public $varPDO;

        function __construct($arrInput)
        {
            $this->arrSet       = $arrInput;
            $this->strTableName = "tbl";
            $this->initializePDO();
        }

        private function initializePDO()
        {
            $this->varPDO = new PDO("sqlite::memory:");
            $this->varPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (count($this->arrSet) > 0)
            {
                $strColumns     = "";
                $strColumnsBare = "";

                foreach ($this->arrSet[0] as $k => $v)
                {
                    $strColumns     .= "`$k` text NULL, ";
                    $strColumnsBare .= "`$k`, ";
                }

                $strColumns     = rtrim($strColumns, ", ");
                $strColumnsBare = rtrim($strColumnsBare, ", ");
                $strStatement   = "create table if not exists `{$this->strTableName}` ($strColumns);";

                $this->query($strStatement);

                foreach ($this->arrSet as $r)
                {
                    $arrValues      = array();
                    $strValuesBare  = "";

                    foreach ($r as $k => $v)
                    {
                        array_push($arrValues, $v);
                        $strValuesBare .= "?, ";
                    }

                    $strValuesBare  = rtrim($strValuesBare, ", ");
                    $strStatement   = "insert into `{$this->strTableName}` ($strColumnsBare) values ($strValuesBare);";

                    $this->query($strStatement, $arrValues);
                }
            }
        }

        public function query($input, ...$args)
        {
            if ($this->arrSet == null || count($this->arrSet) < 1)
                return [];

            $output = array();
            $query  = $this->varPDO->prepare($input);
            $args   = Arrays::flatten($args);

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

            foreach ($output as $i => $row)
                foreach ($row as $key => $value)
                    if (is_numeric($key))
                        unset($output[$i][$key]);

            return
            array_values($output);
        }
    }
?>