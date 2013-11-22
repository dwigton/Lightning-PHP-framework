<?php
class Lightning_Stored_Model extends Lightning_Model
{
    protected $source = 'default';
    protected $adapter;
    protected $table;
    
    public function __construct() {
        $this->collection_type = $this->getDefaultCollectionType();
    }
    
    protected function getDefaultCollectionType()
    {
        return 'Lightning_Stored_Collection';
    }

    public function load($key, $value)
    {
        $item = $this->getCollection()
                ->addFilter($key, 'eq', $value)
                ->getItem(0);
        $this->setData($item->getData());
        
        return $this;
    }
    
    public function save()
    {
        $this->getAdapter()->saveModel($this);
    }
    
    public function delete()
    {
        $this->getAdapter()->deleteModel($this);
    }
    
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