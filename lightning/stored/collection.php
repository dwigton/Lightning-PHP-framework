<?php
abstract class Lightning_Stored_Collection extends Lightning_Collection
{
    protected $source = 'default';
    protected $adapter;
    protected $table;
    
    public function __construct()
    {
        $this->_item_type = $this->getDefaultItemType();
    }
    
    abstract protected function getDefaultItemType();

    abstract public function load();
    
    abstract public function save();
    
    abstract public function delete();
    
    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $this->adapter = App::getDataSource($this->source)->newAdapter();
        }
        
        return $this->adapter;
    }
    
    public function getNewItem()
    {
        $item = parent::getNewItem;
        $item->setSource($this->getSource());
        $item->setTable($this->getTable());
        return $item;
    }
    
    public function setSource($source)
    {
        $this->source = $source;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function setTable($table)
    {
        $this->table = $table;
    }
    
    public function getTable()
    {
        if (is_null($this->table)){
            throw new Exception($message, $code, $previous)
        }
        return $this->table;
    }
}