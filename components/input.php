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
        $this->display();
    }
    public function display()
    {
?>
        <div class="govuk-form-group">
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
            ?>
            <input value="<?= $this->value ?>" class="govuk-input <?= $this->class ?>" id="<?= $this->id ?>" name="<?= $this->id ?>" type="text" <?= $this->maxlength_string ?>>
        </div>
<?php
    }
}
