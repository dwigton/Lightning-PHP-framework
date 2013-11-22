<?php
class Lightning_Stored_Collection extends Lightning_Collection
{
    protected $source = 'default';
    protected $table;
    
    public function __construct()
    {
        parent::__construct();
        $this->item_type = $this->getDefaultItemType();
        $this->flattened = false;
    }
    
    protected function getDefaultItemType()
    {
        return 'Lightning_Stored_Model';
    }

    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $this->adapter = App::getDataSource($this->source)->newAdapter();
            $this->adapter->setCollection($this);
        }
        
        return $this->adapter;
    }
    
    public function getNewItem()
    {
        $item = parent::getNewItem();
        $item->setSource($this->getSource());
        $item->setTable($this->getTable());
        return $item;
    }
    
    public function setSource($source)
    {
        $this->source  = $source;
        $this->adapter = null;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function setTable($table)
    {
        $this->table = $table;
        if( $this->collection_name == 'default' ){
            $this->collection_name = $table;
        }
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    protected function beforeFlatten()
    {
        Lightning_Event::raiseEvent('Before_Stored_Collection_Flatten', $this);
    }
}