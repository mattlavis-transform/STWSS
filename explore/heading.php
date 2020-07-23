<?php
require("../includes/db.php");
$app = new application();
$app->get_commodities_from_API();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Explore the trade tariff";
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
        $id = get_querystring("id");
        $chapter = substr($id, 0, 2);
        $heading = substr($id, 2, 2);
        $app->crumb_string = "Home|/;Explore the trade tariff|/explore;Section " . integerToRoman($app->heading->section_id) . "|/explore/section.html?id=" . $app->heading->section_id . ";Chapter " . $chapter . "|/explore/chapter.html?id=" . $chapter . ";Heading " . $heading . "|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Heading <?= $app->heading->id ?> - <?= $app->heading->description ?></h1>
                    <p><a target="_blank" class="govuk-link" href="report.html?id=<?= $app->heading->id ?>">Download comparative measure report</a>
                    <br />This will produce a report of the measures applies to the declarable commodities and may take several minutes.</p>
                    <table class="govuk-table govuk-table--m sticky">
                        <caption class="govuk-table__caption">List of headings</caption>
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">Commodity</th>
                                <th scope="col" class="govuk-table__header c">Suffix</th>
                                <th scope="col" class="govuk-table__header c">Indents</th>
                                <th scope="col" class="govuk-table__header">Title</th>
                                <th scope="col" class="govuk-table__header c">End line</th>
                                <!--<th scope="col" class="govuk-table__header">Actions</th>//-->
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->commodities as $commodity) {
                            ?>
                                <tr id="row_<?= $commodity->id ?>" class="govuk-table__row">
                                    <td class="govuk-table__cell"><?= format_goods_nomenclature_item_id($commodity->goods_nomenclature_item_id) ?></td>
                                    <td class="govuk-table__cell c"><?= $commodity->productline_suffix ?></td>
                                    <td class="govuk-table__cell c"><?= $commodity->number_indents ?></td>
                                    <td class="govuk-table__cell"><?= $commodity->description ?></td>
                                    <td class="govuk-table__cell c"><?= YN($commodity->declarable) ?></td>
                                    <!--
                                    <td class="govuk-table__cell nr">
                                        <a href="chapter.html?id=<?= $commodity->id ?>">View measures</a>
                                    </td>
                                    //-->

                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>