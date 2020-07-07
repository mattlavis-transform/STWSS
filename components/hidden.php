<?php
class hidden
{
    // Class properties and methods go here
    public $id      = null;
    public $value = null;

    function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
        $this->display();
    }
    public function display()
    {
?>
        <input type="hidden" id="<?= $this->id ?>" name="<?= $this->id ?>" value="<?= $this->value ?>">
<?php
    }
}
