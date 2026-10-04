<?php
class Transaction
{
    private $id;
    private $type;
    private $description;
    private $amount;
    private $category;

    public function __construct($id, $type, $description, $amount, $category = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->description = $description;
        $this->amount = $amount;
        $this->category = $category;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCategory()
    {
        return $this->category;
    }
}
?>
