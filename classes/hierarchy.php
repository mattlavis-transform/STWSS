<?php
class hierarchy
{
    // Class properties and methods go here
    public $goods_nomenclature_item_id  = "";
    public $productline_suffix          = "";
    public $description                 = "";
    public $type                        = "";
    public $type_index                  = "";

    public function formatted_commodity() {
        $s = "";
        $s .= "<span class='mono'>" . substr($this->goods_nomenclature_item_id, 0, 2) . "</span>";
        $s .= "<span class='mono'>" . substr($this->goods_nomenclature_item_id, 2, 2) . "</span>";
        $s .= "<span class='mono'>" . substr($this->goods_nomenclature_item_id, 4, 2) . "</span>";
        $s .= "<span class='mono'>" . substr($this->goods_nomenclature_item_id, 6, 2) . "</span>";
        $s .= "<span class='mono'>" . substr($this->goods_nomenclature_item_id, 8, 2) . "</span>";
        if ($this->productline_suffix != "80") {
            $s .= " (" . $this->productline_suffix . ")";
        }
        //$s .= "</span>";
        return ($s);
    }
}
