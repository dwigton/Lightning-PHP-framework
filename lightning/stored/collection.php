<?php
class Lightning_Stored_Collection extends Lightning_Model
{
    public function __construct() {
        $this->_item_type = 'Lightning_Stored_Model';
    }

    public function load()
    {
        return false;
    }
    
    public function save()
    {
        return false;
    }
    
    public function delete()
    {
        return false;
    }
    
    public function join(Lightning_Stored_Collection $collection)
    {
        
    }
}