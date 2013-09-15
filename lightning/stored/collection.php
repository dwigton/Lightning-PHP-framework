<?php
abstract class Lightning_Stored_Collection extends Lightning_Collection
{
    protected $source;
    
    public function __construct() {
        $this->_item_type = $this->getDefaultItemType();
    }
    
    abstract protected function getDefaultItemType();

    abstract public function load();
    
    abstract public function save();
    
    abstract public function delete();
    
    public function getNewItem()
    {
        $item = parent::getNewItem;
        $item->setSource($this->getSource());
        return $item;
    }
    
    public function setSource($source)
    {
        $this->source = $source;
    }
    
    public function getSource(){
        return $this->source;
    }
}