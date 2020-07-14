<?php
require("./includes/db.php");
$app = new application();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template">
<?php
//pre ($_SERVER['DOCUMENT_ROOT']);
$page_title = "Main menu";
require("./includes/meta.php");
?>

<body class="govuk-template__body ">
    <script>
        document.body.className = ((document.body.className) ? document.body.className + ' js-enabled' : 'js-enabled');
    </script>
    <a href="#main-content" class="govuk-skip-link">Skip to main content</a>
    <?php
    require("./includes/header.php");
    ?>
    <div class="govuk-width-container ">
        <?php
        $app->crumb_string = "";
        require("./includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-two-thirds">
                    <h1 class="govuk-heading-l">Login</h1>
                    <form method="get" action="/includes/routes.php">
                        <div class="govuk-form-group">
                            <label class="govuk-label" for="width-10">
                                User name
                            </label>
                            <input class="govuk-input govuk-input--width-10" id="user_name" name="user_name" type="text">
                        </div>
                        <div class="govuk-form-group">
                            <label class="govuk-label" for="width-10">
                                Password
                            </label>
                            <input class="govuk-input govuk-input--width-10" id="password" name="password" type="password">
                        </div>
                        <?php
                        new hidden("action", "login");
                        ?>
                        <button class="govuk-button" data-module="govuk-button">
                            Log in
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php
    require("./includes/footer.php");
    ?>
</body>

</html>