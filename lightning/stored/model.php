<?php
abstract class Lightning_Stored_Model extends Lightning_Model
{
    protected $source = 'default';
    protected $adapter;
    protected $table;
    
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
    
    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $this->adapter = App::getDataSource($this->source)->newAdapter();
        }
        
        return $this->adapter;
    }
    
    public function setTable($table)
    {
        $this->table = $table;
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    public function getCollection()
    {
        $collection = parent::getCollection();
        $collection->setSource($this->getSource());
        $collection->setTable($this->getTable());
        return $collection;
    }
}