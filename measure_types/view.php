<?php
require("../includes/db.php");
$app = new application();
$measure_type = new measure_type();
$measure_type->populate();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "View measure type";
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
        $app->crumb_string = "Home|/;Measure types|/measure_types;Measure type " . $measure_type->id . "|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Measure type <?= $measure_type->id ?> - <?= $measure_type->description ?></h1>
                    <div class="govuk-tabs" data-module="govuk-tabs">
                        <h2 class="govuk-tabs__title">
                            Contents
                        </h2>
                        <ul class="govuk-tabs__list">
                            <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                                <a class="govuk-tabs__tab" href="#details">
                                    About this measure type
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#content">
                                    Content
                                </a>
                            </li>
                        </ul>
                        <div class="govuk-tabs__panel" id="details">
                            <h2 class="govuk-heading-l">About this measure type</h2>
                            <table class="govuk-table govuk-table--m">
                                <tbody class="govuk-table__body">
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Measure type</td>
                                        <td class="govuk-table__cell">Chapter <?= $measure_type->id ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Description</td>
                                        <td class="govuk-table__cell"><?= $measure_type->description ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h2 class="govuk-heading-m">Edit the measure type</h2>
                            <h3 class="govuk-label-wrapper"><label class="govuk-label govuk-label--s" for="more-detail">
                                    Measure type description overlay
                                </label>
                            </h3>
                            <div id="more-detail-hint" class="govuk-hint">
                                Optionally, you may override the measure type description for display within complex measures.
                            </div>
                            <textarea class="govuk-textarea" id="more-detail" name="more-detail" rows="2" aria-describedby="more-detail-hint"></textarea>

                            <h3 class="govuk-label-wrapper"><label class="govuk-label govuk-label--s" for="more-detail">
                                    Measure type subtext
                                </label>
                            </h3>
                            <div id="more-detail-hint" class="govuk-hint">
                                Optionally, you may include additional text to help to explain the purpose of the measure type within complex measures.
                            </div>
                            <textarea class="govuk-textarea" id="more-detail" name="more-detail" rows="2" aria-describedby="more-detail-hint"></textarea>
                            <form action="action.php">
                                <input type="hidden" name="measure_type_id" id="measure_type_id" value="<?= $measure_type->id ?>">
                                <button class="govuk-button" data-module="govuk-button">
                                    Update measure type
                                </button>
                            </form>


                            <h2 class="govuk-heading-xl govuk-!-margin-top-9">Error states</h2>
                            <h2 class="govuk-heading-m">Edit the measure type</h2>


                            <div class="govuk-error-summary" aria-labelledby="error-summary-title" role="alert" tabindex="-1" data-module="govuk-error-summary">
                                <h2 class="govuk-error-summary__title" id="error-summary-title">
                                    There is a problem
                                </h2>
                                <div class="govuk-error-summary__body">
                                    <ul class="govuk-list govuk-error-summary__list">
                                        <li>
                                            <a href="#passport-issued-error">Enter the measure type description overlay</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="govuk-form-group govuk-form-group--error">
                                <h1 class="govuk-label-wrapper"><label class="govuk-label govuk-label--s" for="more-detail">
                                        Measure type description overlay
                                    </label>
                                </h1>
                                <div id="more-detail-hint" class="govuk-hint">
                                    Optionally, you may override the measure type description for display within complex measures.
                                </div>
                                <span id="more-detail-error" class="govuk-error-message">
                                    <span class="govuk-visually-hidden">Error:</span> Enter the measure type description overlay
                                </span>
                                <textarea class="govuk-textarea govuk-textarea--error" id="more-detail" name="more-detail" rows="5" aria-describedby="more-detail-hint more-detail-error"></textarea>
                            </div>

                            <h3 class="govuk-label-wrapper"><label class="govuk-label govuk-label--s" for="more-detail">
                                    Measure type subtext
                                </label>
                            </h3>
                            <div id="more-detail-hint" class="govuk-hint">
                                Optionally, you may include additional text to help to explain the purpose of the measure type within complex measures.
                            </div>
                            <textarea class="govuk-textarea" id="more-detail" name="more-detail" rows="2" aria-describedby="more-detail-hint"></textarea>


                            <input type="hidden" name="measure_type_id" id="measure_type_id" value="<?= $measure_type->id ?>">
                            <button class="govuk-button" data-module="govuk-button">
                                Update measure type
                            </button>






                        </div>
                        <div class="govuk-tabs__panel" id="content">
                            <h2 class="govuk-heading-l">Content</h2>
                            <?php
                            if (count($measure_type->content) == 0) {
                                echo ("<p class='govuk-body'>There is no content attached to this measure type.");
                            } else {
                            ?>
                                <table class="govuk-table govuk-table--m">
                                    <thead class="govuk-table__head">
                                        <tr class="govuk-table__row">
                                            <th scope="col" class="govuk-table__header">ID</th>
                                            <th scope="col" class="govuk-table__header">Description</th>
                                            <th scope="col" class="govuk-table__header">Explanatory text</th>
                                            <th scope="col" class="govuk-table__header">URL</th>
                                            <th scope="col" class="govuk-table__header r">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="govuk-table__body">
                                        <?php
                                        foreach ($measure_type->content as $c) {
                                        ?>
                                            <tr class="govuk-table__row">
                                                <td class="govuk-table__cell"><?= $c->id ?></td>
                                                <td class="govuk-table__cell"><?= $c->step_description ?></td>
                                                <td class="govuk-table__cell"><?= $c->step_howto_description ?></td>
                                                <td class="govuk-table__cell"><a target="_blank" href='<?= $c->step_url ?>'><?= $c->step_url ?></a></td>
                                                <td class="govuk-table__cell r">
                                                    <a class="govuk-link" href="/content/edit.html?id=<?= $c->id ?>" title="Edit content item <?= $c->id ?>"><i class="far fa-edit"></i></a>
                                                    <a href="/includes/routes.php?action=delete_content_linkage&src=entity&link_type=measure_type&id=<?= $measure_type->id ?>&sid=<?= $c->unique_id ?>" title="Remove content item <?= $c->id ?> from measure type <?= $measure_type->id ?>"><i class='fas fa-trash-alt'></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            <?php
                            }
                            ?>
                            <p class="govuk-body"><a href="/content/add.html?link_type=measure_type&sid=<?= $measure_type->id ?>&id=<?= $measure_type->id ?>">Add content to measure type <?= $measure_type->id ?></a></p>
                        </div>
                    </div>
                </div>
        </main>
    </div>
    <?php
    require("../includes/footer.php");
    ?>
</body>

</html>