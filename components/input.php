<?php
class input
{
    // Class properties and methods go here
    public $id                  = null;
    public $label               = null;
    public $hint                = null;
    public $value               = null;
    public $mandatory           = null;
    public $class               = null;
    public $maxlength           = null;
    public $maxlength_string    = null;
    public $error_class = "";
    public $error_state = false;

    function __construct($id, $label, $hint, $mandatory, $value, $class, $maxlength)
    {
        $this->id           = $id;
        $this->label        = $label;
        $this->hint         = $hint;
        $this->mandatory    = $mandatory;
        $this->value        = $value;
        $this->class        = $class;
        $this->maxlength    = $maxlength;
        if ($this->maxlength != "") {
            $this->maxlength_string = " size='" . $this->maxlength . "' maxlength='" . $this->maxlength . "'";
        } else {
            $this->maxlength_string = "";
        }
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
        <div class="govuk-form-group <?= $this->error_class ?>">
            <label class="govuk-label govuk-label--s" for="<?= $this->id ?>">
                <?= $this->label ?>
            </label>
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
            <input value="<?= $this->value ?>" class="govuk-input <?= $this->class ?>" id="<?= $this->id ?>" name="<?= $this->id ?>" type="text" <?= $this->maxlength_string ?>>
        </div>
<?php
    }
}
