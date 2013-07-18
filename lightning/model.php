<?php
class Lightning_Model
{
    protected $_data = array();
    protected $_collection_type = "Lightning_Model";
    protected $_sub_collections = array();
    protected $_keys = array();
    
    

    public function getData()
    {
        return $this->_data;
    }
    
    public function getKey($key)
    {
        if($this->hasKey($key)){
            return $this->_data[$key];
        }else{
            return null;
        }
    }
    
    public function hasKey($key)
    {
        return array_key_exists($key, $this->_data);
    }
    
    public function setData($data)
    {
        if(is_array($data)){
            $this->_data = $data;
        }
        return $this;
    }
    
    public function setKey($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
    
    public function hasData()
    {
        return !empty($this->_data);
    }
    
    public function setCollectionType($type)
    {
        $this->_collection_type = $type;
        return $this;
    }
    
    public function getCollectionType()
    {
        return $this->_collection_type;
    }
    
    public function getCollection()
    {
        $collection = new $this->_collection_type();
        $collection->setItemType(get_class($this));
        
        return $collection;
    }
    
    
}