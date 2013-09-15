<?php
class Lightning_Mysql_Collection extends Lightning_Stored_Collection
{
    public function load()
    {
        return false;
    }
    
    protected function getDefaultItemType() {
        return 'Lightning_Mysql_Model';
    }

    public function save()
    {
        return false;
    }
    
    public function delete()
    {
        return false;
    }
    
    public function collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        return $this->getAdapter()->collectionJoin($this, $right_collection, $left_key, $right_key, $type);
    }
    
    public function applyFilters() {
        parent::applyFilters();
    }
}