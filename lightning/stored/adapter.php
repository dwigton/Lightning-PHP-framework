<?php
class Lightning_Stored_Adapter extends Lightning_Adapter
{
    protected $connection;
    
    public function __construct($connection)
    {
        $this->connection = $connection;
    }
    
    public function flatten()
    {
        return parent::flatten();
    }
    
    protected function getConnection()
    {
        return $this->connection;
    }
    
    public function getNewModel( $table )
    {
        return $this->getNewCollection($table)->getNewItem();
    }
    
    public function getNewCollection( $table )
    {
        require_once App::getCollectionClassFile($this->connection->getSource(),$table);
        $file = App::getCollectionClass($this->connection->getSource(),$table);
        $collection = new $file();
        $this->setCollection($collection);
        $collection->setAdapter($this);
        $collection->setTable($table);
        
        $model_class = App::getModelClass($this->connection->getSource(),$table);
        if($model_class){
            require_once App::getModelClassFile($this->connection->getSource(),$table);
            $collection->setItemType($model_class);
        }
        return $collection;
    }
}