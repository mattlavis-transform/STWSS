<?php
class heading
{
    // Class properties and methods go here
    public $id                          = "";
    public $section_id                  = "";
    public $chapter_id                  = "";
    public $title                       = "";
    public $description                 = "";
    public $notes                       = "";
    public $json                        = "";
    public $goods_nomenclature_item_id  = "";
    public $goods_nomenclature_sid      = null;

    public function persist()
    {
        global $conn;
        $this->get_blob();
        $sql = "INSERT INTO headings (
            heading_id, goods_nomenclature_item_id, goods_nomenclature_sid, description, blob
            ) VALUES ($1, $2, $3, $4, $5)";

        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $this->id, $this->goods_nomenclature_item_id, $this->goods_nomenclature_sid, $this->description, $this->blob
        ));
    }

    public function get_blob()
    {
        $url = "https://www.trade-tariff.service.gov.uk/api/v2/headings/" . $this->id;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($this->curl);
        $this->blob = $output; // json_decode($output, true);
        curl_close($this->curl);
    }
}
