<?php
    class Stopwatch
    {
        public $flTimeStart;
        public $flTimeEnd;
        public $flTimeMeasured;

        public function __construct()
        {
            $this->restart();
        }

        public function restart()
        {
            $this->flTimeStart = microtime(true);
        }

        public function measure()
        {
            $this->flTimeStop = microtime(true);
            $this->flTimeMeasured = $this->flTimeStop - $this->flTimeStart;

            if ($this->flTimeMeasured == null)
                $this->flTimeMeasured = 0.00;

            $strFormatted = number_format($this->flTimeMeasured, 2);

            return "{$strFormatted}s";
        }
    }
?>