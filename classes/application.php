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
    public $measure_types = array();
    public $document_codes = array();
    public $section_content = array();
    public $chapter_content = array();
    public $measure_type_content = array();
    public $document_code_content = array();
    public $trade_type_content = array();
    public $headers = array();
    public $subheaders = array();
    public $content = array();
    public $content_linkage = array();
    public $trade_types = array();
    public $content_linking_methods = array();
    public $yes_no = array();
    public $error_array = array();
    public $found_content = array();
    public $err = 0;

    function __construct()
    {
        $this->application_name = "Smart Signposting Data Management";
        $this->url = $_SERVER['PHP_SELF'];
        array_push($this->yes_no, new data_item("yes", "Yes"));
        array_push($this->yes_no, new data_item("no", "No"));

        $this->error_messages = array_map('str_getcsv', file($_SERVER['DOCUMENT_ROOT'] . '/csv/errors.csv'));
        if (session_id() == "") {
            session_start();
        }
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
        from certificates c order by 1";

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
        from signposting_steps ss, signposting_step_document_code_assignment ssdca 
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
        from sections s 
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

    public function get_chapters()
    {
        global $conn;
        $sql = "select id, description as title from chapters order by id;";
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
        from signposting_step_section_assignment sssa, signposting_steps ss 
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
        from signposting_step_chapter_assignment ssca, signposting_steps ss
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
        from signposting_step_trade_type_assignment sstta , signposting_steps ss
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
        $sql = "select measure_type_id, description from measure_types order by 1;";
        $result = pg_query($conn, $sql);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new section;
                $obj->id = $row['measure_type_id'];
                $obj->title = $row['description'];
                $obj->description = $row['measure_type_id'] . " - " . $row['description'];

                array_push($this->measure_types, $obj);
            }
        }
        $this->get_measure_type_content();
    }



    public function get_measure_type_content()
    {
        global $conn;
        $sql = "select ssmta.id as sid, ssmta.measure_type_id, ss.id, ss.step_description 
        from signposting_step_measure_type_assignment ssmta, signposting_steps ss
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
        $sql = "select code, description from certificates order by 1;";
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
        from signposting_step_document_code_assignment ssdca, signposting_steps ss
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
        $sql = "select id, header_description as description from signposting_step_headers order by 1";
        $result = pg_query($conn, $sql);
        $this->headers = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new header;
                $obj->id = $row['id'];
                $obj->description = $row['description'];
                array_push($this->headers, $obj);
            }
        }
    }

    public function get_subheaders()
    {
        global $conn;
        $sql = "select id, subheader_description as description, header_id from signposting_step_subheaders order by 1";
        $result = pg_query($conn, $sql);
        $this->subheaders = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new subheader;
                $obj->id = $row['id'];
                $obj->description = $row['description'];
                $obj->header_id = $row['header_id'];
                array_push($this->subheaders, $obj);
            }
        }
    }

    public function get_content()
    {
        global $conn;
        $sql = "select ss.id, step_description, step_howto_description, step_url, header_id, subheader_id, 
        ssh.header_description, sss.subheader_description 
        from signposting_steps ss, signposting_step_headers ssh, signposting_step_subheaders sss 
        where ss.header_id = ssh.id
        and ss.subheader_id = sss.id
        order by id";
        $result = pg_query($conn, $sql);
        $this->content = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content;
                $obj->id = $row['id'];
                $obj->step_description = $row['step_description'];
                $obj->step_howto_description = $row['step_howto_description'];
                $obj->step_url = $row['step_url'];
                $obj->header_id = $row['header_id'];
                $obj->subheader_id = $row['subheader_id'];
                $obj->header_description = $row['header_description'];
                $obj->subheader_description = $row['subheader_description'];
                array_push($this->content, $obj);
            }
        }
    }

    public function get_content_linkage()
    {
        global $conn;
        $sql = "with cte as (
            select sssa.id, signposting_step_id, 'Section ' || s.numeral as entity_id, 1 as priority, s.title as description
            from signposting_step_section_assignment sssa, sections s
            where sssa.section_id = s.id 
            union
            select ssca.id, signposting_step_id, 'Chapter ' || c.id as entity_id, 2 as priority, c.description
            from signposting_step_chapter_assignment ssca, chapters c
            where ssca.chapter_id = cast(c.id as int)
            union
            select ssmta.id, signposting_step_id, 'Measure type ' || mt.measure_type_id as entity_id, 4 as priority, mt.description
            from signposting_step_measure_type_assignment ssmta, measure_types mt
            where ssmta.measure_type_id = mt.measure_type_id 
            union
            select ssdca.id, signposting_step_id, 'Document code ' || c.code as entity_id, 5 as priority, c.description
            from signposting_step_document_code_assignment ssdca, certificates c
            where ssdca.document_code = c.code
            union
            select sstta.id, signposting_step_id, 'Trade type ' || sstta.trade_type as entity_id, 6 as priority, 
            case when sstta.trade_type = 'IMPORT' then 'For all import trade' else 'For all export trade' end as description
            from signposting_step_trade_type_assignment sstta
        )
        select * from cte order by priority, id";
        $result = pg_query($conn, $sql);
        $this->content_linkage = array();
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new content_linkage;
                $obj->id = $row['id'];
                $obj->signposting_step_id = $row['signposting_step_id'];
                $obj->entity_id = $row['entity_id'];
                $obj->priority = $row['priority'];
                $obj->description = $row['description'];
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
        $this->content_linking_method = get_request("content_linking_method");
        $this->link_type = get_request("link_type");
        $this->sid = get_request("sid");
        $this->id = get_request("id");
        if ($this->content_linking_method == "new") {
            $url = "/content/edit.html?link_type=" . $this->link_type . "&sid=" . $this->sid . "&identifier=" . $this->id;
        } else {
            $url = "/content/find.html?link_type=" . $this->link_type . "&sid=" . $this->sid . "&id=" . $this->id;
        }
        header("Location: " . $url);
    }

    public function show_content_linkage_message()
    {
        $this->link_type = get_request("link_type");
        $this->sid = get_request("sid");
        $this->identifier = get_request("identifier");

        //application::debug();
        if ($this->link_type != "") {
            new inset("This content will be linked to " . str_replace("_", " ", $this->link_type) . " " . $this->identifier);
        }
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
        $sql = "select * from signposting_steps ss
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
                $c->header_id = $row['header_id'];
                $c->subheader_id = $row['subheader_id'];
                array_push($this->found_content, $c);
            }
        }
    }

    function get_linkage_url($content_id)
    {
        $this->linkage_url = "/includes/routes.php?action=create_content_linkage&id=" . $content_id . "&link_type=" . $this->link_type; //&section=$app->sid";

        switch ($this->link_type) {
            case "section":
                $this->linkage_url .= "&section=" . $this->sid;
                break;
            case "chapter":
                $this->linkage_url .= "&chapter=" . $this->sid;
                break;
            case "commodity":
                $this->linkage_url .= "&commodity=" . $this->sid;
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
}
