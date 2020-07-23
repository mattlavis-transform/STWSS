<?php
require("../includes/db.php");
$app = new application();
$app->get_headings_from_API();
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
        $app->crumb_string = "Home|/;Explore the trade tariff|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Chapter <?= $app->chapter->id ?> - <?= $app->chapter->description ?></h1>
                    <table class="govuk-table govuk-table--m sticky">
                        <caption class="govuk-table__caption">List of headings</caption>
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">Heading</th>
                                <th scope="col" class="govuk-table__header">Title</th>
                                <th scope="col" class="govuk-table__header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->headings as $heading) {
                            ?>
                                <tr id="row_<?= $heading->id ?>" class="govuk-table__row">
                                    <td class="govuk-table__cell"><?= format_goods_nomenclature_item_id($heading->id) ?></td>
                                    <td class="govuk-table__cell"><?= $heading->description ?></td>

                                    <td class="govuk-table__cell nr">
                                        <a href="heading.html?id=<?= $heading->id ?>">View commodities</a><br />
                                        <a target="_blank" href="report.html?id=<?= $heading->id ?>">View measure report</a>
                                    </td>

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