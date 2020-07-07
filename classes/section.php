<?php
class section
{
    // Class properties and methods go here
    public $id              = null;
    public $numeral         = "";
    public $title           = "";
    public $description     = "";
    public $position        = "";
    public $chapter_from    = "";
    public $chapter_to      = "";
    public $content         = array();

    public $edit_page_title = "";

    function __construct()
    {
        //$content = new content();
    }

    public function populate()
    {
        global $conn, $app;

        $this->id = get_querystring("id");
        if ($this->id == "") {
            $url = "/sections/";
            header("Location: " . $url);
        }

        $sql = "select numeral, title, chapter_from, chapter_to
        from sections where id = $1;";
        pg_prepare($conn, "get_section", $sql);
        $result = pg_execute($conn, "get_section", array($this->id));
        $row_count = pg_num_rows($result);
        $content = new content();
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $this->numeral = $row['numeral'];
            $this->title = $row['title'];
            $this->chapter_from = $row['chapter_from'];
            $this->chapter_to = $row['chapter_to'];
        }

        $this->get_signposting_steps();
    }

    public function get_signposting_steps()
    {
        global $conn;
        $sql = "select sssa.id as unique_id, ss.id, ss.step_description, ss.step_howto_description,
        ss.step_url, ss.header_id, ss.subheader_id, ssh.header_description, sss.subheader_description 
        from signposting_step_section_assignment sssa, signposting_steps ss, signposting_step_headers ssh, signposting_step_subheaders sss 
        where ss.id = sssa.signposting_step_id 
        and ss.header_id = ssh.id 
        and ss.subheader_id = sss.id 
        and section_id = $1
        order by ss.id";
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
                $content->header_id = $row['header_id'];
                $content->subheader_id = $row['subheader_id'];
                $content->header_description = $row['header_description'];
                $content->subheader_description = $row['subheader_description'];
                array_push($this->content, $content);
            }
        }
        return ($content);
    }
}
