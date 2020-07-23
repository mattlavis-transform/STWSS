<?php
class measure
{
    // Class properties and methods go here
    public $measure_sid                 = Null;
    public $geographical_area_id        = "";
    public $geographical_description    = "";
    public $measure_type_id             = "";
    public $measure_type_description    = "";
    public $measure_realm               = "";
    public $import                      = null;
    public $vat                         = null;
    public $excise                      = null;


    function valid_measure_type()
    {
        global $app;
        $valid = false;

        foreach ($app->measure_type_ranges as $mtr) {
            if (($this->measure_type_id >= $mtr->from) && ($this->measure_type_id <= $mtr->to)) {
                $valid = true;
                break;
            }
        }
        return ($valid);
    }
}
