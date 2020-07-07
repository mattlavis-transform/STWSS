<?php
class data_item
{
    public $id = "";
    public $description = "";

    function __construct($id, $description)
    {
        $this->id = $id;
        $this->description = $description;
    }
}
