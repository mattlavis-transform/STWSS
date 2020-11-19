<?php
require("../includes/db.php");
$app = new application();
$document_code_id = get_querystring("document_code_id");
$c = new confirmation();
$c->panel_title = "Document code " . $document_code_id . " has been successfully updated";
$c->panel_body = "";
$c->step1 = "<a class='govuk-link' href='/document_codes/view.html?id=" . $document_code_id . "'>View this document code</a>";
$c->step2 = "<a class='govuk-link' href='/document_codes/'>View all document codes</a>";
$c->step3 = "";
$c->step4 = "";
$c->encrypt_data();
$url = "/includes/confirm.html?data=" . $c->data_encrypted;
header("Location: " . $url);
?>