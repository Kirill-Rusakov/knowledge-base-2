<?php
require_once 'Entity.php';

class Deal extends Entity 
{
    private $name;
    private $amount;
    private $contacts = [];
    
    public function __construct($id, $name, $amount, $contacts = []) 
    {
        parent::__construct($id);
        $this->name = $name;
        $this->amount = $amount;
        $this->contacts = $contacts;
    }
    
    public function getName() 
    {
        return $this->name;
    }
    
    public function getAmount() 
    {
        return $this->amount;
    }
    
    public function getContacts() 
    {
        return $this->contacts;
    }
    
    public function toArray() 
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'contacts' => $this->contacts
        ];
    }
}