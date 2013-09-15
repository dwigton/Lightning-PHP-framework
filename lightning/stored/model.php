<?php
abstract class Lightning_Stored_Model extends Lightning_Model
{
    protected $source;
    
    public function __construct() {
        $this->_item_type = $this->getDefaultCollectionType();
    }
    
    abstract protected function getDefaultCollectionType();

    abstract public function load(array $keys);
    
    abstract public function save();
    
    abstract public function delete();
    
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
        $collection->setSource($this->getSource());
        return $collection;
    }
}