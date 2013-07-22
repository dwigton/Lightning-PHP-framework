<?php
class Lightning_Stored_Model extends Lightning_Model
{
    protected $source;
    protected $adapter;
    
    public function __construct() {
        $this->setCollectionType('Lightning_Stored_Collection');
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
        $this->adapter = $adapter;
    }
    
    public function getAdapter()
    {
        return $this->adapter;
    }
    
    public function setSource($source)
    {
        $this->source = $source;
    }
    
    public function getSource(){
        return $this->source;
    }
    
    public function getCollection()
    {
        $collection = parent::getCollection();
        $collection->setAdapter($this->getAdapter());
        $collection->setSource($this->getSource());
        
        return $collection;
    }
}