<?php
class Database 
{
    private $dataFile = __DIR__ . '/../data/data.json';
    
    public function __construct() 
    {
        if (!file_exists($this->dataFile)) {
            $this->initData();
        }
    }
    
    private function initData() 
    {
        $data = [
            'deals' => [
                1 => [
                    'id' => 1,
                    'name' => 'Хотят люстру',
                    'amount' => 4000,
                    'contacts' => [15, 25]
                ],
                14 => [
                    'id' => 14,
                    'name' => 'Хотят светильник',
                    'amount' => 3500,
                    'contacts' => [15]
                ]
            ],
            'contacts' => [
                15 => [
                    'id' => 15,
                    'first_name' => 'Иван',
                    'last_name' => 'Петров',
                    'deals' => [1, 14]
                ],
                25 => [
                    'id' => 25,
                    'first_name' => 'Наталья',
                    'last_name' => 'Сидорова',
                    'deals' => [1]
                ]
            ]
        ];
        
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function getData() 
    {
        return json_decode(file_get_contents($this->dataFile), true);
    }
    
    public function saveData($data) 
    {
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function getDeals() 
    {
        $data = $this->getData();
        return $data['deals'] ?? [];
    }
    
    public function getContacts() 
    {
        $data = $this->getData();
        return $data['contacts'] ?? [];
    }
    
    public function getDeal($id) 
    {
        $data = $this->getData();
        return $data['deals'][$id] ?? null;
    }
    
    public function getContact($id) 
    {
        $data = $this->getData();
        return $data['contacts'][$id] ?? null;
    }
    
    public function saveDeal($dealData) 
    {
        $data = $this->getData();
        
        if (!isset($dealData['id'])) {
            $ids = array_keys($data['deals']);
            $dealData['id'] = $ids ? max($ids) + 1 : 1;
        }
        
        $data['deals'][$dealData['id']] = $dealData;
        $this->saveData($data);
        return $dealData['id'];
    }
    
    public function saveContact($contactData) 
    {
        $data = $this->getData();
        
        if (!isset($contactData['id'])) {
            $ids = array_keys($data['contacts']);
            $contactData['id'] = $ids ? max($ids) + 1 : 1;
        }
        
        $data['contacts'][$contactData['id']] = $contactData;
        $this->saveData($data);
        return $contactData['id'];
    }
    
    public function deleteDeal($id) 
    {
        $data = $this->getData();
        unset($data['deals'][$id]);
        $this->saveData($data);
    }
    
    public function deleteContact($id) 
    {
        $data = $this->getData();
        unset($data['contacts'][$id]);
        $this->saveData($data);
    }
}