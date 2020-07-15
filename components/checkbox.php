<?php
class checkbox
{
    // Class properties and methods go here
    public $id              = null;
    public $data            = null;
    public $label           = null;
    public $hint            = null;
    public $value           = null;
    public $mandatory       = null;
    public $legend_style    = null;

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
?>
        <div class="govuk-form-group">
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
                ?>
                <div class="govuk-radios">
                    <?php
                    foreach ($this->data as $item) {
                        $id = $this->id . "_" . $item->id;
                        $name = $this->id;
                    ?>

                        <div class="govuk-checkboxes__item">
                            <input class="govuk-checkboxes__input" id="<?= $id ?>" name="<?= $name ?>" type="checkbox" value="<?= $item->id ?>">
                            <label class="govuk-label govuk-checkboxes__label" for="<?= $id ?>">
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
?>