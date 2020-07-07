<?php
class button
{
    // Class properties and methods go here
    public $id      = null;
    public $caption = null;

    function __construct($id, $caption)
    {
        $this->id = $id;
        $this->caption = $caption;
        $this->display();
    }
    public function display()
    {
?>
        <button class="govuk-button" id="<?= $this->id ?>" name="<?= $this->id ?>" data-module="govuk-button">
            <?= $this->caption ?>
        </button>
<?php
    }
}
