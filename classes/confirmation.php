<?php
class confirmation
{
    public $id = "";
    public $description = "";
    public $delimiter = "|||";
    public $data = "";
    public $data_encrypted = "";
    public $panel_title = "";
    public $panel_body = "";
    public $step1 = "";
    public $step2 = "";
    public $step3 = "";
    public $step4 = "";

    function __construct()
    {
    }

    public function get_data()
    {
        global $app;
        $this->data_encrypted = get_querystring("data");
        $this->data = SA_Encryption::decrypt_from_url_param($this->data_encrypted);
        $this->data_array = explode($app->delimiter, $this->data);
        //prend($this->data_array);
        $this->panel_title = $this->data_array[0];
        $this->panel_body = $this->data_array[1];
        $this->step1 = $this->data_array[2];
        $this->step2 = $this->data_array[3];
        $this->step3 = $this->data_array[4];
        $this->step4 = $this->data_array[5];

    }

    public function encrypt_data()
    {
        global $app;
        $this->data = $this->panel_title . $app->delimiter;
        $this->data .= $this->panel_body . $app->delimiter;
        $this->data .= $this->step1 . $app->delimiter;
        $this->data .= $this->step2 . $app->delimiter;
        $this->data .= $this->step3 . $app->delimiter;
        $this->data .= $this->step4 . $app->delimiter;
        $this->data_encrypted = SA_Encryption::encrypt_to_url_param($this->data);
    }
}
