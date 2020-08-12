<?php
class radio
{
    // Class properties and methods go here
    public $id              = null;
    public $data            = null;
    public $label           = null;
    public $hint            = null;
    public $value           = null;
    public $mandatory       = null;
    public $legend_style    = null;
    public $error_class = "";
    public $error_state = false;

    function __construct($id, $data, $label, $hint, $mandatory, $value, $legend_style = "govuk-fieldset__legend--m")
    {
        $this->id           = $id;
        $this->data         = $data;
        $this->label        = $label;
        $this->hint         = $hint;
        $this->mandatory    = $mandatory;
        $this->value        = $value;
        $this->legend_style = $legend_style;
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
            <fieldset class="govuk-fieldset">
                <legend class="govuk-fieldset__legend <?= $this->legend_style ?>">
                    <h1 class="govuk-fieldset__heading">
                        <?= $this->label ?>
                    </h1>
                </legend>
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
                <div class="govuk-radios">
                    <?php
                    foreach ($this->data as $item) {
                        $id = $this->id . "_" . $item->id;
                        $name = $this->id;
                    ?>
                        <div class="govuk-radios__item">
                            <input class="govuk-radios__input" id="<?= $id ?>" name="<?= $name ?>" type="radio" value="<?= $item->id ?>">
                            <label class="govuk-label govuk-radios__label" for="<?= $id ?>">
                                <?= $item->description ?>
                            </label>
                        </div>
                    <?php
                    }
                    ?>

                </div>
            </fieldset>
        </div>
<?php
    }
}
