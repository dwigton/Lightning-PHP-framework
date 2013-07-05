<?php
class Lightning_Stored_Model extends Lightning_Model
{
    protected $_source;
    
    public function __construct() {
        $this->_collection_type = 'Lightning_Stored_Collection';
        //$this->_adapter = new Li
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
    
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
    }
    
    public function getAdapter()
    {
        return $this->_adapter;
    }
    
    
}