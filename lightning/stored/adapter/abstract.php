<?php
abstract class Lightning_Stored_Adapter_Abstract
{
    public abstract function open();
    
    public abstract function close();
    
    public abstract function addRecord($data);
    
    public abstract function updateRecord($conditions, $data);
    
    public abstract function deleteRecord($conditions);
    
    public abstract function addBlock();
    
    public abstract function updateBlock();
    
    public abstract function deleteBlock();

    public abstract function collectionJoin($collection, $right_collection, $left_key, $right_key, $type);
}