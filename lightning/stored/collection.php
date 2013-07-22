<?php
class Lightning_Stored_Collection extends Lightning_Collection
{
    protected $_source;
    protected $_adapter;
    
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
    
    public function getNewItem()
    {
        $item = parent::getNewItem;
        $item->setAdapter($this->getAdapter());
        $item->setSource($this->getSource());
        return $item;
    }
    
    public function join(Lightning_Stored_Collection $collection)
    {
        
    }
    
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
    }
    
    public function getAdapter()
    {
        return $this->_adapter;
    }
    
    public function setSource($source)
    {
        $this->_source = $source;
    }
    
    public function getSource(){
        return $this->_source;
    }
    
    public function collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        return $this->getAdapter()->collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type);
    }
}