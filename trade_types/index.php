<?php
require("../includes/db.php");
$app = new application();
$app->get_trade_types();
?>
<!DOCTYPE html>
<html lang="en" class="govuk-template ">
<?php
$page_title = "Trade types";
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
        $app->crumb_string = "Home|/;Trade types|";
        require("../includes/crumb.php");
        ?>
        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">
            <div class="govuk-grid-row">
                <div class="govuk-grid-column-full">
                    <h1 class="govuk-heading-l">Trade types</h1>
                    <table class="govuk-table govuk-table--m sticky">
                        <thead class="govuk-table__head">
                            <tr class="govuk-table__row">
                                <th scope="col" class="govuk-table__header">Trade type</th>
                                <th scope="col" class="govuk-table__header">Content assigned</th>
                            </tr>
                        </thead>
                        <tbody class="govuk-table__body">
                            <?php
                            foreach ($app->trade_types as $trade_type) {
                            ?>
                                <tr class="govuk-table__row">
                                    <td class="govuk-table__cell b" style="width:20%"><?= $trade_type->description ?></td>
                                    <td class="govuk-table__cell" style="width:80%">
                                        <?php
                                        if (count($trade_type->content) > 0) {
                                            echo ('<ol class="govuk-list govuk-list--m govuk-list--number">');
                                            foreach ($trade_type->content as $c) {
                                                $remove_url = "/includes/routes.php?action=delete_content_linkage&src=entity_index&link_type=trade_type&sid=" . $c->sid;
                                                echo ("<li><a class='govuk-link' href='/content/edit.html?id=" . $c->id . "'>");
                                                echo ($c->step_description);
                                                echo ("</a>&nbsp;");
                                                echo ("<a class='govuk-link' href='" . $remove_url . "'><i class='fas fa-trash-alt'></i></a>");
                                                echo ("</li>");
                                            }
                                            echo ("</ol>");

                                        }
                                        ?>
                                        <div class="mt_0_5em"><a class="govuk-link" href="/content/add.html?link_type=trade_type&id=<?=$trade_type->id?>&sid=<?=$trade_type->id?>">Add content to trade type</a></div>
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