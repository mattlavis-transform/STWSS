<!DOCTYPE html>
<html lang="en" class="govuk-template ">

<head>
    <meta charset="utf-8">
    <title>
        Question page
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b0c0c">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <link rel="shortcut icon" sizes="16x16 32x32 48x48" href="/govuk/assets/images/favicon.ico" type="image/x-icon">
    <link rel="mask-icon" href="/govuk/assets/images/govuk-mask-icon.svg" color="#0b0c0c">
    <link rel="apple-touch-icon" sizes="180x180" href="/govuk/assets/images/govuk-apple-touch-icon-180x180.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/govuk/assets/images/govuk-apple-touch-icon-167x167.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/govuk/assets/images/govuk-apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" href="/govuk/assets/images/govuk-apple-touch-icon.png">



    <!--[if lte IE 8]><link href="/public/stylesheets/application-ie8.css" rel="stylesheet" type="text/css" /><![endif]-->
    <!--[if gt IE 8]><!-->
    <link href="/public/stylesheets/application.css" media="all" rel="stylesheet" type="text/css" />
    <link href="/public/stylesheets/local.css" media="all" rel="stylesheet" type="text/css" />
    <!--<![endif]-->






    <meta property="og:image" content="/govuk/assets/images/govuk-opengraph-image.png">
</head>

<body class="govuk-template__body ">
    <script>
        document.body.className = ((document.body.className) ? document.body.className + ' js-enabled' : 'js-enabled');
    </script>



    <a href="#main-content" class="govuk-skip-link">Skip to main content</a>



<?php
    require("./includes/header.php");
?>







    <div class="govuk-width-container ">

        <a class="govuk-back-link" href="/url/of/previous/page">Back</a>

        <main class="govuk-main-wrapper govuk-main-wrapper--auto-spacing" id="main-content" role="main">


            <div class="govuk-grid-row">
                <div class="govuk-grid-column-two-thirds">

                    <form novalidate action="/consignment-paperwork" method="post" class="form">













                        <div class="govuk-form-group">

                            <fieldset class="govuk-fieldset" aria-describedby="contents-hint">

                                <legend class="govuk-fieldset__legend govuk-fieldset__legend--l">

                                    <h1 class="govuk-fieldset__heading">
                                        Does the consignment contain any of the following prioritised items?
                                    </h1>

                                </legend>


                                <span id="contents-hint" class="govuk-hint">
                                    Select all that apply.
                                </span>


                                <div class="govuk-checkboxes">








                                    <div class="govuk-checkboxes__item">
                                        <input class="govuk-checkboxes__input" id="contents" name="contents" type="checkbox" value="Live chicks">
                                        <label class="govuk-label govuk-checkboxes__label" for="contents">
                                            Live chicks
                                        </label>

                                    </div>









                                    <div class="govuk-checkboxes__item">
                                        <input class="govuk-checkboxes__input" id="contents-2" name="contents" type="checkbox" value="Live fish or shellfish">
                                        <label class="govuk-label govuk-checkboxes__label" for="contents-2">
                                            Live fish or shellfish
                                        </label>

                                    </div>









                                    <div class="govuk-checkboxes__item">
                                        <input class="govuk-checkboxes__input" id="contents-3" name="contents" type="checkbox" value="COVID-19 prioritised goods">
                                        <label class="govuk-label govuk-checkboxes__label" for="contents-3">
                                            COVID-19 prioritised goods
                                        </label>

                                    </div>



                                </div>
                            </fieldset>


                        </div>








                        <button class="govuk-button" data-module="govuk-button">
                            Continue
                        </button>


                    </form>

                </div>
            </div>


        </main>
    </div>




    <?php
    require("./includes/footer.php");
?>
</body>
</html>