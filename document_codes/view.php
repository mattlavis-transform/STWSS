<?php
require("../includes/db.php");
$app = new application();
$document_code = new document_code();
$document_code->populate();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "View document code";
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
        $app->crumb_string = "Home|/;Document codes|/document_codes;Document code " . $document_code->id . "|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Document code <?= $document_code->id ?> - <?= $document_code->description ?></h1>
                    <div class="govuk-tabs" data-module="govuk-tabs">
                        <h2 class="govuk-tabs__title">
                            Contents
                        </h2>
                        <ul class="govuk-tabs__list">
                            <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                                <a class="govuk-tabs__tab" href="#details">
                                    About this document code
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#content">
                                    Content
                                </a>
                            </li>
                        </ul>
                        <div class="govuk-tabs__panel" id="details">
                            <h2 class="govuk-heading-l">About this document code</h2>
                            <table class="govuk-table govuk-table--m">
                                <tbody class="govuk-table__body">
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Document code</td>
                                        <td class="govuk-table__cell">Chapter <?= $document_code->id ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Description</td>
                                        <td class="govuk-table__cell"><?= $document_code->description ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="govuk-tabs__panel" id="content">
                            <h2 class="govuk-heading-l">Content</h2>
                            <?php
                            if (count($document_code->content) == 0) {
                                echo ("<p class='govuk-body'>There is no content attached to this document code.");
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
                                        foreach ($document_code->content as $c) {
                                        ?>
                                            <tr class="govuk-table__row">
                                                <td class="govuk-table__cell"><?= $c->id ?></td>
                                                <td class="govuk-table__cell"><?= $c->step_description ?></td>
                                                <td class="govuk-table__cell"><?= $c->step_howto_description ?></td>
                                                <td class="govuk-table__cell"><a target="_blank" href='<?= $c->step_url ?>'><?= $c->step_url ?></a></td>
                                                <td class="govuk-table__cell r">
                                                    <a class="govuk-link" href="/content/edit.html?id=<?= $c->id ?>" title="Edit content item <?= $c->id ?>"><i class="far fa-edit"></i></a>
                                                    <a href="/includes/routes.php?action=delete_content_linkage&src=entity&link_type=document_code&id=<?= $document_code->id ?>&sid=<?= $c->unique_id ?>" title="Remove content item <?= $c->id ?> from document code <?= $document_code->id ?>"><i class='fas fa-trash-alt'></i></a>
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
                            <p class="govuk-body"><a href="/content/add.html?link_type=document_code&sid=<?= $document_code->id ?>&id=<?= $document_code->id ?>">Add content to document code <?= $document_code->id ?></a></p>
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