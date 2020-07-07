<?php
require("../includes/db.php");
$app = new application();
$app->get_chapters();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Chapters";
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
        $app->crumb_string = "Home|/;Chapters|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Chapters</h1>
                    <table class="govuk-table govuk-table--m sticky">
                        <caption class="govuk-table__caption">Current chapters</caption>
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">Chapter</th>
                                <th scope="col" class="govuk-table__header">Title</th>
                                <th scope="col" class="govuk-table__header">Actions</th>
                                <th scope="col" class="govuk-table__header">Content assigned</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->chapters as $chapter) {
                            ?>
                                <tr class="govuk-table__row">
                                    <td class="govuk-table__cell b" style="width:10%"><?= $chapter->id ?></td>
                                    <td class="govuk-table__cell" style="width:35%"><?= $chapter->title ?></td>
                                    <td class="govuk-table__cell nr" style="width:15%"><a href="view.html?id=<?= $chapter->id ?>">View chapter</a></td>
                                    <td class="govuk-table__cell" style="width:40%">
                                        <?php
                                        if (count($chapter->content) > 0) {
                                            echo ('<ol class="govuk-list govuk-list--m govuk-list--number">');
                                            foreach ($chapter->content as $c) {
                                                echo ("<li><a class='govuk-link' href='/content/edit.html?id=" . $c->id . "'>");
                                                echo ($c->step_description);
                                                echo ('</a>&nbsp;');
                                                $remove_url = "/includes/routes.php?action=delete_content_linkage&src=entity_index&link_type=chapter&sid=" . $c->sid;
                                                echo ("<a class='govuk-link' href='" . $remove_url . "'><i class='fas fa-trash-alt'></i></a>");
                                                echo ("</li>");
                                            }
                                            echo ("</ol>");
                                        }
                                        ?>
                                        <div class="mt_0_5em"><a class="govuk-link" href="/content/add.html?link_type=chapter&id=<?=$chapter->id?>&sid=<?=$chapter->id?>">Add content to chapter</a></div>
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