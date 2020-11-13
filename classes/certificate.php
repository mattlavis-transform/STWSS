<?php
class certificate
{
    // Class properties and methods go here
    public $code                    = "";
    public $certificate_code        = "";
    public $certificate_type_code   = "";
    public $description             = "";
    public $notes                   = "";
    public $content                 = "";
    public $commodities             = array();


    public function get_details() {
        global $conn;
        $sql = "select description from chieg.certificates where code = $1;";
        pg_prepare($conn, "get_details", $sql);
        $result = pg_execute($conn, "get_details", array($this->code));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $this->description = $row['description'];
        }
    }

    public function get_usage() {
        global $conn;
        $sql = "select gn.goods_nomenclature_item_id, gn.description, gn.number_indents 
        from chieg.goods_nomenclatures gn, chieg.measure_association_goods_nomenclatures magn,
        measures m, measure_conditions mc 
        where mc.measure_sid = m.measure_sid 
        and magn.measure_sid = m.measure_sid 
        and magn.goods_nomenclature_sid = gn.goods_nomenclature_sid 
        and gn.productline_suffix = '80'
        and mc.document_code = $1
        order by gn.goods_nomenclature_item_id ";
        pg_prepare($conn, "get_usage", $sql);
        $result = pg_execute($conn, "get_usage", array($this->code));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            while ($row = pg_fetch_array($result)) {
                $commodity = new commodity();
                $commodity->goods_nomenclature_item_id = $row["goods_nomenclature_item_id"];
                $commodity->description = $row["description"];
                $commodity->number_indents = $row["number_indents"];
                array_push($this->commodities, $commodity);
            }
        }
        return ($row_count);
        
    }

    public function get_content() {
        global $app;
        foreach($app->certificates_to_document_codes as $d) {
            if ($d->document_code == $this->code) {
                $this->content = $d->step_description;
                break;
            }
        }
    }
}
