<?php
class Lightning_Stored_Connection
{
    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $adapter_class;
    
    public function __construct($host, $username, $password, $database, $adapter_class) 
    {
        $this->credentials = $credentials;
    }
    
    public function newAdapter()
    {
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