<?php
require("../includes/db.php");
$app = new application();
$app->get_certificate_content_assignment();
$app->get_certificates();
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
        $app->crumb_string = "Home|/;Documents|";
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
                                <th scope="col" class="govuk-table__header">Code</th>
                                <th scope="col" class="govuk-table__header">Description</th>
                                <th scope="col" class="govuk-table__header">Content assigned</th>
                                <th scope="col" class="govuk-table__header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->certificates as $c) {
                            ?>
                                <tr class="govuk-table__row">
                                    <td class="govuk-table__cell b"><?= $c->code ?></td>
                                    <td class="govuk-table__cell"><?= $c->description ?></td>
                                    <td class="govuk-table__cell">
                                        <?php
                                        if ($c->content == "") {
                                            echo ("<a class='govuk-link' href=''>Add content</a>");
                                        } else {
                                            echo ("<a class='govuk-link' href=''>$c->content</a>");

                                        }
                                        ?>
                                    </td>
                                    <td class="govuk-table__cell r"><a href="/documents/view.html?code=<?= $c->code ?>">View</a></td>
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