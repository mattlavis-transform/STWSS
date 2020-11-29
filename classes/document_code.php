<?php
class document_code
{
    // Class properties and methods go here
    public $id              = "";
    public $title           = "";
    public $description     = "";
    public $content         = array();
    public $overlay         = "";
    public $subtext         = "";
    public $overlay_welsh   = "";
    public $subtext_welsh   = "";

    public function populate()
    {
        global $conn, $app;

        $this->id = get_querystring("id");
        if ($this->id == "") {
            $url = "/document_codes/";
            header("Location: " . $url);
        }

        $sql = "select description, overlay, subtext, overlay_welsh, subtext_welsh
        from chieg.certificates where code = $1;";
        pg_prepare($conn, "get_document_code", $sql);
        $result = pg_execute($conn, "get_document_code", array($this->id));
        $row_count = pg_num_rows($result);
        $content = new content();
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $this->description = $row['description'];
            $this->overlay = $row['overlay'];
            $this->subtext = $row['subtext'];
            $this->overlay_welsh = $row['overlay_welsh'];
            $this->subtext_welsh = $row['subtext_welsh'];
        }

        $this->get_signposting_steps();
    }

    public function get_signposting_steps()
    {
        global $conn;
        $sql = "select ssdca.id as unique_id, ssdca.document_code, ss.id, ss.step_description, ss.step_description, ss.step_howto_description,
        ss.step_url
        from chieg.signposting_step_document_code_assignment ssdca, chieg.signposting_steps ss
        where ss.id = ssdca.signposting_step_id 
        and ssdca.document_code = $1
        order by ss.id;";
        pg_prepare($conn, "get_signposting_steps", $sql);
        $result = pg_execute($conn, "get_signposting_steps", array($this->id));
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
