<?php
require("../includes/db.php");
$app = new application();
$commodity = new commodity();
$commodity->goods_nomenclature_item_id = get_querystring("goods_nomenclature_item_id");
$commodity->productline_suffix = get_querystring("productline_suffix");
$commodity->get_details();
$commodity->get_hierarchy();
$commodity->get_document_codes();
$commodity->get_import_measures();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Commodity code $commodity->goods_nomenclature_item_id";
require("../includes/meta.php");
?>

<body class="govuk-template__body ">
    <script>
        document.body.className = ((document.body.className) ? document.body.className + ' js-enabled' : 'js-enabled');
    </script>
    <a href="#main-content" class="govuk-skip-link">Skip to main content</a>
    <?php
    require("../includes/header.php");
    ?>
    <div class="govuk-width-container ">
        <?php
        $app->crumb_string = "Home|/;Commodities|/commodities;" . $commodity->goods_nomenclature_item_id . "|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Commodity <?= $commodity->goods_nomenclature_item_id ?></h1>


                    <div class="govuk-tabs" data-module="govuk-tabs">
                        <h2 class="govuk-tabs__title">
                            Contents
                        </h2>
                        <ul class="govuk-tabs__list">
                            <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                                <a class="govuk-tabs__tab" href="#commodity_details">
                                    Details
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#import_measures">
                                    Import measures
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#export_measures">
                                    Export measures
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#import_docs">
                                    Import docs
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#export_docs">
                                    Export docs
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#similar_codes">
                                    Similar
                                </a>
                            </li>
                        </ul>
                        <div class="govuk-tabs__panel" id="commodity_details">
                            <h2 class="govuk-heading-l">Commodity details</h2>
                            <table class="govuk-table govuk-table--m">
                                <tbody class="govuk-table__body">
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Commodity code</td>
                                        <td class="govuk-table__cell"><?= format_goods_nomenclature_item_id($commodity->goods_nomenclature_item_id) ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Product line suffix</td>
                                        <td class="govuk-table__cell"><?= $commodity->productline_suffix ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Description</td>
                                        <td class="govuk-table__cell"><?= $commodity->description ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Number of indents</td>
                                        <td class="govuk-table__cell"><?= $commodity->number_indents ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Hierarchy</td>
                                        <td class="govuk-table__cell"><?= $commodity->hierarchy_string ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="govuk-tabs__panel" id="import_measures">
                            <h2 class="govuk-heading-l">Import measures</h2>
                            <table class="govuk-table govuk-table--m">
                                <thead class="govuk-table__head">
                                    <tr class="govuk-table__row">
                                        <td colspan="7" class="govuk-table__cell b grey">Regulatory measures</td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="col" class="govuk-table__header">ID</th>
                                        <th scope="col" colspan=2 class="govuk-table__header">Measure type</th>
                                        <th scope="col" colspan=2 class="govuk-table__header">Geography</th>
                                        <th scope="col" class="govuk-table__header">Exclusions</th>
                                        <th scope="col" class="govuk-table__header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="govuk-table__body">
                                    <?php
                                    $has_shown_financial_message = false;
                                    foreach ($commodity->import_measures as $m) {
                                    ?>
                                        <tr class="govuk-table__row">
                                            <td class="govuk-table__cell"><?= $m->measure_sid ?></td>
                                            <td class="govuk-table__cell b"><?= $m->measure_type_id ?></td>
                                            <td class="govuk-table__cell"><?= $m->measure_type_description ?></td>
                                            <td class="govuk-table__cell b"><?= $m->geographical_area_id ?></td>
                                            <td class="govuk-table__cell"><?= $m->geographical_area_description ?></td>
                                            <td class="govuk-table__cell">TBC</td>
                                            <td class="govuk-table__cell r"><a href="xx.html">View</a></td>
                                        </tr>
                                        <?php
                                        if ($has_shown_financial_message == false) {
                                            if ($m->measure_realm == "Financial") {
                                        ?>
                                                <tr class="govuk-table__row">
                                                    <td colspan="7" class="govuk-table__cell b grey">Financial measures</td>
                                                </tr>
                                                <tr class="govuk-table__row">
                                                    <th scope="col" class="govuk-table__header">ID</th>
                                                    <th scope="col" colspan=2 class="govuk-table__header">Geography</th>
                                                    <th scope="col" colspan=2 class="govuk-table__header">Measure type</th>
                                                    <th scope="col" class="govuk-table__header">Actions</th>
                                                </tr>
                                    <?php
                                                $has_shown_financial_message = true;
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="govuk-tabs__panel" id="export_measures">
                            <h2 class="govuk-heading-l">Export measures</h2>
                            <table class="govuk-table govuk-table--m">
                                <tbody class="govuk-table__body">
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Commodity code</td>
                                        <td class="govuk-table__cell"><?= format_goods_nomenclature_item_id($commodity->goods_nomenclature_item_id) ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Product line suffix</td>
                                        <td class="govuk-table__cell"><?= $commodity->productline_suffix ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Description</td>
                                        <td class="govuk-table__cell"><?= $commodity->description ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Number of indents</td>
                                        <td class="govuk-table__cell"><?= $commodity->number_indents ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="govuk-tabs__panel govuk-tabs__panel--hidden" id="import_docs">
                            <h2 class="govuk-heading-l">Import documentation</h2>
                            <table class="govuk-table govuk-table--m">
                                <caption class="govuk-table__caption">Document codes referenced by this commodity</caption>
                                <thead class="govuk-table__head">
                                    <tr class="govuk-table__row">
                                        <th scope="col" class="govuk-table__header">Document code</th>
                                        <th scope="col" class="govuk-table__header">Requirement</th>
                                        <th scope="col" class="govuk-table__header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="govuk-table__body">
                                    <?php
                                    foreach ($commodity->certificates as $c) {
                                    ?>
                                        <tr class="govuk-table__row">
                                            <td class="govuk-table__cell"><?= $c->document_code ?></td>
                                            <td class="govuk-table__cell"><?= $c->description ?></td>
                                            <td class="govuk-table__cell r"><a href="/documents/view.html?code=<?= $c->document_code ?>">View</a></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="govuk-tabs__panel govuk-tabs__panel--hidden" id="export_docs">
                            <h2 class="govuk-heading-l">Export documentation</h2>
                            <table class="govuk-table govuk-table--m">
                                <caption class="govuk-table__caption">Document codes referenced by this commodity</caption>
                                <thead class="govuk-table__head">
                                    <tr class="govuk-table__row">
                                        <th scope="col" class="govuk-table__header">Document code</th>
                                        <th scope="col" class="govuk-table__header">Requirement</th>
                                        <th scope="col" class="govuk-table__header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="govuk-table__body">
                                    <?php
                                    foreach ($commodity->certificates as $c) {
                                    ?>
                                        <tr class="govuk-table__row">
                                            <td class="govuk-table__cell"><?= $c->document_code ?></td>
                                            <td class="govuk-table__cell"><?= $c->description ?></td>
                                            <td class="govuk-table__cell r"><a href="/documents/view.html?code=<?= $c->document_code ?>">View</a></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="govuk-tabs__panel govuk-tabs__panel--hidden" id="similar_codes">
                            <h2 class="govuk-heading-l">Similar codes</h2>
                            <table class="govuk-table govuk-table--m">
                                <thead class="govuk-table__head">
                                    <tr class="govuk-table__row">
                                        <th scope="col" class="govuk-table__header">Case manager</th>
                                        <th scope="col" class="govuk-table__header">Cases opened</th>
                                        <th scope="col" class="govuk-table__header">Cases closed</th>
                                    </tr>
                                </thead>
                                <tbody class="govuk-table__body">
                                    <tr class="govuk-table__row">
                                        <td class="govuk-table__cell">David Francis</td>
                                        <td class="govuk-table__cell">98</td>
                                        <td class="govuk-table__cell">95</td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <td class="govuk-table__cell">Paul Farmer</td>
                                        <td class="govuk-table__cell">122</td>
                                        <td class="govuk-table__cell">131</td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <td class="govuk-table__cell">Rita Patel</td>
                                        <td class="govuk-table__cell">126</td>
                                        <td class="govuk-table__cell">142</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>