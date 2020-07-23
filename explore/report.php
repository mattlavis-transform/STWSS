<?php
require("../includes/db.php");
$wts = get_querystring(("wts"));
if ($wts == "1") {
    $start_string       = "<pre>";
    $end_string         = "</pre>";
    $col_delimiter      = ",";
    $row_delimiter      = "<br />";
    $string_delimiter   = '"';

} else {
    $id = get_querystring("id");
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=measure_report_" . $id . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $start_string       = "";
    $end_string         = "";
    $col_delimiter      = ",";
    $row_delimiter      = "\n";
    $string_delimiter   = '"';
    
}

$app = new application();
$app->get_commodities_from_API();
$app->get_measure_types2();

// Get the measures
foreach ($app->commodities as $commodity) {
    if ($commodity->declarable) {
        $commodity->get_measures();
    }
}
$app->import_measure_permutations = set($app->import_measure_permutations);
$app->export_measure_permutations = set($app->export_measure_permutations);

// Write the measures
echo ($start_string);
//echo ("Commodity,Number indents,Description,Measure ID,Import measures,Export measures,");
echo ("Commodity,Indents,Description,");
// Write the column titles for the import measures
$index = 0;
foreach($app->import_measure_permutations as $item) {
    $index += 1;
    echo ($item);
    echo (",");
}
// Write the column titles for the export measures
$index = 0;
foreach($app->export_measure_permutations as $item) {
    $index += 1;
    echo ($item);
    if ($index < count($app->export_measure_permutations)) {
        echo (",");
    }
}
echo ($row_delimiter);
// Write the rows
foreach ($app->commodities as $commodity) {
    if ($commodity->declarable) {
        echo ($string_delimiter . $commodity->goods_nomenclature_item_id . $string_delimiter . $col_delimiter);
        echo ($string_delimiter . $commodity->number_indents . $string_delimiter . $col_delimiter);
        echo ($string_delimiter . $commodity->cleansed_description() . $string_delimiter . $col_delimiter);

        $measure_match = "";
        foreach($app->import_measure_permutations as $item) {
            if (in_array($item, $commodity->measure_array_import)) {
                $measure_match .= "Y,";
            } else {
                $measure_match .= ",";
            }
        }
        foreach($app->export_measure_permutations as $item) {
            if (in_array($item, $commodity->measure_array_export)) {
                $measure_match .= "Y,";
            } else {
                $measure_match .= ",";
            }
        }
        $measure_match = rtrim($measure_match, ",");
        echo ($measure_match);
        

        //echo ($string_delimiter . $commodity->measure_string_import . $string_delimiter . $col_delimiter);
        //echo ($string_delimiter . $commodity->measure_string_export . $string_delimiter);
    /*
        foreach ($commodity->measures as $measure) {
            echo ($string_delimiter . $commodity->goods_nomenclature_item_id . $string_delimiter . $col_delimiter);
            echo ($string_delimiter . $commodity->number_indents . $string_delimiter . $col_delimiter);
            echo ($string_delimiter . $commodity->cleansed_description() . $string_delimiter . $col_delimiter);
            echo ($measure->measure_sid . $col_delimiter);
            echo ($string_delimiter . $measure->measure_type_description . $string_delimiter . $col_delimiter);

        }
        */
        echo ($row_delimiter);
    }
}
echo ($end_string);

