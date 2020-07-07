<?php
require("../includes/db.php");
$app = new application();
$app->get_content_linking_methods();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Add content";
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
            <form novalidate method="get" action="/includes/routes.php">
                <div class="govuk-grid-row">
                    <div class="govuk-grid-column-full">
                        <h1 class="govuk-heading-l">Link content to <?= $app->link_type_string ?></h1>
                        <!--
                        <table class="govuk-table">
                            <tbody class="govuk-table__body">
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">ID</th>
                                    <td class="govuk-table__cell"><?= $content->id ?></td>
                                </tr>
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">Title</th>
                                    <td class="govuk-table__cell"><?= $content->step_description ?></td>
                                </tr>
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">Description</th>
                                    <td class="govuk-table__cell"><?= $content->step_howto_description ?></td>
                                </tr>
                                <tr class="govuk-table__row">
                                    <th scope="col" class="govuk-table__cell">URL</th>
                                    <td class="govuk-table__cell"><?= $content->step_url ?></td>
                                </tr>
                            </tbody>
                        </table>
                        //-->

                        <?php
                        new radio("content_linking_method", $app->content_linking_methods, "Do you want to link to existing content or create a new content item?", "", false, "");
                        new button("submit", "Continue");
                        new hidden("action", "add_content_to_item");
                        new hidden("link_type", $app->link_type);
                        new hidden("sid", $app->sid);
                        new hidden("id", $app->identifier);
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