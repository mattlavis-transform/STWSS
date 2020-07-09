<?php
require("../includes/db.php");
$app = new application();
//$app->get_certificate_content_assignment();
$app->get_content();
$app->get_content_linkage();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Content";
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
        $app->crumb_string = "Home|/;Content|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Content</h1>
                    <details class="govuk-details" data-module="govuk-details">
                        <summary class="govuk-details__summary">
                            <span class="govuk-details__summary-text">
                                Filter / search the content database
                            </span>
                        </summary>
                        <div class="govuk-details__text">
                            Please select the criteria on which to filter the content database<br />
                            <span class="red">This is for show only and needs to be 'designed'</span>
                            <form novalidate>
                                <table class="govuk-table govuk-table--m horizontal">
                                    <tbody class="govuk-table__body">
                                        <tr>
                                            <td class="w30">
                                                <label class="govuk-label govuk-label--s" for="section">Filter by section</label>
                                            </td>
                                            <td class="w70">
                                                <input size="2" maxlength="2" class="govuk-input govuk-input--width-2" id="section" name="section" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="govuk-label govuk-label--s" for="section">Filter by chapter</label>
                                            </td>
                                            <td>
                                                <input size="2" maxlength="2" class="govuk-input govuk-input--width-2" id="chapter" name="chapter" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="govuk-label govuk-label--s" for="measure_type">Filter by measure type</label>
                                            </td>
                                            <td>
                                                <input size="3" maxlength="3" class="govuk-input govuk-input--width-4" id="measure_type" name="measure_type" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="govuk-label govuk-label--s" for="measure_type">Filter by commodity code</label>
                                            </td>
                                            <td>
                                                <input size="3" maxlength="3" class="govuk-input govuk-input--width-10" id="commodity_code" name="commodity_code" type="text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="govuk-label govuk-label--s" for="measure_type">Free text</label>
                                            </td>
                                            <td>
                                                <input size="3" maxlength="3" class="govuk-input" id="freetext" name="freetext" type="text">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="govuk-button" data-module="govuk-button">
                                    Filter results
                                </button>
                            </form>
                        </div>
                    </details>

                    <table class="govuk-table govuk-table--m sticky">
                        <caption class="govuk-table__caption">Current content</caption>
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">ID</th>
                                <th scope="col" class="govuk-table__header">Content</th>
                                <th scope="col" class="govuk-table__header">Linked&nbsp;to</th>
                                <th scope="col" class="govuk-table__header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->content as $c) {
                            ?>
                                <tr class="govuk-table__row">
                                    <td class="govuk-table__cell"><?= $c->id ?></td>
                                    <td class="govuk-table__cell">
                                        <b><?= $c->step_description ?></b><br />
                                        <?= $c->step_howto_description ?><br />
                                        <a target="_blank" rel="noopener noreferrer" href="<?= $c->step_url ?>"><?= $c->step_url ?></a><br /><br />
                                        <em>Assigned to section / subsection:</em><br />
                                        &gt; <?= $c->header_description ?><br />
                                        &gt; <?= $c->subheader_description ?>
                                    </td>
                                    <td class="govuk-table__cell" style="width:30%">
                                        <?php
                                        if (count($c->linkage) == 0) {
                                            echo ("Not linked to any database entities");
                                        } else {
                                            echo ('<ol class="govuk-list govuk-list--m govuk-list--number">');
                                            foreach ($c->linkage as $l) {
                                                $remove_url = "/includes/routes.php?action=delete_content_linkage&src=content_index&link_type=section&sid=" . $l->id;
                                                echo ("<li>");
                                                echo ($l->entity_id . " - " . $l->description . "&nbsp;");
                                                echo ("<a class='govuk-link' href='" . $remove_url . "'><i class='fas fa-trash-alt'></i></a>");
                                                echo ("</li>");
                                            }
                                            echo ("</ol>");
                                        }
                                        ?>
                                    </td>
                                    <td class="govuk-table__cell nr">
                                        <a href="edit.html?id=<?= $c->id ?>">View / edit content</a><br />
                                        <a href="link_01.html?id=<?= $c->id ?>">Add link to content</a><br />
                                        <a class="govuk_link" href="delete.html?id=<?= $c->id ?>">Delete content</a>
                                    </td>

                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>