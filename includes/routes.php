<?php
require("./db.php");
$action = get_request("action");
$app = new application();

switch ($action) {
    case "commodity_search":
        //application::debug();
        $goods_nomenclature_item_id = get_request("goods_nomenclature_item_id");
        $url = "/commodities/view.html?goods_nomenclature_item_id=" . $goods_nomenclature_item_id;
        header("Location: " . $url);
        break;

    case "select_linkage_type":
        $link_type = get_request("link_type");
        $id = get_request("id");
        $url = "/content/link_02.html?link_type=" . $link_type . "&id=" . $id;
        header("Location: " . $url);
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

    default:
        application::debug();
}
