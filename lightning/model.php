<?php
class Lightning_Model
{
    protected $data = array();
    protected $collection_type = "Lightning_Model";
    protected $keys = array();
    protected $adapter;
    
    public function __construct() {
        $this->adapter = new Lightning_Adapter;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function getValue($key)
    {
        if ($this->hasKey($key)) {
            return $this->data[$key];
        } else {
            return null;
        }
    }
    
    public function hasKey($key)
    {
        return array_key_exists($key, $this->data);
    }
    
    public function setData($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        }
        return $this;
    }
    
    public function setValue($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function hasData()
    {
        return !empty($this->data);
    }
    
    public function setCollectionType($type)
    {
        $this->collection_type = $type;
        return $this;
    }
    
    public function getCollectionType()
    {
        return $this->collection_type;
    }
    
    public function getCollection()
    {
        $collection = new $this->collection_type();
        $collection->setItemType(get_class($this));
        
        return $collection;
    }
    
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }
    
    public function getAdapter()
    {
        return $this->adapter;
    }
}
