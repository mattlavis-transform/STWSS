<?php
require("../includes/db.php");
$app = new application();
$certificate = new certificate();
$certificate->code = get_querystring("code");
$certificate->get_details();
$certificate->get_usage();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Main menu";
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
        $app->crumb_string = "Home|/;Documents|/documents;Document " . $certificate->code . "|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Document code <?= $certificate->code ?></h1>




                    <div class="govuk-tabs" data-module="govuk-tabs">
                        <h2 class="govuk-tabs__title">
                            Contents
                        </h2>
                        <ul class="govuk-tabs__list">
                            <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                                <a class="govuk-tabs__tab" href="#details">
                                    Document code details
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#commodities">
                                    Commodities
                                </a>
                            </li>
                        </ul>
                        <div class="govuk-tabs__panel" id="details">
                            <h2 class="govuk-heading-l">Document code details</h2>
                            <table class="govuk-table govuk-table--m">
                                <tbody class="govuk-table__body">
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Code</th>
                                        <td class="govuk-table__cell"><?= $certificate->code ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Description</th>
                                        <td class="govuk-table__cell"><?= $certificate->description ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="govuk-tabs__panel" id="commodities">
                            <h2 class="govuk-heading-l">Commodities that reference this document code</h2>
                            <table class="govuk-table govuk-table--m mt_2em">
                                <thead class="govuk-table__head">
                                    <tr class="govuk-table__row">
                                        <th scope="col" class="govuk-table__header">Commodity</th>
                                        <th scope="col" class="govuk-table__header c">Indent</th>
                                        <th scope="col" class="govuk-table__header">Description</th>
                                        <th scope="col" class="govuk-table__header r">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="govuk-table__body">
                                    <?php
                                    foreach ($certificate->commodities as $c) {
                                    ?>
                                        <tr class="govuk-table__row">
                                            <td class="govuk-table__cell"><?= format_goods_nomenclature_item_id($c->goods_nomenclature_item_id) ?></td>
                                            <td class="govuk-table__cell c"><?= $c->number_indents ?></td>
                                            <td class="govuk-table__cell"><?= $c->description ?></td>
                                            <td class="govuk-table__cell r"><a href="/commodities/view.html?productline_suffix=80&goods_nomenclature_item_id=<?= $c->goods_nomenclature_item_id ?>">View</a></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
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