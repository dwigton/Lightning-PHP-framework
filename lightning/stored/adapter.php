<?php namespace Lightning;
abstract class Stored_Adapter extends Adapter
{
    protected $host;
    protected $username;
    protected $password;
    protected $database;
    
    public function __construct($host, $username, $password, $database) 
    {
        $this->host                 = $host;
        $this->username             = $username;
        $this->password             = $password;
        $this->database             = $database;
        
        $this->addModel('default', 'lightning/stored/model.php', 'Lightning\Stored_Model');
        $this->addCollection('default', 'lightning/stored/collection.php', 'Lightning\Stored_Collection');
    }
    
    public abstract function saveModel(Stored_Model $model);
    
    public abstract function deleteModel(Stored_Model $model);
}
