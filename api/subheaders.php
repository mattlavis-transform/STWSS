<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include '../includes/db.php';
$app = new application;
$header_id = trim(get_querystring("header_id"));

$app->get_subheaders();

if (sizeof($app->subheaders) > 0) {
    $parent_array = array();
    $parent_array["results"] = array();
    if ($header_id != "") {
        foreach ($app->subheaders as $item) {
            if ($item->header_id == $header_id) {
                array_push($parent_array["results"], $item);
            }
        }
    } else {
        foreach ($app->subheaders as $item) {
            array_push($parent_array["results"], $item);
        }
    }
    http_response_code(200);
    echo json_encode($parent_array);
} else {
    http_response_code(404);

    // tell the user no products found
    echo json_encode(
        array("message" => "No products found.")
    );
}
