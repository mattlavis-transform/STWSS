<?php
require("../includes/db.php");
$app = new application();
$app->free_text = get_querystring("free_text");
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
            <form novalidate method="get" action="results.html">
                <div class="govuk-grid-row">
                    <div class="govuk-grid-column-full">
                        <h1 class="govuk-heading-l">Link existing content to <?= $app->link_type_string ?></h1>
                        <?php
                        new input("free_text", "Enter free text to search for content", "This will search in all fields in the content block", false, $app->free_text, "", "");

                        new button("submit", "Search for content");
                        new hidden("action", "find_content");
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