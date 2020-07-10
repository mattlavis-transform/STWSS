<?php
class commodity
{
    // Class properties and methods go here
    public $goods_nomenclature_sid = Null;
    public $goods_nomenclature_item_id = "";
    public $productline_suffix = "";
    public $number_indents = Null;
    public $description = "";
    public $declarable = Null;
    public $certificates = array();
    public $hierarchies = array();
    public $hierarchy_string = "";
    public $import_measures = array();
    public $export_measures = array();
    public $content = array();

    public function get_details()
    {
        $root = "https://www.trade-tariff.service.gov.uk/api/v2/headings/";
        $url = $root . substr($this->goods_nomenclature_item_id, 0, 4);
    global $conn;
        $sql = "select goods_nomenclature_sid, goods_nomenclature_item_id, productline_suffix,
        description, number_indents, declarable 
        from goods_nomenclatures gn where goods_nomenclature_item_id = $1
        and productline_suffix = $2";

        if ($this->productline_suffix == "") {
            $this->productline_suffix = "80";
        }
        pg_prepare($conn, "get_details", $sql);
        $result = pg_execute($conn, "get_details", array($this->goods_nomenclature_item_id, $this->productline_suffix));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $this->goods_nomenclature_sid = $row["goods_nomenclature_sid"];
            $this->goods_nomenclature_item_id = $row["goods_nomenclature_item_id"];
            $this->description = $row["description"];
            $this->productline_suffix = $row["productline_suffix"];
            $this->number_indents = $row["number_indents"];
            $this->declarable = $row["declarable"];
        }
    }

    public function get_hierarchy()
    {
        if (substr($this->goods_nomenclature_item_id, -6) == "000000") {
            $root = "https://www.trade-tariff.service.gov.uk/api/v2/headings/";
            $url = $root . substr($this->goods_nomenclature_item_id, 0, 4);
            $is_heading = true;
        } else {
            $root = "https://www.trade-tariff.service.gov.uk/api/v2/commodities/";
            $url = $root . $this->goods_nomenclature_item_id;
            $is_heading = false;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $json = json_decode($output, true);

        // get the detais
        $this->description = $json["data"]["attributes"]["formatted_description"];
        $this->number_indents = $json["data"]["attributes"]["number_indents"];
        
        // Get the relationships
        $relationships = $json["data"]["relationships"];
        $section_id = $relationships["section"]["data"]["id"];
        $chapter_id = $relationships["chapter"]["data"]["id"];
        if ($is_heading == false) {
            $heading_id = $relationships["heading"]["data"]["id"];
            $ancestors = $relationships["ancestors"];
        }
        // Get the actual data from the included section
        $included = $json["included"];
        foreach ($included as $item) {
            $type = $item["type"];
            switch ($type) {
                case "section":
                    $h = new hierarchy();
                    $h->goods_nomenclature_item_id = $item["attributes"]["numeral"];
                    $h->description = $item["attributes"]["title"];
                    $h->type = "section";
                    $h->type_index = 0;
                    array_push($this->hierarchies, $h);
                    break;
                case "chapter":
                    $h = new hierarchy();
                    $h->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $h->description = $item["attributes"]["formatted_description"];
                    $h->type = "chapter";
                    $h->type_index = 1;
                    array_push($this->hierarchies, $h);
                    break;
                case "commodity":
                    $h = new hierarchy();
                    $h->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $h->productline_suffix = $item["attributes"]["producline_suffix"];
                    $h->description = $item["attributes"]["formatted_description"];
                    $h->type = "commodity";
                    $h->type_index = 2;
                    array_push($this->hierarchies, $h);
                    break;
            }
        }

        // termintte
        curl_close($curl);

        usort($this->hierarchies, "cmp");
        $indent = "&nbsp;&nbsp;&nbsp;";
        $mark = "-&nbsp;";
        $indent_count = 0;
        //pre($this->hierarchies);
        foreach ($this->hierarchies as $h) {
            /*
            $this->hierarchy_string .= str_repeat($indent, $indent_count);
            if ($indent_count > 0) {
                $this->hierarchy_string .= $mark;
            }
            */
            switch ($h->type) {
                case "section":
                    $this->hierarchy_string .= "<div class='c1'>Section " . $h->goods_nomenclature_item_id . "</div><div class='c2'>" . $h->description . "</div>";
                    break;
                case "chapter":
                    $this->hierarchy_string .= "<div class='c1'>Chapter " . substr($h->goods_nomenclature_item_id, 0, 2) . "</div><div class='c2'>" . $h->description . "</div>";
                    break;
                case "commodity":
                    $this->hierarchy_string .= "<div class='c1'>" . $h->formatted_commodity() . "</div><div class='c2'>" . $h->description . "</div>";
                    break;
            }

            //$this->hierarchy_string .= $h->goods_nomenclature_item_id . "&nbsp;" . $h->description . "<br />";
            $indent_count += 1;
        }
    }


    public function get_import_measures()
    {
        global $conn;
        $sql = "with cte as (select m.measure_sid, m.measure_type_id, mt.description as measure_type_description,
        m.geographical_area_id, ga.description as geographical_area_description, mt.measure_type_series_id,
        case when mt.measure_type_series_id in ('A', 'B') then 'Regulatory' else 'Financial' end as measure_realm
        from measures m, measure_association_goods_nomenclatures magn, measure_types mt, geographical_areas ga 
        where m.measure_sid = magn.measure_sid 
        and magn.goods_nomenclature_sid = $1
        and m.is_import = true
        and m.measure_type_id = mt.measure_type_id 
        and m.geographical_area_id = ga.geographical_area_id)
        select * from cte 
        order by measure_realm desc, measure_type_id, geographical_area_id;";

        pg_prepare($conn, "get_import_measures", $sql);
        $result = pg_execute($conn, "get_import_measures", array($this->goods_nomenclature_sid));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            while ($row = pg_fetch_array($result)) {
                $measure = new measure();
                $measure->measure_sid = $row["measure_sid"];
                $measure->measure_type_id = $row["measure_type_id"];
                $measure->measure_type_description = $row["measure_type_description"];
                $measure->geographical_area_id = $row["geographical_area_id"];
                $measure->geographical_area_description = $row["geographical_area_description"];
                $measure->measure_realm = $row["measure_realm"];
                array_push($this->import_measures, $measure);
            }
        }
    }


    public function get_document_codes()
    {
        global $conn;
        $sql = "select distinct mc.document_code, mc.requirement 
        from measure_conditions mc, measures m, measure_association_goods_nomenclatures magn 
        where mc.measure_sid = m.measure_sid 
        and m.measure_sid = magn.measure_sid 
        and magn.goods_nomenclature_sid = $1
        and mc.document_code is not null
        order by mc.document_code";

        pg_prepare($conn, "get_document_codes", $sql);
        $result = pg_execute($conn, "get_document_codes", array($this->goods_nomenclature_sid));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            while ($row = pg_fetch_array($result)) {
                $certificate = new certificate();
                $certificate->document_code = $row["document_code"];
                $certificate->description = $row["requirement"];
                array_push($this->certificates, $certificate);
            }
        }
    }

    public function get_signposting_steps()
    {
        global $conn;
        $sql = "select ssca.id as unique_id, ss.id, ss.step_description, ss.step_howto_description,
        ss.step_url, ss.header_id, ss.subheader_id, ssh.header_description, sss.subheader_description 
        from signposting_step_commodity_assignment ssca, signposting_steps ss, signposting_step_headers ssh, signposting_step_subheaders sss 
        where ss.id = ssca.signposting_step_id 
        and ss.header_id = ssh.id 
        and ss.subheader_id = sss.id 
        and code = $1
        order by ss.id;
        ";
        pg_prepare($conn, "get_signposting_steps", $sql);
        $result = pg_execute($conn, "get_signposting_steps", array($this->goods_nomenclature_item_id));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            while ($row = pg_fetch_array($result)) {
                $content = new content();
                $content->unique_id = $row['unique_id'];
                $content->id = $row['id'];
                $content->step_description = $row['step_description'];
                $content->step_howto_description = $row['step_howto_description'];
                $content->step_url = $row['step_url'];
                $content->header_id = $row['header_id'];
                $content->subheader_id = $row['subheader_id'];
                $content->header_description = $row['header_description'];
                $content->subheader_description = $row['subheader_description'];
                array_push($this->content, $content);

            }
        }
    }
}
