<?php
require("./db.php");
$action = get_request("action");
$app = new application();

//application::debug();

switch ($action) {
    case "commodity_search":
        $goods_nomenclature_item_id = get_request("goods_nomenclature_item_id");
        $url = "/commodities/view.html?goods_nomenclature_item_id=" . $goods_nomenclature_item_id;
        header("Location: " . $url);
        break;

    case "select_linkage_type":
        $link_type = get_request("link_type");
        $id = get_request("id");
        if ($link_type != "") {
            $url = "/content/link_02.html?link_type=" . $link_type . "&id=" . $id;
            header("Location: " . $url);
        } else {
            $errors = array();
            array_push($errors, "section");
            $data = serialize($errors);
            $data_encrypted = SA_Encryption::encrypt_to_url_param($data);
            $url = "/content/link_01.html?link_type=" . $link_type . "&id=" . $id . "&err=1&data=" . $data_encrypted;
            header("Location: " . $url);
        }
        break;

    case "create_content_linkage":
        $c = new content();
        $c->link();
        break;

    case "delete_content_linkage":
        $c = new content();
        $c->unlink();
        break;

    case "add_content_to_item":
        $app->apply_content_linking_method();
        break;

    case "create_content":
        $content = new content();
        $content->create();
        break;

    case "update_content":
        $content = new content();
        $content->update();
        break;

    case "delete_content_check":
        $content = new content();
        $content->delete();
        break;

    case "logout":
        $app->logout();
        break;

    case "login":
        $app->login();
        break;

    default:
        application::debug();
}
