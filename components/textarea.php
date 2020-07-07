<?php
class textarea
{
    // Class properties and methods go here
    public $id          = null;
    public $label       = null;
    public $hint        = null;
    public $value       = null;
    public $rows        = null;
    public $mandatory   = null;
    public $error_class = "";
    public $error_state = false;

    function __construct($id, $label, $hint, $mandatory, $rows, $value)
    {
        $this->id           = $id;
        $this->label        = $label;
        $this->hint         = $hint;
        $this->rows         = $rows;
        $this->mandatory    = $mandatory;
        $this->value        = $value;
        $this->hint_control = "";
        $this->error_class  = "";
        $this->display();
    }
    public function display()
    {
        global $app;
        if (in_array($this->id, $app->error_array)) {
            $this->error_state = true;
            $this->error_class = " govuk-form-group--error";
            $msg = $app->get_error_message($this->id);
        }
?>
        <div class="govuk-form-group  <?= $this->error_class ?>">
            <label class="govuk-label govuk-label--s" for="<?= $this->id ?>"><?= $this->label ?></label>
            <?php
            if ($this->hint != "") {
                $this->hint_control = ' aria-describedby="more-detail-hint"';
            ?>
                <span id="contents-hint" class="govuk-hint"><?= $this->hint ?></span>
            <?php
            }
            if ($this->error_state == true) {
            ?>
                <span class="govuk-error-message">
                    <span class="govuk-visually-hidden">Error:</span> <?= $msg ?>
                </span>
            <?php
            }
            ?>
            <textarea class="govuk-textarea" id="<?= $this->id ?>" name="<?= $this->id ?>" rows="<?= $this->rows ?>" <?= $this->hint_control ?>><?= $this->value ?></textarea>
        </div>
<?php
    }
}
