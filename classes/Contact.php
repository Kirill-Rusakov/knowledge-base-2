<?php
require_once 'Entity.php';

class Contact extends Entity 
{
    private $firstName;
    private $lastName;
    private $deals = [];
    
    public function __construct($id, $firstName, $lastName, $deals = []) 
    {
        parent::__construct($id);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->deals = $deals;
    }
    
    public function getFirstName() 
    {
        return $this->firstName;
    }
    
    public function getLastName() 
    {
        return $this->lastName;
    }
    
    public function getFullName() 
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function getDeals() 
    {
        return $this->deals;
    }
    
    public function toArray() 
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'deals' => $this->deals
        ];
    }
}