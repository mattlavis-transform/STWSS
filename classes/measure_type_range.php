<?php
class measure_type_range
{
    public $from = null;
    public $to = null;

    function __construct($from, $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }
}
