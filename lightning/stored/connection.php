<?php
class Lightning_Stored_Connection
{
    protected $source;
    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $adapter_class_file;
    protected $adapter_class;
    
    public function __construct($host, $username, $password, $database, $adapter_class_file, $adapter_class) 
    {
        $this->host                 = $host;
        $this->username             = $username;
        $this->password             = $password;
        $this->database             = $database;
        $this->adapter_class_file   = $adapter_class_file;
        $this->adapter_class        = $adapter_class;
    }
    
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function newAdapter()
    {
        require_once $this->adapter_class_file;
        return new $this->adapter_class($this);
    }
    
    public function getHost()
    {
        return $this->host;
    }
    
    public function getUserName()
    {
        return $this->username;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getDatabase()
    {
        return $this->database;
    }
}

