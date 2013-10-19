<?php
abstract class Lightning_Stored_Adapter
{
    protected $connection;
    
    public function __construct($connection)
    {
        $this->connection = $connection;
    }
    
    abstract public function loadCollection(Lightning_Stored_Collection $collection);
    
    abstract public function loadModel(Lightning_Stored_Model $model);
    
    //abstract protected function 
    
    public function collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        // Verify that the two collections use the same data source.
        if($right_collection instanceof Lightning_Stored_Collection && $right_collection->getSource() == $left_collection->getSource())
    }
}