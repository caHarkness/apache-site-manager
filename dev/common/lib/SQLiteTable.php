<?php
    class SQLiteTable
    {
        public $arrSet;
        public $strTableName;
        public $varPDO;

        function __construct($arrInput)
        {
            $this->arrSet       = $arrInput;
            $this->strTableName = "temp";
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
            $output = array();
            $query  = $this->varPDO->prepare($input);
            $args   = Tools::flatten($args);

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