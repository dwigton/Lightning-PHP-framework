<?php
class Lightning_Collection implements Iterator
{
    protected $_item_type           = "Lightning_Model";
    protected $_items               = array();
    protected $_filters             = array();
    protected $_filter_operations   = array();
    protected $_keys                = array();
    protected $_collections         = array();
    
    const JOIN_OUTER        = 1;
    const JOIN_INNER        = 2;
    const JOIN_LEFT_OUTER   = 3;
    const JOIN_LEFT_INNER   = 4;
    const JOIN_RIGHT_OUTER  = 5;
    const JOIN_RIGHT_INNER  = 6;
    
    public function getNewItem()
    {
        return new $this->_item_type();
    }
    
    public function setItemType(Lightning_Model $type)
    {
        $this->_item_type = $type;   
        return $this;
    }
    
    public function getItemType()
    {
        return $this->_item_type;
    }
    
    public function addItem(Lightning_Model $item)
    {
        $this->_items[] = $item;
        return $this;
    }
    
    // Data manipulation functions
    
    public function filter($key, $comp, $value, $operator = 'and')
    {
        $this->_filters[] = array(
            'key'       => $key,
            'comp'      => $comp,
            'value'     => $value
        );
        
        $this->operate($operator);
    }
    
    public function applyFilter()
    {
        $result = array();
        foreach($this->_items as $item){
            reset($this->_filter_operations);
            $stack = array();
            foreach($this->_filters as $key => $filter){
                array_push($stack, $this->evaluateFilter($filter, $item));
                $operation = current($this->_filter_operations);
                
                while($operation && $operation['key'] == $key){
                    
                    switch($operation['type']){
                        case 'and'  : if(count($stack) < 2){throw new Exception("Too few items in RPN stack");}
                                        $arg1 = array_pop($stack);
                                        $arg2 = array_pop($stack);
                                        array_push($stack, $arg1 && $arg2); break;
                        case 'or'   : if(count($stack) < 2){throw new Exception("Too few items in RPN stack");}
                                        $arg1 = array_pop($stack);
                                        $arg2 = array_pop($stack);
                                        array_push($stack, $arg1 || $arg2); break;
                        case 'not'  : if(count($stack) < 1){throw new Exception("Too few items in RPN stack");}
                                        array_push($stack, ! array_pop($stack)); break;
                        default     : break;
                    }
                    $operation = next($this->_filter_operations);
                }
                
                if(!$operation && count($stack) > 1){
                    throw new Exception('Too few filter operations');
                }
            }
            if(next($this->_filter_operations)){
                throw new Exception('Too many filter operations');
            }
            if(array_pop($stack)){
                $result[] = $item;
            }
        }
        
        $this->_items = $result;
    }
    
    protected function evaluateFilter($filter, $item)
    {
        if($item->hasKey($filter['key'])){
            switch($filter['comp']){
                case 'eq'   : return $item->getKey($filter['key']) == $filter['value']; break; 
                case 'neq'  : return $item->getKey($filter['key']) != $filter['value']; break; 
                case 'gt'   : return $item->getKey($filter['key']) > $filter['value']; break; 
                case 'gteq' : return $item->getKey($filter['key']) >= $filter['value']; break; 
                case 'lt'   : return $item->getKey($filter['key']) < $filter['value']; break; 
                case 'lteq' : return $item->getKey($filter['key']) <= $filter['value']; break; 
                case 'in'   : return in_array($item->getKey($filter['key']), $filter['value']); break; 
                case 'like' : return preg_match($filter['value'], $item->getKey($filter['key'])); break;
            }
        }
    }
    
    public function operate($type)
    {
        end($this->_filters);
        $this->_filter_operations[] = array('key' => key($this->_filters), 'type' => $type); 
    }
    
    public function joinCollection($alias, $collection, $foreign_key_array, $condition_array, $key_array, $available_keys, $join_type = self::JOIN_OUTER)
    {
        $this->_collections[$alias] = array(
            'type'              => $join_type,
            'collection'        => $collection,
            'foreign_keys'      => $foreign_key_array,
            'conditions'        => $condition_array,
            'keys'              => $key_array,
            'available_keys'    => $available_keys
        );
    }
    
    public function flattenCollection()
    {
        if(!$this->_is_flattened){
            foreach ($this->_sub_collections as $join){
                $join['collection']->flattenCollection();
                $new_data = array();
                
                foreach($join['collection'] as $item){
                    foreach($this->_data as $data){
                        $this->_joinRow($data, $item, $join);
                    }
                }
            }
        }
        
        $this->_is_flattened = true;
    }
    
    protected function _joinRow($local_row, $foreign_row, $join)
    {
        switch($join['type']){
            case self::JOIN_OUTER       : if(true){;} break;
            case self::JOIN_INNER       : break;
            case self::JOIN_LEFT_OUTER  : break;
            case self::JOIN_LEFT_INNER  : break;
            case self::JOIN_RIGHT_OUTER : break;
            case self::JOIN_RIGHT_INNER : break;
        }
    }
    
    // Iterator interface methods
    
    public function rewind()
    {
        reset($this->_items);
    }
  
    public function current()
    {
        return current($this->_items);
    }
  
    public function key() 
    {
        return key($this->_items);
    }
  
    public function next() 
    {
        return next($this->_items);
    }
  
    public function valid()
    {
        $key = key($this->_items);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}