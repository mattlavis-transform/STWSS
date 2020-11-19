<?php
require("../includes/db.php");
$app = new application();
$measure_type_id = get_querystring("measure_type_id");
$c = new confirmation();
$c->panel_title = "Measure type " . $measure_type_id . " has been successfully updated";
$c->panel_body = "";
$c->step1 = "<a class='govuk-link' href='/measure_types/view.html?id=" . $measure_type_id . "'>View this measure type</a>";
$c->step2 = "<a class='govuk-link' href='/measure_types/'>View all measure types</a>";
$c->step3 = "";
$c->step4 = "";
$c->encrypt_data();
$url = "/includes/confirm.html?data=" . $c->data_encrypted;
header("Location: " . $url);
?>