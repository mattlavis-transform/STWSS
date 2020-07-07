<?php
require("../includes/db.php");
$app = new application();
$content = new content();
$content->id = get_request("id");

?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Delete content";
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
                        <h1 class="govuk-heading-l">Delete content item <?= $content->id ?></h1>
                        <?php
                        new radio("yes_no", $app->yes_no, "Are you sure you want to delete this content item?", "", false, "");

                        new button("submit", "Continue");
                        new hidden("action", "delete_content_check");
                        new hidden("id", $content->id);
                        http://stw_data/includes/routes.php?action=delete_content&id=1
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