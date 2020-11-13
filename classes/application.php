<?php
class application
{
    // Class properties and methods go here

    public $page_title;
    public $url;
    public $crumb_string = "";
    public $linkage_url = "";
    public $free_text = "";
    public $delimiter = "||";
    public $link_type = "";
    public $certificates = array();
    public $certificates_to_document_codes = array();
    public $sections = array();
    public $chapters = array();
    public $headings = array();
    public $measure_types = array();
    public $document_codes = array();
    public $section_content = array();
    public $chapter_content = array();
    public $measure_type_content = array();
    public $document_code_content = array();
    public $trade_type_content = array();
    public $headers = array();
    public $subheaders = array();

    public $headers_import = array();
    public $subheaders_import = array();

    public $headers_export = array();
    public $subheaders_export = array();

    public $content = array();
    public $content_linkage = array();
    public $trade_type_linkage = array();
    public $trade_types = array();
    public $content_linking_methods = array();
    public $yes_no = array();
    public $error_array = array();
    public $found_content = array();
    public $err = 0;
    public $logged_in = false;
    public $user_id = null;
    public $user_name = null;
    public $first_name = null;
    public $last_name = null;
    public $chapter = null;
    public $section = null;

    public $page_size = 10;
    public $page = 1;
    public $record_count = 0;
    public $content_records = array();
    public $measure_type_ranges = array();

    public $import_measure_permutations = array();
    public $export_measure_permutations = array();

    function __construct()
    {

        /*
        $pwd = "";
        $test = SA_Encryption::encrypt_to_url_param($pwd);
        h1 ($test);
        */

        $this->application_name = "Smart Signposting Data Management";
        $this->url = $_SERVER['PHP_SELF'];
        array_push($this->yes_no, new data_item("yes", "Yes"));
        array_push($this->yes_no, new data_item("no", "No"));

        $this->error_messages = array_map('str_getcsv', file($_SERVER['DOCUMENT_ROOT'] . '/csv/errors.csv'));
        if (session_id() == "") {
            session_start();
        }
        $this->check_permissions();
        $this->setup_measure_type_ranges();
    }

    public function setup_measure_type_ranges()
    {
        $this->measure_type_ranges = array();
        array_push($this->measure_type_ranges, new measure_type_range("400", "479"));
        array_push($this->measure_type_ranges, new measure_type_range("700", "999"));
        array_push($this->measure_type_ranges, new measure_type_range("AAA", "CZZ"));
        array_push($this->measure_type_ranges, new measure_type_range("ECM", "ECM"));
        array_push($this->measure_type_ranges, new measure_type_range("EHC", "EHC"));
        array_push($this->measure_type_ranges, new measure_type_range("EQC", "EWP"));
        array_push($this->measure_type_ranges, new measure_type_range("HAA", "IZZ"));
        array_push($this->measure_type_ranges, new measure_type_range("PHC", "UZZ"));

        //pre ($this->measure_type_ranges);
    }

    public function check_permissions()
    {
        $page = $_SERVER["SCRIPT_NAME"];
        if (isset($_SESSION["user_id"])) {
            $this->user_id = $_SESSION["user_id"];
            $this->first_name = $_SESSION["first_name"];
        } else {
            if (($page != "/login.php") && ($page != "/includes/routes.php")) {
                $user_id = get_session_variable("user_id");
                h1("User ID = " . $user_id);
                if ($user_id == "") {
                    $this->logged_in = false;
                    $url = "/login.html";
                    header("Location: " . $url);
                }
            }
        }
    }

    static public function array_to_list($arr)
    {
        $s = "";
        $count = count($arr);
        $index = 0;
        foreach ($arr as $item) {
            $index += 1;
            $s .= $item;
            if ($index != $count) {
                $s .= ", ";
            }
        }
        if ($s == "") {
            $s = -1;
        }
        return ($s);
    }

    static public function debug()
    {
        ini_set('display_errors', false);
        try {
            echo ("Called from function <b>" . debug_backtrace()[1]['function'] . "</b>");
        } catch (exception $e) {
        }
        pre($_REQUEST);
        ini_set('display_errors', true);
        die();
    }

