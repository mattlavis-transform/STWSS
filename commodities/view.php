<?php
require("../includes/db.php");
$app = new application();
$commodity = new commodity();
$commodity->goods_nomenclature_item_id = get_querystring("goods_nomenclature_item_id");
$commodity->productline_suffix = get_querystring("productline_suffix");
$ret = $commodity->get_commodity_from_api();
if ($ret) {
    $commodity->get_signposting_steps();
    $app->crumb_string = "Home|/;Commodities|/commodities;" . $commodity->goods_nomenclature_item_id . "|";
    $h1_tag = "Commodity " . $commodity->goods_nomenclature_item_id;
    //$commodity->get_document_codes();
    //$commodity->get_import_measures();
} else {
    $app->crumb_string = "Home|/;Commodities|/commodities;Error|";
    $h1_tag = "Error - commodity code not found";
}
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
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l"><?= $h1_tag ?></h1>

                    <?php
                    if ($ret) {
                    ?>
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
                                    <a class="govuk-tabs__tab" href="#content">
                                        Content
                                    </a>
                                </li>
                            </ul>
                            <div class="govuk-tabs__panel" id="commodity_details">
                                <h2 class="govuk-heading-l">Commodity details</h2>

                                <table class="govuk-table govuk-table--m">
                                    <tbody class="govuk-table__body">
                                        <tr class="govuk-table__row">
                                            <th scope="row" class="govuk-table__cell">SID</td>
                                            <td class="govuk-table__cell"><?= $commodity->goods_nomenclature_sid ?></td>
                                        </tr>
                                        <tr class="govuk-table__row">
                                            <th scope="row" class="govuk-table__cell">Commodity</td>
                                            <td class="govuk-table__cell"><?= format_goods_nomenclature_item_id($commodity->goods_nomenclature_item_id) ?></td>
                                        </tr>
                                        <tr class="govuk-table__row">
                                            <th scope="row" class="govuk-table__cell"><abbr title="Product line suffix">PLS</abbr></td>
                                            <td class="govuk-table__cell"><?= $commodity->productline_suffix ?></td>
                                        </tr>
                                        <tr class="govuk-table__row">
                                            <th scope="row" class="govuk-table__cell">Description</td>
                                            <td class="govuk-table__cell"><?= $commodity->description ?></td>
                                        </tr>
                                        <tr class="govuk-table__row">
                                            <th scope="row" class="govuk-table__cell">Indents</td>
                                            <td class="govuk-table__cell"><?= $commodity->number_indents ?></td>
                                        </tr>
                                        <tr class="govuk-table__row">
                                            <th scope="row" class="govuk-table__cell">Hierarchy</td>
                                            <td class="govuk-table__cell"><?= $commodity->hierarchy_string ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="govuk-tabs__panel" id="content">
                                <h2 class="govuk-heading-l">Content</h2>
                                <?php
                                if (count($commodity->content) == 0) {
                                    echo ("<p class='govuk-body'>There is no content attached to this commodity.");
                                } else {
                                ?>
                                    <table class="govuk-table govuk-table--m">
                                        <thead class="govuk-table__head">
                                            <tr class="govuk-table__row">
                                                <th scope="col" class="govuk-table__header">ID</th>
                                                <th scope="col" class="govuk-table__header">Description</th>
                                                <th scope="col" class="govuk-table__header">Explanatory text</th>
                                                <th scope="col" class="govuk-table__header">URL</th>
                                                <th scope="col" class="govuk-table__header r">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="govuk-table__body">
                                            <?php
                                            foreach ($commodity->content as $c) {
                                            ?>
                                                <tr class="govuk-table__row">
                                                    <td class="govuk-table__cell"><?= $c->id ?></td>
                                                    <td class="govuk-table__cell"><?= $c->step_description ?></td>
                                                    <td class="govuk-table__cell"><?= $c->step_howto_description ?></td>
                                                    <td class="govuk-table__cell"><a target="_blank" href='<?= $c->step_url ?>'><?= $c->step_url ?></a></td>
                                                    <td class="govuk-table__cell r">
                                                        <a href="/content/edit.html?id=<?= $c->id ?>" title="Edit content item <?= $c->id ?>"><i class="far fa-edit"></i></a>
                                                        <a href="/includes/routes.php?action=delete_content_linkage&src=entity&link_type=section&id=<?= $section->id ?>&sid=<?= $c->unique_id ?>" title="Remove content item <?= $c->id ?> from section <?= $section->numeral ?>"><i class='fas fa-trash-alt'></i></a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                <?php
                                }
                                ?>
                                <p class="govuk-body"><a href="/content/add.html?link_type=commodity&sid=<?= $commodity->goods_nomenclature_sid ?>&id=<?= $commodity->goods_nomenclature_item_id ?>">Add content to commodity <?= $commodity->goods_nomenclature_item_id ?></a></p>
                            </div>

                        </div>
                    <?php
                    }
                    ?>


                </div>
            </div>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>