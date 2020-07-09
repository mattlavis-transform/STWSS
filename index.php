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
                    <h1 class="govuk-heading-l">Main menu</h1>

                    <h2 class="govuk-heading-m">Content</h2>
                    <p class="govuk-body"><a href="/content/edit.html">Create new content</a></p>
                    <p class="govuk-body"><a href="/content">View existing content</a></p>

                    <h2 class="govuk-heading-m">Tariff entities</h2>
                    <p class="govuk-body"><a href="/sections">Sections</a></p>
                    <p class="govuk-body"><a href="/chapters">Chapters</a></p>
                    <p class="govuk-body"><a href="/headings">Headings</a> (incomplete)</p>
                    <p class="govuk-body"><a href="/commodities">Commodity codes</a> (incomplete)</p>
                    <p class="govuk-body"><a href="/measure_types">Measure types</a></p>
                    <p class="govuk-body"><a href="/document_codes">Document codes</a></p>
                    <p class="govuk-body"><a href="/trade_types">Trade types</a></p>
                </div>
            </div>
        </main>
    </div>
    <?php
    require("./includes/footer.php");
?>
</body>
</html>