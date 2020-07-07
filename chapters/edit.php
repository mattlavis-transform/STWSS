<?php
require("../includes/db.php");
$app = new application();
$app->get_sections();
$app->get_headers();
$app->get_subheaders();
$section = new section();
$section->populate();
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
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l"><?= $section->edit_page_title ?></h1>
                    <h2 class="govuk-heading-m">About this section</h2>
                    <table class="govuk-table govuk-table--m">
                        <tbody class="govuk-table__body">
                            <tr class="govuk-table__row">
                                <th scope="row" class="govuk-table__cell">Section</td>
                                <td class="govuk-table__cell">Section <?= $section->numeral ?></td>
                            </tr>
                            <tr class="govuk-table__row">
                                <th scope="row" class="govuk-table__cell">Title</td>
                                <td class="govuk-table__cell"><?= $section->title ?></td>
                            </tr>
                            <tr class="govuk-table__row">
                                <th scope="row" class="govuk-table__cell">Including chapters</td>
                                <td class="govuk-table__cell">From <?= $section->chapter_from ?> to <?= $section->chapter_to ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <h2 class="govuk-heading-m">This section's content</h2>

<?php
    #new select("header", $app->headers, "My label 1", "My hint", false);
    #new select("subheader", $app->subheaders, "My label 2", "", false);

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