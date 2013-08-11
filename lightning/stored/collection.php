<?php
class Lightning_Stored_Collection extends Lightning_Collection
{
    protected $source;
    protected $adapter;
    protected $collection_index;
    
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
    
    public function setCollectionIndex($index)
    {
        $this->collection_index = index;
    }
    
    public function getCollectionIndex(){
        return $this->collection_index;
    }
    
    public function collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        return $this->getAdapter()->collectionJoin($this, $right_collection, $left_key, $right_key, $type);
    }
    
    public function applyFilters() {
        parent::applyFilters();
    }
}