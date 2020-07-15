<?php
require("../includes/db.php");
$app = new application();
$c = new confirmation();
$c->get_data();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = $c->panel_title;
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
        $app->crumb_string = "";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <div class="govuk-panel govuk-panel--confirmation">
                        <h1 class="govuk-panel__title">
                            <?= $c->panel_title ?>
                        </h1>
                        <div class="govuk-panel__body">
                            <?= $c->panel_body ?>
                        </div>
                    </div>

                    <h2 class="govuk-heading-m">Next steps</h2>

                    <p class="govuk-body"><?= $c->step1 ?></p>
                    <p class="govuk-body"><?= $c->step2 ?></p>
                    <p class="govuk-body"><?= $c->step3 ?></p>
                    <p class="govuk-body"><?= $c->step4 ?></p>
                    
                </div>
            </div>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>