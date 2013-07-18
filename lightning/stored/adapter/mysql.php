<?php
class Lightning_Stored_Adapter_Mysql extends Lightning_Stored_Adapter_Abstract
{
    private $_username;
    private $_schema;
    private $_password;
    private $_host;
    
    public function __construct($host, $schema, $user, $password) {
        $this->_host        = $host;
        $this->_schema      = $schema;
        $this->_username    = $user;
        $this->_password    = $password;
    }
}