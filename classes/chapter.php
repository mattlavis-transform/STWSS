<?php
class chapter
{
    // Class properties and methods go here
    public $id              = "";
    public $title           = "";
    public $description     = "";
    public $notes           = "";
    public $content         = array();

    public function populate()
    {
        global $conn, $app;

        $this->id = get_querystring("id");
        if ($this->id == "") {
            $url = "/chapters/";
            header("Location: " . $url);
        }

        $sql = "select description from chapters where id = $1;";
        pg_prepare($conn, "get_chapter", $sql);
        $result = pg_execute($conn, "get_chapter", array($this->id));
        $row_count = pg_num_rows($result);
        $content = new content();
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $this->description = $row['description'];
        }

        $this->get_signposting_steps();
    }

    public function get_signposting_steps()
    {
        global $conn;
        $sql = "select ssca.id as unique_id, ss.id, ss.step_description, ss.step_howto_description,
        ss.step_url, ss.header_id, ss.subheader_id, ssh.header_description, sss.subheader_description 
        from signposting_step_chapter_assignment ssca, signposting_steps ss, signposting_step_headers ssh, signposting_step_subheaders sss 
        where ss.id = ssca.signposting_step_id 
        and ss.header_id = ssh.id 
        and ss.subheader_id = sss.id 
        and chapter_id = $1
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
