<?php
require("../includes/db.php");
$app = new application();
$app->get_headers();
$app->check_for_errors();
$app->get_subheaders();
$app->get_content_linking_methods();
$app->get_trade_types();

$content = new content();
$content->populate();
if ($content->id != "") {
    $width_class = "govuk-grid-column-full";
} else {
    $width_class = "govuk-grid-column-two-thirds";
    //$width_class = "govuk-grid-column-full";
}

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
            <form method="get" action="/includes/routes.php" novalidate>
                <div class="govuk-grid-row">
                    <div class="<?= $width_class ?>">
                        <h1 class="govuk-heading-l"><?= $content->edit_page_title ?></h1>
                        <?php
                        $app->show_content_linkage_message();
                        if ($content->id != "") {
                        ?>
                            <div class="govuk-tabs" data-module="govuk-tabs">
                                <h2 class="govuk-tabs__title">
                                    Contents
                                </h2>
                                <ul class="govuk-tabs__list">
                                    <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                                        <a class="govuk-tabs__tab" href="#details">
                                            Content details
                                        </a>
                                    </li>
                                    <?php
                                    if ($content->id != "") {
                                    ?>
                                        <li class="govuk-tabs__list-item">
                                            <a class="govuk-tabs__tab" href="#linkage">
                                                Linkage
                                            </a>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                                <div class="govuk-tabs__panel" id="details">
                                <?php
                            }
                            new error_summary();
                            if (count($app->error_array) > 0) {
                                $content->step_description = get_session_variable("step_description");
                                $content->step_howto_description = get_session_variable("step_howto_description");
                                $content->step_url = get_session_variable("step_url");
                                $content->header_id = get_session_variable("header_id");
                                $content->subheader_id = get_session_variable("subheader_id");
                                $content->country_exclusions = get_session_variable("country_exclusions");
                            }
                            new textarea("step_description", "Step title", "This is the content which will be displayed in the step's hyperlink", false, 3, $content->step_description);
                            new textarea("step_howto_description", "Explanatory text", "Optionally, add some explanatory text to advise users further", false, 2, $content->step_howto_description);
                            new textarea("step_url", "URL", "Please enter the full URL including the https://", false, 2, $content->step_url);

                                ?>


                                <div class="govuk-form-group">
                                    <fieldset class="govuk-fieldset" aria-describedby="trade_type-conditional-hint">
                                        <legend class="govuk-fieldset__legend govuk-fieldset__legend--s">
                                            <h1 class="govuk-fieldset__heading">
                                                Select a trade type </h1>
                                        </legend>
                                        <span id="trade_type-conditional-hint" class="govuk-hint">
                                            Identify if this content is to apply to import trade, export trade or both. If you do not explicitly link the step to any other entities, then the step will apply to the specified trade type(s) under all circumstances. You must select at least one of these options.
                                        </span>
                                        <div class="govuk-checkboxes" data-module="govuk-checkboxes">
                                            <div class="govuk-checkboxes__item">
                                                <input class="govuk-checkboxes__input" id="trade_type-conditional" name="trade_type" type="checkbox" value="email" data-aria-controls="conditional-trade_type-conditional">
                                                <label class="govuk-label govuk-checkboxes__label" for="trade_type-conditional">
                                                    Import
                                                </label>
                                            </div>
                                            <div class="govuk-checkboxes__conditional govuk-checkboxes__conditional--hidden" id="conditional-trade_type-conditional">
                                                <?php
                                                new select("header_import", $app->headers, "Select the import heading", "", false, $content->header_id, "");
                                                new select("subheader_import", $app->subheaders, "Select the import subheading", "", false, $content->subheader_id, "");
                                                ?>
                                            </div>
                                            <div class="govuk-checkboxes__item">
                                                <input class="govuk-checkboxes__input" id="trade_type-conditional-2" name="trade_type" type="checkbox" value="phone" data-aria-controls="conditional-trade_type-conditional-2">
                                                <label class="govuk-label govuk-checkboxes__label" for="trade_type-conditional-2">
                                                    Export
                                                </label>
                                            </div>
                                            <div class="govuk-checkboxes__conditional govuk-checkboxes__conditional--hidden" id="conditional-trade_type-conditional-2">
                                                <?php
                                                new select("header_import", $app->headers, "Select the import heading", "", false, $content->header_id, "");
                                                new select("subheader_import", $app->subheaders, "Select the import subheading", "", false, $content->subheader_id, "");
                                                ?>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <?php
                                new hidden("id", $content->id);
                                new hidden("country_exclusions", "");
                                new hidden("sid", $app->sid);
                                new hidden("identifier", $app->identifier);
                                if ($content->id == "") {
                                    new hidden("action", "create_content");
                                } else {
                                    new hidden("action", "update_content");
                                }
                                new hidden("link_type", $app->link_type);
                                new button("submit", $content->button_face);
                                if ($content->id != "") {
                                ?>
                                </div>
                            <?php
                                }
                                if ($content->id != "") {
                            ?>

                                <div class="govuk-tabs__panel" id="linkage">
                                    <h2 class="govuk-heading-l">Linkage</h2>
                                    <?php
                                    $content->get_linkages("section");
                                    $content->get_linkages("chapter");
                                    $content->get_linkages("commodity");
                                    $content->get_linkages("measure_type");
                                    $content->get_linkages("document_code");
                                    $content->get_linkages("trade_type");
                                    ?>
                                </div>
                            <?php
                                }
                                if ($content->id != "") {
                            ?>
                            </div>
                        <?php
                                }
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