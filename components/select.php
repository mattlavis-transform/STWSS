<?php
class select
{
    // Class properties and methods go here
    public $id          = null;
    public $data        = null;
    public $label       = null;
    public $hint        = null;
    public $value       = null;
    public $mandatory   = null;
    public $error_class = "";
    public $error_state = false;

    function __construct($id, $data, $label, $hint, $mandatory, $value, $label_class = "govuk-label--s")
    {
        $this->id           = $id;
        $this->data         = $data;
        $this->label        = $label;
        $this->hint         = $hint;
        $this->mandatory    = $mandatory;
        $this->value        = $value;
        $this->error_class  = "";
        $this->label_class  = $label_class;

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
            <label class="govuk-label <?= $this->label_class ?> for=" <?= $this->id ?>">
                <?= $this->label ?>
            </label>
            <?php
            if ($this->hint != "") {
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
            <select class="govuk-select" id="<?= $this->id ?>" name="<?= $this->id ?>">
                <option value="0">Please select</option>
                <?php
                foreach ($this->data as $item) {
                    if ($item->id == $this->value) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }
                    echo ('<option' . $selected . ' value="' . $item->id . '">' . $item->description . '</option>');
                }
                ?>
            </select>
        </div>
<?php
    }
}
