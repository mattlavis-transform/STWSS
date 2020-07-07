<?php
class error_summary
{
    // Class properties and methods go here
    public $content = null;

    function __construct()
    {
        global $app;
        if ($app->err == "1") {
            $this->display();
        }
    }

    public function display()
    {
        global $app;
        ?>
        <div class="govuk-error-summary" aria-labelledby="error-summary-title" role="alert" tabindex="-1" data-module="govuk-error-summary">
            <h2 class="govuk-error-summary__title" id="error-summary-title">
                There is a problem
            </h2>
            <div class="govuk-error-summary__body">
                <ul class="govuk-list govuk-error-summary__list">
                <?php
                foreach ($app->error_array as $error) {
                    $msg = $app->get_error_message($error);
                    echo ('<li><a href="#' . $error . '">' . $msg . '</a></li>');
                }
                ?>
                </ul>
            </div>
        </div>
<?php
    }
}
