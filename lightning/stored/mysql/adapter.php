<?php
class Lightning_Stored_Mysql_Adapter extends Lightning_Stored_Adapter
{    
    protected $select;
    protected $from;
    protected $where;
    protected $order;
    protected $limit;
    protected $symbols = array();
    protected $current_symbol;

    public function flattenCollection(Lightning_Stored_Collection $collection)
    {        
        parent::flatten();
        
        return $this;
    }
    
    public function collectionJoin(Lightning_Collection $left_collection, Lightning_Collection $right_collection, $left_key, $right_key, $type)
    {
        if ($right_collection->getAdapter() === $this) {
            $from .= strtoupper($type)." JOIN ".$right_collection->getSource()
            .this->symbol($right_collection>getSource()) 
            .' ON '.$this->symbol().'.'.$right_key.' = '.$left_key;
        } else {

        }
    }
    
    public function saveModel(Lightning_Stored_Model $model )
    {
        //$query = "INSERT INTO ".$model->getSource()." "
    }
    
    public function deleteModel( $model )
    {
        
    }

    private function symbol($table = "", $new = false)
    {
        $result = '';
        if ($table !== "") {
            $parts = explode($table, '_');
            foreach ($parts as $part) {
                $result .= strtolower($part[0]);
            }
            if (array_key_exists($result, $this->$symbols)) {
                if ($new) {
                    $result = ++$result.$this->symbols[$result]['count'];
                } else {
                    $result = $this->symbols[$result]['stack'][$this->symbols[$result]['index']]; 
                }
            } else {
                $this->symbols[$result]['count'] = 0;
            }
            $this->current_symbol = $result;
        } else {
            $result = $this->current_symbol;
        }
        return $result;
    }

}
