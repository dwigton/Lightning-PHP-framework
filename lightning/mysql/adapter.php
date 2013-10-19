<?php
class Lightning_Mysql_Adapter extends Lightning_Stored_Adapter
{
    protected $select;
    protected $from;
    protected $where;
    protected $update;
    protected $delete;
    protected $insert;
    protected $query;
    
    public function query()
    {
        
    }
    
    protected function buildQuery()
    {
        
    }
    
    public function collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        if($right_collection->getConnection()){}
    }
    
    public function loadCollection(Lightning_Stored_Collection $collection) 
    {
        
    }
    
    public function loadModel(Lightning_Stored_Model $model)
    {
        
    }
}