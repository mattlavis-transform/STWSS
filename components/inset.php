<?php
class inset
{
    // Class properties and methods go here
    public $content = null;

    function __construct($content)
    {
        $this->content = $content;
        $this->display();
    }
    public function display()
    {
?>
        <div class="govuk-inset-text">
            <?=$this->content?>
        </div>
<?php
    }
}
