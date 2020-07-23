<?php

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=report.csv");
header("Pragma: no-cache");
header("Expires: 0");

$col_delimiter = ",";
$row_delimiter = "\n";
$string_delimiter = '"';

require("../includes/db.php");
$app = new application();
$app->get_commodities_from_API();

// Get the measures
foreach ($app->commodities as $commodity) {
    if ($commodity->declarable) {
        $commodity->get_measures();
    }
}

// Write the measures
echo ("Commodity,Number indents,Description,Measure ID,Measure type\n");
foreach ($app->commodities as $commodity) {
    if ($commodity->declarable) {
        foreach ($commodity->measures as $measure) {
            echo ($string_delimiter . $commodity->goods_nomenclature_item_id . $string_delimiter . $col_delimiter);
            echo ($string_delimiter . $commodity->number_indents . $string_delimiter . $col_delimiter);
            echo ($string_delimiter . $commodity->cleansed_description() . $string_delimiter . $col_delimiter);
            echo ($measure->measure_sid . $col_delimiter);
            echo ($string_delimiter . $measure->measure_type_id . $string_delimiter . $col_delimiter);

            echo ($row_delimiter);
        }
    }
}
