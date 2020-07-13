<?php
require("../includes/db.php");
$app = new application();
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
        $app->crumb_string = "Home|/;Commodities|";
        require("../includes/crumb.php");
?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Commodities</h1>
                    <form novalidate action="/includes/routes.php" method="post" class="get">
                        <div class="govuk-form-group">
                            <h1 class="govuk-label-wrapper"><label class="govuk-label govuk-label--m" for="goods_nomenclature_item_id">
                                    Search for a commodity code
                                </label>
                            </h1>
                            <span id="contents-hint" class="govuk-hint">
                                Enter the 10-digit commodity code
                            </span>
                            <input class="govuk-input govuk-input--width-10" id="goods_nomenclature_item_id" name="goods_nomenclature_item_id" type="text">
                        </div>

                        <div class="govuk-form-group">
                            <input type="hidden" name="action" id="action" value="commodity_search" />
                            <button class="govuk-button" data-module="govuk-button">
                                Search
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>