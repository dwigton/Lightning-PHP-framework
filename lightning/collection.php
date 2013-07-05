<?php
class Lightning_Collection implements Iterator
{
    protected $_items = array();
    protected $_item_type = "Lightning_Model";
    
    public function getNewItem()
    {
        return new $this->_item_type();
    }
    
    public function setItemType(Lightning_Model $type)
    {
        $this->_item_type = $type;   
        return $this;
    }
    
    public function getItemType()
    {
        return $this->_item_type;
    }
    
    public function addItem(Lightning_Model $item)
    {
        $this->_items[] = $item;
        return $this;
    }
    
    // Iterator interface methods
    
    public function rewind()
    {
        reset($this->_items);
    }
  
    public function current()
    {
        return current($this->_items);
    }
  
    public function key() 
    {
        return key($this->_items);
    }
  
    public function next() 
    {
        return next($this->_items);
    }
  
    public function valid()
    {
        $key = key($this->_items);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}