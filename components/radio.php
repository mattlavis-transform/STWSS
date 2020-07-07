<?php
class radio
{
    // Class properties and methods go here
    public $id          = null;
    public $data        = null;
    public $label       = null;
    public $hint        = null;
    public $value       = null;
    public $mandatory   = null;

    function __construct($id, $data, $label, $hint, $mandatory, $value)
    {
        $this->id           = $id;
        $this->data         = $data;
        $this->label        = $label;
        $this->hint         = $hint;
        $this->mandatory    = $mandatory;
        $this->value        = $value;
        $this->display();
    }
    public function display()
    {
?>
        <div class="govuk-form-group">
            <fieldset class="govuk-fieldset">
                <legend class="govuk-fieldset__legend govuk-fieldset__legend--m">
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
