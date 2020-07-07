<?php
require("../includes/db.php");
$app = new application();
$chapter = new chapter();
$chapter->populate();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "View chapter";
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
        $app->crumb_string = "Home|/;Chapters|/chapters;Chapter " . $chapter->id . "|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Chapter <?= $chapter->id ?> - <?= $chapter->description ?></h1>
                    <div class="govuk-tabs" data-module="govuk-tabs">
                        <h2 class="govuk-tabs__title">
                            Contents
                        </h2>
                        <ul class="govuk-tabs__list">
                            <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                                <a class="govuk-tabs__tab" href="#details">
                                    About this chapter
                                </a>
                            </li>
                            <li class="govuk-tabs__list-item">
                                <a class="govuk-tabs__tab" href="#content">
                                    Content
                                </a>
                            </li>
                        </ul>
                        <div class="govuk-tabs__panel" id="details">
                            <h2 class="govuk-heading-l">About this chapter</h2>
                            <table class="govuk-table govuk-table--m">
                                <tbody class="govuk-table__body">
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Chapter</td>
                                        <td class="govuk-table__cell">Chapter <?= $chapter->id ?></td>
                                    </tr>
                                    <tr class="govuk-table__row">
                                        <th scope="row" class="govuk-table__cell">Description</td>
                                        <td class="govuk-table__cell"><?= $chapter->description ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="govuk-tabs__panel" id="content">
                            <h2 class="govuk-heading-l">Content</h2>
                            <?php
                            if (count($chapter->content) == 0) {
                                echo ("<p class='govuk-body'>There is no content attached to this chapter.");
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
                                        foreach ($chapter->content as $c) {
                                        ?>
                                            <tr class="govuk-table__row">
                                                <td class="govuk-table__cell"><?= $c->id ?></td>
                                                <td class="govuk-table__cell"><?= $c->step_description ?></td>
                                                <td class="govuk-table__cell"><?= $c->step_howto_description ?></td>
                                                <td class="govuk-table__cell"><a target="_blank" href='<?= $c->step_url ?>'><?= $c->step_url ?></a></td>
                                                <td class="govuk-table__cell r">
                                                    <a href="/content/edit.html?id=<?= $c->id ?>" title="Edit content item <?= $c->id ?>"><i class="far fa-edit"></i></a>
                                                    <a href="/includes/routes.php?action=delete_content_linkage&src=entity&link_type=chapter&id=<?= $chapter->id ?>&sid=<?= $c->unique_id ?>" title="Remove content item <?= $c->id ?> from chapter <?= $chapter->numeral ?>"><i class='fas fa-trash-alt'></i></a>
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
                            <p class="govuk-body"><a href="/content/add.html?link_type=chapter&sid=<?= $chapter->id ?>&id=<?= $chapter->id ?>">Add content to chapter <?= $chapter->id ?></a></p>
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