<?php
require("../includes/db.php");
$app = new application();
$app->get_headers();
$app->get_subheaders();
$content = new content();
$content->get_link_options();
$content->populate();
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
        $app->crumb_string = "Home|/;Content|/content;Content item " . $content->id . "|/content/edit.html?id=" . $content->id . ";Link content|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <form novalidate method="get" action="/includes/routes.php">
                <div class="govuk-grid-row">
                    <div class="govuk-grid-column-full">
                        <h1 class="govuk-heading-l">Add link to content item <?= $content->id ?></h1>
                        <table class="govuk-table">
                            <tbody class="govuk-table__body">
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">ID</th>
                                    <td class="govuk-table__cell"><?= $content->id ?></td>
                                </tr>
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">Step title</th>
                                    <td class="govuk-table__cell"><?= $content->step_description ?></td>
                                </tr>
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">Explanatory text</th>
                                    <td class="govuk-table__cell"><?= $content->step_howto_description ?></td>
                                </tr>
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">URL</th>
                                    <td class="govuk-table__cell"><?= $content->step_url ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        new radio("link_type", $content->link_options, "Select the type of object to which you would like to link this content item", "", false, $content->subheader_id);
                        new button("submit", "Continue");
                        new hidden("action", "select_linkage_type");
                        new hidden("id", $content->id);
                        ?>

                    </div>
                </div>
            </form>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>