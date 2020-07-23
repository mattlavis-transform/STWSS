<?php
require("../includes/db.php");
$app = new application();
//$app->get_certificate_content_assignment();
$app->get_page();
$app->get_sections_from_API();
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
                    <h1 class="govuk-heading-l">Sections of the Trade Tariff</h1>
                    <table class="govuk-table govuk-table--m sticky">
                        <caption class="govuk-table__caption">List of sections</caption>
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">Section</th>
                                <th scope="col" class="govuk-table__header">Title</th>
                                <th scope="col" class="govuk-table__header">Chapters</th>
                                <th scope="col" class="govuk-table__header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->sections as $section) {
                            ?>
                                <tr id="row_<?= $section->id ?>" class="govuk-table__row">
                                    <td class="govuk-table__cell"><?= $section->numeral ?></td>
                                    <td class="govuk-table__cell"><?= $section->description ?></td>
                                    <td class="govuk-table__cell"><?= $section->chapter_from ?> to <?= $section->chapter_to ?></td>

                                    <td class="govuk-table__cell nr">
                                        <a href="section.html?id=<?= $section->id ?>">View chapters</a>
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