<?php
class Lightning_Mysql_Model extends Lightning_Stored_Model
{
    protected function getDefaultCollectionType()
    {
        return 'Lightning_Mysql_Collection';
    }

    public function load(array $keys)
    {
        $where = ' ';
        $first = true;
        
        foreach ($keys as $key=>$value){
            if ($first) {
                $first = false;
            }else{
                $where .= ' AND ';
            }
            $where .= "$key = '$value'";
        }
        
        $query = 'SELECT * FROM ' . $this->source . $where . ' LIMIT 1';
        
        
    }
    
    public function save()
    {
        
    }
    
    public function delete()
    {
        
    }
}