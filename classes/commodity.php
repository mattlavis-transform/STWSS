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
    public $is_heading = null;
    public $commodity_type = null;
    public $curl = null;
    public $json = null;

    public function validate()
    {
        /*
        Purpose - Check whether the commodity exists
        Step 1  - Check on the commodity page itself; if page exists then the commodity exists
        Step 2  - Check on the headings page: look at the 1st 4 digits, then bring back all the child codes
                  If one of the child codes matches, then we are cool
        */
        $this->pad_commodity_code();

        $retval = null;
        $root = "https://www.trade-tariff.service.gov.uk/api/v2/commodities/";
        $url = $root . $this->goods_nomenclature_item_id;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($this->curl);
        $this->json = json_decode($output, true);

        if (isset($this->json["data"])) {
            $data = $this->json["data"];
            $this->goods_nomenclature_sid = $this->json["data"]["id"];
            $this->description = $this->json["data"]["attributes"]["formatted_description"];
            $this->number_indents = $this->json["data"]["attributes"]["number_indents"];
            $this->productline_suffix = "80";
            $this->commodity_type = "endline";
            $retval = true; // Returns true if the commodity code exists as a leaf / end-line only
            //h1("I am an end line and exist in the database");
        } else {
            $root = "https://www.trade-tariff.service.gov.uk/api/v2/headings/";
            $url = $root . substr($this->goods_nomenclature_item_id, 0, 4);
            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_URL, $url);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($this->curl);
            $this->json = json_decode($output, true);

            $found = false;
            if (isset($this->json["included"])) {
                $included = $this->json["included"];
                foreach ($included as $item) {
                    if ($item["type"] == "commodity") {
                        if ($item["attributes"]["goods_nomenclature_item_id"] == $this->goods_nomenclature_item_id) {
                            if ($item["attributes"]["producline_suffix"] == "80") {
                                $this->goods_nomenclature_sid = $item["id"];
                                $this->number_indents = $item["attributes"]["number_indents"];
                                $this->productline_suffix = "80";
                                $this->description = $item["attributes"]["formatted_description"];
                                $this->commodity_type = "intermediate";
                                $found = true;
                                break;
                            }
                        }
                    }
                }
                $retval = $found;
                if ($found == true) {
                    //h1("I am an intermediate code and I exist in the database");
                    $retval = true;
                } else {
                    if (isset($this->json["data"])) {
                        //h1("I am a heading");
                        $this->goods_nomenclature_sid = $this->json["data"]["id"];
                        $this->description = $this->json["data"]["attributes"]["formatted_description"];
                        $this->number_indents = 0;
                        $this->productline_suffix = "80";
                        $this->commodity_type = "heading";
                        $retval = true;
                    }
                }
            } else {
                $retval = false;
                $this->commodity_type = null;
                //h1("I do not exist");
            }
        }
        $this->terminate_curl();
        return ($retval);
    }

    public function pad_commodity_code()
    {
        $this->goods_nomenclature_item_id = str_replace(" ", "", $this->goods_nomenclature_item_id);
        $this->goods_nomenclature_item_id = trim($this->goods_nomenclature_item_id);
        $this->goods_nomenclature_item_id = str_pad($this->goods_nomenclature_item_id, 10, "0");
    }

    public function get_commodity_from_api()
    {
        $ret = $this->validate();

        // Get the relationships
        $relationships = $this->json["data"]["relationships"];
        $section_id = $relationships["section"]["data"]["id"];
        $chapter_id = $relationships["chapter"]["data"]["id"];
        if ($this->is_heading == false) {
            if (isset($relationships["heading"])) {
                $heading_id = $relationships["heading"]["data"]["id"];
            }
            if (isset($relationships["ancestors"])) {
                $ancestors = $relationships["ancestors"];
            }
        }

        //h1($this->commodity_type);
        if ($this->commodity_type != "endline") {
            $data = $this->json["data"];

            $h = new hierarchy();
            $h->goods_nomenclature_item_id = $data["attributes"]["goods_nomenclature_item_id"];
            $h->productline_suffix = "80";
            $h->description = $data["attributes"]["formatted_description"];
            $h->type = "heading";
            $h->type_index = 3;
            $h->url = "/commodities/view.html?goods_nomenclature_item_id=" . substr($h->goods_nomenclature_item_id, 0, 4);
            array_push($this->hierarchies, $h);

            if ($this->commodity_type == "intermediate") {
                $included = $this->json["included"];
                $found = true;
                $sid = $this->goods_nomenclature_sid;
                do {
                    $found = false;
                    foreach ($included as $item) {
                        if ($item["id"] == $sid) {
                            $sid = $item["attributes"]["parent_sid"];
                            $found = true;
                            if ($sid != $this->goods_nomenclature_sid) {

                                $h = new hierarchy();
                                $h->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                                $h->productline_suffix = $item["attributes"]["producline_suffix"];
                                $h->description = $item["attributes"]["formatted_description"];
                                $h->type = "commodity";
                                $h->type_index = 3;
                                $h->url = "/commodities/view.html?goods_nomenclature_item_id=" . $h->goods_nomenclature_item_id . "&productline_suffix=" . $h->productline_suffix;
                                array_push($this->hierarchies, $h);
                            }
                            break;
                        }
                    }
                } while ($found == true);
            }
        }

        // Get the actual data from the included section
        $included = $this->json["included"];
        foreach ($included as $item) {
            $type = $item["type"];
            switch ($type) {
                case "section":
                    $h = new hierarchy();
                    $h->goods_nomenclature_item_id = $item["attributes"]["numeral"];
                    $h->description = $item["attributes"]["title"];
                    $h->type = "section";
                    $h->type_index = 0;
                    $h->url = "/sections/view.html?id=" . $item["id"];
                    array_push($this->hierarchies, $h);
                    break;

                case "chapter":
                    $h = new hierarchy();
                    $h->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $h->description = $item["attributes"]["formatted_description"];
                    $h->type = "chapter";
                    $h->type_index = 1;
                    $h->url = "/chapters/view.html?id=" . substr($h->goods_nomenclature_item_id, 0, 2);
                    array_push($this->hierarchies, $h);
                    break;

                case "heading":
                    $h = new hierarchy();
                    $h->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $h->description = $item["attributes"]["formatted_description"];
                    $h->type = "heading";
                    $h->type_index = 2;
                    $h->url = "/commodities/view.html?goods_nomenclature_item_id=" . substr($h->goods_nomenclature_item_id, 0, 4);
                    array_push($this->hierarchies, $h);
                    break;

                case "commodity":
                    if ($this->commodity_type == "endline") {
                        $h = new hierarchy();
                        $h->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                        $h->productline_suffix = $item["attributes"]["producline_suffix"];
                        $h->description = $item["attributes"]["formatted_description"];
                        $h->type = "commodity";
                        $h->type_index = 3;
                        $h->url = "/commodities/view.html?goods_nomenclature_item_id=" . $h->goods_nomenclature_item_id . "&productline_suffix=" . $h->productline_suffix;
                        array_push($this->hierarchies, $h);
                    }
                    break;
            }
        }

        usort($this->hierarchies, "cmp");
        $index = 1;
        foreach ($this->hierarchies as $h) {
            $this->hierarchy_string .= "<div class='w100'><div class='c1'>" . $index . ".</div>";
            switch ($h->type) {
                case "section":
                    $this->hierarchy_string .= "<div class='c2'><a href='" . $h->url . "'>Section " . $h->goods_nomenclature_item_id . "</a></div><div class='c3'>" . $h->description . "</div>";
                    break;
                case "chapter":
                    $this->hierarchy_string .= "<div class='c2'><a href='" . $h->url . "'>Chapter " . substr($h->goods_nomenclature_item_id, 0, 2) . "</a></div><div class='c3'>" . $h->description . "</div>";
                    break;
                case "heading":
                    $this->hierarchy_string .= "<div class='c2'><a href='" . $h->url . "'>Heading " . substr($h->goods_nomenclature_item_id, 0, 4) . "</a></div><div class='c3'>" . $h->description . "</div>";
                    break;
                case "commodity":
                    $this->hierarchy_string .= "<div class='c2'><a href='" . $h->url . "'>" . $h->formatted_commodity() . "</a></div><div class='c3'>" . $h->description . "</div>";
                    break;
            }
            $this->hierarchy_string .= "</div><div class='clearer'><!-- &nbsp; //--></div>";
            $index += 1;
        }
        return (true);
    }

    private function terminate_curl()
    {
        curl_close($this->curl);
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
        and goods_nomenclature_sid = $1
        order by ss.id;";

        $sql = "select ssca.id as unique_id, ss.id, ss.step_description, ss.step_howto_description,
        ss.step_url
        from signposting_step_commodity_assignment ssca, signposting_steps ss
        where ss.id = ssca.signposting_step_id 
        and goods_nomenclature_sid = $1
        order by ss.id;";

        pg_prepare($conn, "get_signposting_steps", $sql);
        $result = pg_execute($conn, "get_signposting_steps", array($this->goods_nomenclature_sid));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            while ($row = pg_fetch_array($result)) {
                $content = new content();
                $content->unique_id = $row['unique_id'];
                $content->id = $row['id'];
                $content->step_description = $row['step_description'];
                $content->step_howto_description = $row['step_howto_description'];
                $content->step_url = $row['step_url'];
                /*
                $content->header_id = $row['header_id'];
                $content->subheader_id = $row['subheader_id'];
                $content->header_description = $row['header_description'];
                $content->subheader_description = $row['subheader_description'];
                */
                array_push($this->content, $content);
            }
        }
    }
}
