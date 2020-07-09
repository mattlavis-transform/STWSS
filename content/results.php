<?php
require("../includes/db.php");
$app = new application();
$app->find_content();
$app->get_content_linking_methods();

?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Find content";
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
                    <h1 class="govuk-heading-l">Link existing content to <?= $app->link_type_string ?></h1>
                    <?php
                        $find_url = "find.html?link_type=" . $app->link_type . "&sid=" . $app->sid . "&id=" . $app->id . "&free_text=" . $app->free_text;
                        if (count($app->found_content) > 0) {
                        echo ("<p>Select a content item to link to $app->link_type_string");
                        echo (" or <a class='govuk-link' href='" . $find_url . "'>search again</a>.</p>");
                        echo ("<table class='govuk-table govuk-table--m'>");
                        echo ("<thead class='govuk-table__head'>");
                        echo ("<tr class='govuk-table__row'>");
                        echo ("<th scope='col' class='govuk-table__header'>Content</th>");
                        echo ("<th scope='col' class='govuk-table__header'>Actions</th>");
                        echo ("</tr>");
                        echo ("</thead>");
                        echo ("<tbody class='govuk-table__body'>");
                        foreach ($app->found_content as $c) {
                            $use_url = "/includes/routes.php?action=create_content_linkage&id=$c->id&link_type=$app->link_type&section=$app->sid";
                            $app->get_linkage_url($c->id);
                            echo ("<tr class='govuk-table__row'>\n");
                            echo ("<td class='govuk-table__cell'>");
                            echo ("<b>" . $c->step_description . "</b><br />");
                            echo ($c->step_howto_description . "<br />");
                            echo ("<a class='govuk_link' target='_blank' href='$c->step_url'>" . $c->step_url . "</a>");
                            echo ("</td>");
                            echo ("<td class='govuk-table__cell'><a class='govuk-link' href='$app->linkage_url'>Use this content</a></td>");
                            echo ("</tr>");
                        }
                        echo ("</body>");
                        echo ("</table>");
                    } else {
                        echo ("<p>No matching content has been found. <a class='govuk-link' href='" . $find_url . "'>Search again</a></p>");
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