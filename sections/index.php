<?php
require("../includes/db.php");
$app = new application();
$app->get_sections();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Sections";
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
        $app->crumb_string = "Home|/;Sections|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Sections</h1>
                    <table class="govuk-table govuk-table--m sticky">
                        <caption class="govuk-table__caption">Current sections</caption>
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">Section</th>
                                <th scope="col" class="govuk-table__header">Title</th>
                                <th scope="col" class="govuk-table__header">Actions</th>
                                <th scope="col" class="govuk-table__header">Content assigned</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->sections as $s) {
                            ?>
                                <tr class="govuk-table__row">
                                    <td class="govuk-table__cell b" style="width:10%"><?= $s->numeral ?></td>
                                    <td class="govuk-table__cell" style="width:35%"><?= $s->title ?></td>
                                    <td class="govuk-table__cell nr" style="width:15%"><a href="view.html?id=<?= $s->id ?>">View section</a></td>
                                    <td class="govuk-table__cell" style="width:40%">
                                        <?php
                                        if (count($s->content) > 0) {
                                            echo ('<ol class="govuk-list govuk-list--m govuk-list--number">');
                                            foreach ($s->content as $c) {
                                                echo ("<li><a class='govuk-link' href='/content/edit.html?id=" . $c->id . "'>");
                                                echo ($c->step_description);
                                                echo ("</a>&nbsp;");
                                                $remove_url = "/includes/routes.php?action=delete_content_linkage&src=entity_index&link_type=section&sid=" . $c->sid;
                                                echo ("<a class='govuk-link' href='" . $remove_url . "'><i class='fas fa-trash-alt'></i></a>");
                                                echo ("</li>");
                                            }
                                            echo ("</ol>");
                                        }
                                        ?>
                                        <div class="mt_0_5em"><a class="govuk-link" href="/content/add.html?link_type=section&sid=<?=$s->id?>&id=<?=$s->numeral?>">Add content to section</a></div>
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