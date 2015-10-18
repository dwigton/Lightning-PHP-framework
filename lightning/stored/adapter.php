<?php
abstract class Lightning_Stored_Adapter extends Lightning_Adapter
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
        
        $this->addModel('default', 'lightning/stored/model.php', 'Lightning_Stored_Model');
        $this->addCollection('default', 'lightning/stored/collection.php', 'Lightning_Stored_Collection');
    }
    
    public abstract function saveModel(Lightning_Stored_Model $model);
    
    public abstract function deleteModel(Lightning_Stored_Model $model);
}