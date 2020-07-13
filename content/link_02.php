<?php
require("../includes/db.php");
$app = new application();
$content = new content();
$content->populate();
$link_type = get_request("link_type");
switch ($link_type) {
    case "section":
        $page_title = "Link content item " . $content->id . " to section";
        break;
    case "chapter":
        $page_title = "Link content item " . $content->id . " to chapter";
        break;
    case "commodity":
        $page_title = "Link content item " . $content->id . " to commodity code";
        break;
    case "measure_type":
        $page_title = "Link content item " . $content->id . " to measure type";
        break;
    case "document_code":
        $page_title = "Link content item " . $content->id . " to document code";
        break;
    case "trade_type":
        $page_title = "Link content item " . $content->id . " to trade type";
        break;
}

?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
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
                        <h1 class="govuk-heading-l"><?= $page_title ?></h1>
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
                        <?php
                        switch ($link_type) {
                            case "section":
                                $app->get_sections();
                                new select("section", $app->sections, "Select a section", "Select from one of the available sections", false, "");
                                break;
                            case "chapter":
                                $app->get_chapters();
                                new select("chapter", $app->chapters, "Select a chapter", "Select from one of the available chapters", false, "");
                                break;
                            case "commodity":
                                new input("commodity_code", "Enter a commodity code", "Enter at least 4 digits from the commodity code", false, "", "govuk-input--width-10", 10);
                                break;
                            case "measure_type":
                                new input("measure_type", "Enter the ID of the measure type", "Enter the 3-digit measure type ID", false, "", "govuk-input--width-3", 3);
                                break;
                            case "document_code":
                                new input("document_code", "Enter the document code", "Enter the 4-digit document code", false, "", "govuk-input--width-4", 4);
                                break;
                            case "trade_type":
                                $app->get_trade_types();
                                new radio("trade_type", $app->trade_types, "Select a trade type", "Identify if this content is to apply to all import trade or all export trade", false, "");
                                break;
                        }
                        new button("submit", "Create link");
                        new hidden("action", "create_content_linkage");
                        new hidden("id", $content->id);
                        new hidden("link_type", $link_type);
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