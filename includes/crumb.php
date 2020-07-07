<?php
    $crumbs = array();
    if ($app->crumb_string != "") {
        $arr = explode(";", $app->crumb_string);
        foreach($arr as $item) {
            $parts = explode("|", $item);
            $crumb_item = new reusable();
            $crumb_item->text = $parts[0];
            $crumb_item->url = $parts[1];
            array_push($crumbs, $crumb_item);
        }
        if (count($crumbs) > 0) {
            echo ('<div class="govuk-breadcrumbs">');
            echo ('<ol class="govuk-breadcrumbs__list">');
            foreach ($crumbs as $c) {
                echo ('<li class="govuk-breadcrumbs__list-item">');
                if ($c->url == "") {
                    echo ($c->text);
                } else {
                    echo ('<a class="govuk-breadcrumbs__link" href="' . $c->url . '">' . $c->text . '</a>');
                }
                echo ('</li>');
    
            }
            echo ('</ol>');
            echo ('</div>');
        }
?>
<!--
<div class="govuk-breadcrumbs">
    <ol class="govuk-breadcrumbs__list">
        <li class="govuk-breadcrumbs__list-item">
            <a class="govuk-breadcrumbs__link" href="#">Home</a>
        </li>
        <li class="govuk-breadcrumbs__list-item">
            <a class="govuk-breadcrumbs__link" href="#">Passports, travel and living abroad</a>
        </li>
        <li class="govuk-breadcrumbs__list-item">
            <a class="govuk-breadcrumbs__link" href="#">Travel abroad</a>
        </li>
    </ol>
</div>
//-->
<?php
}
?>