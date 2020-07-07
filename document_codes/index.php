<?php
require("../includes/db.php");
$app = new application();
$app->get_document_codes();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Document codes";
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
        $app->crumb_string = "Home|/;Document codes|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Document codes</h1>
                    <table class="govuk-table govuk-table--m sticky">
                        <caption class="govuk-table__caption">Current document codes</caption>
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">ID</th>
                                <th scope="col" class="govuk-table__header">Description</th>
                                <th scope="col" class="govuk-table__header">Actions</th>
                                <th scope="col" class="govuk-table__header">Content assigned</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->document_codes as $document_code) {
                            ?>
                                <tr class="govuk-table__row">
                                    <td class="govuk-table__cell b" style="width:10%"><?= $document_code->id ?></td>
                                    <td class="govuk-table__cell" style="width:35%"><?= $document_code->title ?></td>
                                    <td class="govuk-table__cell nr" style="width:15%"><a href="view.html?id=<?= $document_code->id ?>">View document code</a></td>
                                    <td class="govuk-table__cell" style="width:40%">
                                        <?php
                                        if (count($document_code->content) > 0) {
                                            echo ('<ol class="govuk-list govuk-list--m govuk-list--number">');
                                            foreach ($document_code->content as $c) {
                                                echo ("<li><a class='govuk-link' href='/content/edit.html?id=" . $c->id . "'>");
                                                echo ($c->step_description);
                                                echo ('</a>&nbsp;');
                                                $remove_url = "/includes/routes.php?action=delete_content_linkage&src=entity_index&link_type=document_code&sid=" . $c->sid;
                                                echo ("<a class='govuk-link' href='" . $remove_url . "'><i class='fas fa-trash-alt'></i></a>");
                                                echo ("</li>");
                                            }
                                            echo ("</ol>");
                                        }
                                        ?>
                                        <div class="mt_0_5em"><a class="govuk-link" href="/content/add.html?link_type=document_code&id=<?=$document_code->id?>&sid=<?=$document_code->id?>">Add content to document code</a></div>
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