    public function get_certificates()
    {
        global $conn;
        $sql = "select c.code, c.certificate_type_code, c.certificate_code, c.description
        from chieg.certificates c order by 1";

        $result = pg_query($conn, $sql);
        $temp = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $certificate = new certificate;
                $certificate->certificate_type_code = $row['certificate_type_code'];
                $certificate->certificate_code = $row['certificate_code'];
                $certificate->code = $row['code'];
                $certificate->description = $row['description'];
                $certificate->get_content();
                array_push($temp, $certificate);
            }
            $this->certificates = $temp;
        }
    }

    public function get_certificate_content_assignment()
    {
        global $conn;
        $sql = "select ss.id, ss.step_description, ssdca.document_code 
        from chieg.signposting_steps ss, chieg.signposting_step_document_code_assignment ssdca 
        where ss.id = ssdca.id
        order by 3, 1;";
        $result = pg_query($conn, $sql);
        $temp = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new reusable;
                $obj->id = $row['id'];
                $obj->document_code = $row['document_code'];
                $obj->step_description = $row['step_description'];

                array_push($this->certificates_to_document_codes, $obj);
            }
        }
    }

    public function get_sections()
    {
        global $conn;
        $sql = "select id, numeral, title, position, chapter_from, chapter_to 
        from chieg.sections s 
        order by position;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new section;
                $obj->id = $row['id'];
                $obj->numeral = $row['numeral'];
                $obj->title = $row['title'];
                $obj->description = $obj->numeral . " - " . $obj->title;
                $obj->position = $row['position'];
                $obj->chapter_from = $row['chapter_from'];
                $obj->chapter_to = $row['chapter_to'];

                array_push($this->sections, $obj);
            }
        }
        $this->get_section_content();
    }

    public function get_sections_from_API()
    {
        $url = "https://www.trade-tariff.service.gov.uk/api/v2/sections/";
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($this->curl);
        $this->json = json_decode($output, true);
        curl_close($this->curl);

        if (isset($this->json["data"])) {
            $data = $this->json["data"];
            $this->sections = array();
            foreach ($data as $item) {
                $obj = new section;
                $obj->id = $item["attributes"]["id"];
                $obj->numeral = $item["attributes"]["numeral"];
                $obj->description = $item["attributes"]["title"];
                $obj->position = $item["attributes"]["position"];
                $obj->chapter_from = $item["attributes"]["chapter_from"];
                $obj->chapter_to = $item["attributes"]["chapter_to"];

                //pre ($obj);
                array_push($this->sections, $obj);
            }
        }
    }

    public function get_chapters_from_API()
    {
        $section_id = get_querystring("id");
        $url = "https://www.trade-tariff.service.gov.uk/api/v2/sections/" . $section_id;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($this->curl);
        $this->json = json_decode($output, true);
        curl_close($this->curl);

        // Get section details
        if (isset($this->json["data"])) {
            $data = $this->json["data"];
            $this->section = new section();
            $this->section->id = $data["id"];
            $this->section->numeral = $data["attributes"]["numeral"];
            $this->section->description = $data["attributes"]["title"];
        }

        // Get subsidiary chapter details
        if (isset($this->json["included"])) {
            $included = $this->json["included"];
            //prend($included);
            $this->chapters = array();
            foreach ($included as $item) {
                $type =  $item["type"];
                if ($type == "chapter") {
                    $chapter = new chapter;

                    $chapter->goods_nomenclature_sid = $item["attributes"]["goods_nomenclature_sid"];
                    $chapter->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $chapter->description = $item["attributes"]["formatted_description"];
                    $chapter->id = substr($chapter->goods_nomenclature_item_id, 0, 2);

                    array_push($this->chapters, $chapter);
                }
            }
        }
        //die();
    }

    public function get_headings_from_API()
    {
        $section_id = get_querystring("id");
        $url = "https://www.trade-tariff.service.gov.uk/api/v2/chapters/" . $section_id;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($this->curl);
        $this->json = json_decode($output, true);
        curl_close($this->curl);

        //prend ($this->json);

        // Get chapter details
        if (isset($this->json["data"])) {
            $data = $this->json["data"];
            //pre($data);
            $this->chapter = new chapter();
            $this->chapter->id = $data["id"];
            $this->chapter->goods_nomenclature_sid = $data["attributes"]["goods_nomenclature_sid"];
            $this->chapter->goods_nomenclature_item_id = $data["attributes"]["goods_nomenclature_item_id"];
            $this->chapter->description = $data["attributes"]["formatted_description"];
            $this->chapter->id = substr($this->chapter->goods_nomenclature_item_id, 0, 2);

            // Get the section
            if (isset($data["relationships"])) {
                $relationships = $data["relationships"];
                if (isset($relationships["section"])) {
                    $this->chapter->section_id = $relationships["section"]["data"]["id"];
                }
                //pre($relationships);
            }
        }


        //die();

        // Get subsidiary heading details
        if (isset($this->json["included"])) {
            $included = $this->json["included"];
            $this->headings = array();
            foreach ($included as $item) {
                $type =  $item["type"];
                if ($type == "heading") {
                    $heading = new heading();
                    $heading->goods_nomenclature_sid = $item["attributes"]["goods_nomenclature_sid"];
                    $heading->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $heading->description = $item["attributes"]["formatted_description"];
                    $heading->id = substr($heading->goods_nomenclature_item_id, 0, 4);
                    //$heading->persist();
                    array_push($this->headings, $heading);
                }
            }
        }
    }

    public function get_commodities_from_API()
    {
        $heading_id = get_querystring("id");
        $url = "https://www.trade-tariff.service.gov.uk/api/v2/headings/" . $heading_id;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($this->curl);
        $this->json = json_decode($output, true);
        curl_close($this->curl);

        // Get chapter details
        if (isset($this->json["data"])) {
            $data = $this->json["data"];
            //prend ($data);

            $this->heading = new heading();
            $this->heading->goods_nomenclature_sid = $data["id"];
            $this->heading->goods_nomenclature_item_id = $data["attributes"]["goods_nomenclature_item_id"];
            $this->heading->description = $data["attributes"]["formatted_description"];
            $this->heading->id = substr($this->heading->goods_nomenclature_item_id, 0, 4);

            // Get the section
            if (isset($data["relationships"])) {
                $relationships = $data["relationships"];
                if (isset($relationships["section"])) {
                    $this->heading->section_id = $relationships["section"]["data"]["id"];
                }
                //pre($relationships);
            }
        }

        // Get subsidiary heading details
        if (isset($this->json["included"])) {
            $included = $this->json["included"];
            $this->commodities = array();
            foreach ($included as $item) {
                $type =  $item["type"];
                if ($type == "commodity") {
                    $commodity = new commodity();
                    $commodity->goods_nomenclature_sid = $item["attributes"]["goods_nomenclature_sid"];
                    $commodity->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $commodity->productline_suffix = $item["attributes"]["producline_suffix"];
                    $commodity->number_indents = $item["attributes"]["number_indents"];
                    $commodity->description = $item["attributes"]["formatted_description"];
                    $commodity->declarable = $item["attributes"]["leaf"];
                    array_push($this->commodities, $commodity);
                }
            }
        }
    }

    public function get_commodities_from_DB()
    {
        global $conn;
        $heading_id = get_querystring("id");
        //h1($heading_id);
        $sql = "select * from chieg.headings where heading_id = $1"; // . $heading_id;
        $stmt = uniqid();
        pg_prepare($conn,  $stmt, $sql);
        $result = pg_execute($conn,  $stmt, array($heading_id));
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $this->heading = new heading();
            $this->heading->goods_nomenclature_sid = $row["goods_nomenclature_sid"];
            $this->heading->goods_nomenclature_item_id = $row["goods_nomenclature_item_id"];
            $this->heading->description = $row["description"];
            $this->heading->id = substr($this->heading->goods_nomenclature_item_id, 0, 4);
            $this->heading->json = json_decode($row["blob"], true);
        }

        // Get chapter details
        if (isset($this->heading->json["data"])) {
            //h1 ("getting data");
            $data = $this->heading->json["data"];

            // Get the section
            if (isset($data["relationships"])) {
                $relationships = $data["relationships"];
                if (isset($relationships["section"])) {
                    $this->heading->section_id = $relationships["section"]["data"]["id"];
                }
            }
        }

        // Get subsidiary heading details
        if (isset($this->heading->json["included"])) {
            $included = $this->heading->json["included"];
            $this->commodities = array();
            foreach ($included as $item) {
                $type =  $item["type"];
                if ($type == "commodity") {
                    $commodity = new commodity();
                    $commodity->goods_nomenclature_sid = $item["attributes"]["goods_nomenclature_sid"];
                    $commodity->goods_nomenclature_item_id = $item["attributes"]["goods_nomenclature_item_id"];
                    $commodity->productline_suffix = $item["attributes"]["producline_suffix"];
                    $commodity->number_indents = $item["attributes"]["number_indents"];
                    $commodity->description = $item["attributes"]["formatted_description"];
                    $commodity->declarable = $item["attributes"]["leaf"];
                    array_push($this->commodities, $commodity);
                }
            }
        }
    }

    public function get_chapters()
    {
        global $conn;
        $sql = "select chapter as id, description as title from chieg.chapters order by chapter;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new chapter;
                $obj->id = $row['id'];
                $obj->title = $row['title'];
                $obj->description = $obj->id . " - " . $obj->title;

                array_push($this->chapters, $obj);
            }
        }
        $this->get_chapter_content();
    }

    public function get_section_content()
    {
        global $conn;
        $sql = "select sssa.id as sid, sssa.section_id, ss.id, ss.step_description 
        from chieg.signposting_step_section_assignment sssa, chieg.signposting_steps ss 
        where ss.id = sssa.signposting_step_id order by sssa.section_id, ss.id;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content;

                $obj->sid = $row['sid'];
                $obj->id = $row['id'];
                $obj->section_id = $row['section_id'];
                $obj->step_description = $row['step_description'];

                array_push($this->section_content, $obj);
            }
        }
        // Assign the content to the sections
        foreach ($this->sections as $section) {
            foreach ($this->section_content as $content) {
                if ($content->section_id == $section->id) {
                    array_push($section->content, $content);
                }
            }
        }
    }

    public function get_chapter_content()
    {
        global $conn;
        $sql = "select ssca.id as sid, chapter_id, ss.id, ss.step_description 
        from chieg.signposting_step_chapter_assignment ssca, chieg.signposting_steps ss
        where ss.id = ssca.signposting_step_id order by ssca.chapter_id, ss.id;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content;

                $obj->sid = $row['sid'];
                $obj->id = $row['id'];
                $obj->chapter_id = $row['chapter_id'];
                $obj->step_description = $row['step_description'];

                array_push($this->chapter_content, $obj);
            }
        }
        // Assign the content to the chapters
        foreach ($this->chapters as $chapter) {
            foreach ($this->chapter_content as $content) {
                if ($content->chapter_id == $chapter->id) {
                    array_push($chapter->content, $content);
                }
            }
        }
    }


    public function get_trade_type_content()
    {
        global $conn;
        $sql = "select sstta.id as sid, sstta.trade_type, ss.id, ss.step_description 
        from chieg.signposting_step_trade_type_assignment sstta, chieg.signposting_steps ss
        where ss.id = sstta.signposting_step_id order by sstta.trade_type ;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content;

                $obj->sid = $row['sid'];
                $obj->id = $row['id'];
                $obj->trade_type = $row['trade_type'];
                $obj->step_description = $row['step_description'];

                array_push($this->trade_type_content, $obj);
            }
        }
        // Assign the content to the chapters
        foreach ($this->trade_types as $trade_type) {
            $trade_type->content = array();
            foreach ($this->trade_type_content as $content) {
                if ($content->trade_type == $trade_type->id) {
                    array_push($trade_type->content, $content);
                }
            }
        }
    }


    public function get_measure_types()
    {
        global $conn;
        $sql = "select measure_type_id, description from chieg.measure_types order by 1;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new measure_type;
                $obj->id = $row['measure_type_id'];
                $obj->title = $row['description'];
                $obj->description = $row['measure_type_id'] . " - " . $row['description'];

                array_push($this->measure_types, $obj);
            }
        }
        $this->get_measure_type_content();
    }

    public function get_measure_types2()
    {
        global $conn;
        $sql = "select measure_type_id, description from chieg.measure_types where measure_type_series_id in ('A', 'B') order by 1;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new measure_type;
                $obj->id = $row['measure_type_id'];
                $obj->title = $row['description'];
                $obj->description = $row['measure_type_id'] . " - " . $row['description'];

                array_push($this->measure_types, $obj);
            }
        }
    }



    public function get_measure_type_content()
    {
        global $conn;
        $sql = "select ssmta.id as sid, ssmta.measure_type_id, ss.id, ss.step_description 
        from chieg.signposting_step_measure_type_assignment ssmta, chieg.signposting_steps ss
        where ss.id = ssmta.signposting_step_id order by ssmta.measure_type_id;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content;

                $obj->sid = $row['sid'];
                $obj->id = $row['id'];
                $obj->measure_type_id = $row['measure_type_id'];
                $obj->step_description = $row['step_description'];

                array_push($this->measure_type_content, $obj);
            }
        }
        // Assign the content to the measure types
        foreach ($this->measure_types as $measure_type) {
            $measure_type->content = array();
            foreach ($this->measure_type_content as $content) {
                if ($content->measure_type_id == $measure_type->id) {
                    array_push($measure_type->content, $content);
                }
            }
        }
    }


    public function get_document_codes()
    {
        global $conn;
        $sql = "select code, description from chieg.certificates order by 1;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new section;
                $obj->id = $row['code'];
                $obj->title = $row['description'];
                $obj->description = $row['code'] . " - " . $row['description'];

                array_push($this->document_codes, $obj);
            }
        }
        $this->get_document_code_content();
    }


    public function get_document_code_content()
    {
        global $conn;
        $sql = "select ssdca.id as sid, ssdca.document_code, ss.id, ss.step_description 
        from chieg.signposting_step_document_code_assignment ssdca, chieg.signposting_steps ss
        where ss.id = ssdca.signposting_step_id order by ssdca.document_code;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content;

                $obj->sid = $row['sid'];
                $obj->id = $row['id'];
                $obj->document_code = $row['document_code'];
                $obj->step_description = $row['step_description'];

                array_push($this->document_code_content, $obj);
            }
        }
        // Assign the content to the measure types
        foreach ($this->document_codes as $document_code) {
            $document_code->content = array();
            foreach ($this->document_code_content as $content) {
                if ($content->document_code == $document_code->id) {
                    array_push($document_code->content, $content);
                }
            }
        }
    }

    public function get_headers()
    {
        global $conn;
        $sql = "select id, (order_index || '. ' || header_description) as description, trade_type
        from chieg.signposting_step_headers order by order_index";
        $result = pg_query($conn, $sql);
        $this->headers = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new header;
                $obj->id = $row['id'];
                $obj->description = $row['description'];
                $obj->trade_type = $row['trade_type'];
                array_push($this->headers, $obj);
                if ($obj->trade_type == "IMPORT") {
                    array_push($this->headers_import, $obj);
                } elseif ($obj->trade_type == "EXPORT") {
                    array_push($this->headers_export, $obj);
                }
            }
        }
    }

    public function get_subheaders()
    {
        global $conn;
        $sql = "select sss.id, sss.subheader_description as description, sss.header_id, ssh.trade_type 
        from chieg.signposting_step_subheaders sss, chieg.signposting_step_headers ssh 
        where sss.header_id = ssh.id 
        order by ssh.order_index, sss.order_index";
        $result = pg_query($conn, $sql);
        $this->subheaders = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new subheader;
                $obj->id = $row['id'];
                $obj->description = $row['description'];
                $obj->header_id = $row['header_id'];
                $obj->trade_type = $row['trade_type'];
                array_push($this->subheaders, $obj);
                if ($obj->trade_type == "IMPORT") {
                    array_push($this->subheaders_import, $obj);
                } elseif ($obj->trade_type == "EXPORT") {
                    array_push($this->subheaders_export, $obj);
                }
            }
        }
    }

    public function get_content()
    {
        global $conn;

        $sql = "with cte as (
        select ss.id, step_description, step_howto_description, step_url
        from chieg.signposting_steps ss order by id
        )
        select *, count(*) OVER() AS record_count from cte
        limit $this->page_size offset $this->page_size * ($this->page - 1);";

        $result = pg_query($conn, $sql);
        $this->content = array();
        $this->record_count = 0;
        $this->content_records = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $this->record_count = $row["record_count"];
                $obj = new content;
                $obj->id = $row['id'];
                $obj->step_description = $row['step_description'];
                $obj->step_howto_description = $row['step_howto_description'];
                $obj->step_url = $row['step_url'];

                array_push($this->content, $obj);
                array_push($this->content_records, $obj->id);
            }
        }
    }

    public function get_content_linkage()
    {
        global $conn;

        $records = application::array_to_list($this->content_records);

        $sql = "with cte as (
        select sssa.id, signposting_step_id, 'Section ' || s.numeral as entity_id, 1 as priority, s.title as description, 'section' as link_type
        from chieg.signposting_step_section_assignment sssa, chieg.sections s
        where sssa.section_id = s.id 
        union
        select ssca.id, signposting_step_id, 'Chapter ' || c.chapter as entity_id, 2 as priority, c.description, 'chapter' as link_type
        from chieg.signposting_step_chapter_assignment ssca, chieg.chapters c
        where ssca.chapter_id = cast(c.chapter as int)
        union
        select ssmta.id, signposting_step_id, 'Measure type ' || mt.measure_type_id as entity_id, 4 as priority, mt.description, 'measure_type' as link_type
        from chieg.signposting_step_measure_type_assignment ssmta, chieg.measure_types mt
        where ssmta.measure_type_id = mt.measure_type_id 
        union
        select ssdca.id, signposting_step_id, 'Document code ' || c.code as entity_id, 5 as priority, c.description, 'document_code' as link_type
        from chieg.signposting_step_document_code_assignment ssdca, chieg.certificates c
        where ssdca.document_code = c.code
        union
        select ssca.id, ssca.signposting_step_id, 'Commodity ' || gn.goods_nomenclature_item_id as entity_id, 6 as priority, gn.description, 'commodity' as link_type
        from chieg.signposting_step_commodity_assignment ssca, chieg.goods_nomenclatures gn 
        where gn.goods_nomenclature_sid = ssca.goods_nomenclature_sid 
        )
        select * from cte where signposting_step_id in (" . $records . ") order by priority, id";
        // pre($sql);
        $result = pg_query($conn, $sql);
        $this->content_linkage = array();
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content_linkage;
                $obj->id = $row['id'];
                $obj->signposting_step_id   = $row['signposting_step_id'];
                $obj->entity_id             = $row['entity_id'];
                $obj->priority              = $row['priority'];
                $obj->description           = $row['description'];
                $obj->link_type             = $row['link_type'];

                array_push($this->content_linkage, $obj);
            }
        }

        foreach ($this->content as $c) {
            foreach ($this->content_linkage as $l) {
                if ($c->id == $l->signposting_step_id) {
                    array_push($c->linkage, $l);
                }
            }
        }
    }


    public function get_content_trade_types_headings()
    {
        global $conn;

        $records = application::array_to_list($this->content_records);

        $sql = "with cte as (
        select ssha.id, signposting_step_id, ssha.trade_type, ssha.header_id, ssha.subheader_id,
        ssh.header_description, sss.subheader_description, ssh.order_index as o1, sss.order_index as o2
        from chieg.signposting_step_heading_assignment ssha, chieg.signposting_step_headers ssh, chieg.signposting_step_subheaders sss 
        where ssha.header_id = ssh.id 
        and ssha.subheader_id = sss.id 
        )
        select * from cte where signposting_step_id in (" . $records . ") order by trade_type, o1, o2";
        $result = pg_query($conn, $sql);
        $this->trade_type_linkage = array();
        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            while ($row = pg_fetch_array($result)) {
                $obj = new reusable;
                $obj->id                    = $row['id'];
                $obj->signposting_step_id   = $row['signposting_step_id'];
                $obj->trade_type            = $row['trade_type'];
                $obj->header_id             = $row['header_id'];
                $obj->subheader_id          = $row['subheader_id'];
                $obj->header_description    = $row['header_description'];
                $obj->subheader_description = $row['subheader_description'];

                array_push($this->trade_type_linkage, $obj);
            }
        }

        foreach ($this->content as $c) {
            foreach ($this->trade_type_linkage as $l) {
                if ($c->id == $l->signposting_step_id) {
                    array_push($c->trade_types, $l);
                }
            }
        }
    }

    public function get_trade_types()
    {
        array_push($this->trade_types, new data_item("IMPORT", "Import"));
        array_push($this->trade_types, new data_item("EXPORT", "Export"));
        $this->get_trade_type_content();
    }

    public function get_content_linking_methods()
    {
        $this->link_type = get_request("link_type");
        $this->sid = get_request("sid");
        $this->identifier = get_request("id");
        $this->crumb_string = "Home|/;Content|/content;Content item|";
        switch ($this->link_type) {
            case "section":
                $this->crumb_string = "Home|/;Sections|/sections;Section " . $this->identifier . "|/sections/view.html?id=" . $this->sid . ";Add content|";
                $this->link_type_string = "Section " . $this->identifier;
                break;
            case "chapter":

                $this->link_type_string = "chapter " . $this->identifier;
                break;
            case "commodity":

                $this->link_type_string = "commodity code " . $this->identifier;
                break;
            case "measure_type":

                $this->link_type_string = "measure type " . $this->identifier;
                break;
            case "document_code":

                $this->link_type_string = "document code " . $this->identifier;
                break;
            case "trade_type":

                $this->link_type_string = "trade type " . ucwords(strtolower($this->identifier));
                break;
        }
        array_push($this->content_linking_methods, new data_item("existing", "Find existing content"));
        array_push($this->content_linking_methods, new data_item("new", "Create new content"));
    }



    public function apply_content_linking_method()
    {
        $this->link_type = get_request("link_type");
        $this->sid = get_request("sid");
        $this->id = get_request("id");
    
        //application::debug();
    
        $this->content_linking_method = get_request("content_linking_method");
        if ($this->content_linking_method == "") {
            $errors = array();
            array_push($errors, "content_linking_method");
            $data = serialize($errors);
            $data_encrypted = SA_Encryption::encrypt_to_url_param($data);
            $url = "/content/add.html?link_type=" . $this->link_type . "&id=" . $this->id . "&sid=" . $this->sid . "&err=1&data=" . $data_encrypted;
            header("Location: " . $url);
        } else {
            if ($this->content_linking_method == "new") {
                $url = "/content/edit.html?link_type=" . $this->link_type . "&sid=" . $this->sid . "&identifier=" . $this->id;
            } else {
                $url = "/content/find.html?link_type=" . $this->link_type . "&sid=" . $this->sid . "&id=" . $this->id;
            }
            header("Location: " . $url);
        }
    }

    public function show_content_linkage_message()
    {
        $this->link_type = get_request("link_type");
        $this->sid = get_request("sid");
        $this->identifier = get_request("identifier");

        if ($this->link_type != "") {
            new inset("This content will be linked to " . str_replace("_", " ", $this->link_type) . " " . $this->identifier);
        }
    }

    static public function truncate($text, $chars = 120)
    {
        if (strlen($text) > $chars) {
            $text = $text . ' ';
            $text = substr($text, 0, $chars);
            //$text = substr($text, 0, strrpos($text ,' '));
            $text = $text . '...';
        }
        //h1 ($text);
        return ($text);
    }

    static public function get_plural($s)
    {
        $s = str_replace("_", " ", $s);
        if (substr($s, -1) == "y") {
            return (substr($s, 0, -1) . "ies");
        } else {
            return ($s . "s");
        }
    }

    public function check_for_errors()
    {
        $this->err = get_querystring("err");
        if ($this->err == "1") {
            $this->data_encrypted = get_querystring("data");
            $this->data = SA_Encryption::decrypt_from_url_param($this->data_encrypted);
            $this->error_array = explode($this->delimiter, $this->data);
            $this->error_array = unserialize($this->error_array[0]);
        }
    }

    public function get_error_message($field)
    {
        $msg = "";
        $found = false;
        foreach ($this->error_messages as $error_message) {
            if ($field == $error_message[0]) {
                $msg = $error_message[1];
                $found = true;
                break;
            }
        }

        if ($found == false) {
            $msg = "Error message not found";
        }
        return ($msg);
    }

    function find_content()
    {
        global $conn;
        $this->free_text = get_querystring("free_text");
        $free_text = "%" . $this->free_text . "%";
        $this->link_type = get_querystring("link_type");
        $this->sid = get_querystring("sid");
        $this->id = get_querystring("id");

        $command = uniqid();
        $sql = "select * from chieg.signposting_steps ss
        where ss.step_description ilike $1
        or ss.step_howto_description ilike $1
        or ss.step_url ilike $1
        order by step_description";
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array($free_text));
        $this->found_content = array();
        $row_count = pg_num_rows($result);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $c = new content;
                $c->id = $row['id'];
                $c->step_description = $row['step_description'];
                $c->step_howto_description = $row['step_howto_description'];
                $c->step_url = $row['step_url'];
                /*
                $c->header_id = $row['header_id'];
                $c->subheader_id = $row['subheader_id'];
                */
                array_push($this->found_content, $c);
            }
        }
    }

    function get_linkage_url($content_id)
    {
        $this->linkage_url = "/includes/routes.php?action=create_content_linkage&id=" . $content_id . "&link_type=" . $this->link_type;

        switch ($this->link_type) {
            case "section":
                $this->linkage_url .= "&section=" . $this->sid;
                break;
            case "chapter":
                $this->linkage_url .= "&chapter=" . $this->sid;
                break;
            case "commodity":
                $this->linkage_url .= "&commodity=" . $this->sid . "&commodity_code=" . $this->id;
                break;
            case "measure_type":
                $this->linkage_url .= "&measure_type=" . $this->sid;
                break;
            case "document_code":
                $this->linkage_url .= "&document_code=" . $this->sid;
                break;
            case "trade_type":
                $this->linkage_url .= "&trade_type=" . $this->sid;
                break;
        }
    }

    function logout()
    {
        session_unset();
        $url = "/";
        header("Location: " . $url);
    }

    function login()
    {
        global $conn;
        $user_name = get_querystring("user_name");
        h1($user_name);
        $password = get_querystring("password");
        $password_encrypted = SA_Encryption::encrypt_to_url_param($password);
        $sql = "select * from chieg.users where user_name = $1 and password = $2 limit 1";
        $stmt = uniqid();
        pg_prepare($conn, $stmt, $sql);
        $result = pg_execute($conn, $stmt, array(
            $user_name,
            $password_encrypted
        ));

        $row_count = pg_num_rows($result);
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $user_id = $row["user_id"];
            $user_name = $row["user_name"];
            $first_name = $row["first_name"];
            $last_name = $row["last_name"];
            $this->set_user_id($user_id, $user_name, $first_name, $last_name);
            $this->description = $row['description'];
        } else {
            $url = "/login.html";
            header("Location: " . $url);
        }
    }

    function set_user_id($user_id, $user_name, $first_name, $last_name)
    {
        $this->user_id = $user_id;
        $this->user_name = $user_name;
        $this->first_name = $first_name;
        $this->last_name = $last_name;

        $_SESSION["user_id"] = $user_id;
        $_SESSION["user_name"] = $user_name;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;

        $url = "/";
        header("Location: " . $url);
    }

    function get_page()
    {
        $p = get_querystring("p");
        if ($p != "") {
            $this->page = $p;
        }
        $ps = get_querystring("ps");
        if ($ps != "") {
            $this->page_size = $ps;
        }
        $this->page_size = max($this->page_size, 1);
        $this->page = max($this->page, 1);
    }

    function show_paging_controls()
    {
        $page_count = intdiv($this->record_count - 1, $this->page_size) + 1;
        echo ('<div class="govuk-body">');
        for ($i = 1; $i <= $page_count; $i++) {
            $item = '<span class="page">';
            if ($i != $this->page) {
                $url = "/content/?p=$i&ps=$this->page_size";
                $item .= "<a class='govuk-link' href='" . $url . "'>";
            }
            $item .= $i;
            if ($i != $this->page) {
                $item .= "</a>";
            }
            $item .= '</span>';
            echo ($item);
        }
        echo ('</div>');
    }
}
