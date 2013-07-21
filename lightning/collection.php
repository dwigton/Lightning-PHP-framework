<?php
class Lightning_Collection implements Iterator
{
    protected $_item_type           = "Lightning_Model";
    protected $_items               = array();
    protected $_filters             = array();
    protected $_filter_operations   = array();
    protected $_keys                = array();
    protected $_collections         = array();
    protected $_collection_joins    = array();
    
    protected $_filtered    = true;
    protected $_flattened   = true;
    protected $_flatten_running = false;
    protected $_keys_set = false;

    const JOIN_OUTER    = 'outer';
    const JOIN_INNER    = 'inner';
    const JOIN_LEFT     = 'left';
    const JOIN_RIGHT    = 'right';
    
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
        if(!$this->_keys_set){
            foreach ($item->getData() as $key=>$data){
                $this->_keys[$key] = $key;
            }
        }
        $this->_items[] = $item;
        return $this;
    }
    
    public function addNewItem($data)
    {
        $item = $this->getNewItem();
        $item->setData($data);
        $this->addItem($item);
        return $this;
    }
    
    public function getNewItem()
    {
        return new $this->_item_type();
    }
    
    public function count()
    {
        $this->flattenCollection();
        return count($this->_items);
    }
    
    public function getItemKeys()
    {
        $this->flattenCollection();
        return $this->_keys;
    }
    
    public function setItemKeys($key_array){
        $this->_keys = $key_array;
        $this->_keys_set = true;
        return $this;
    }
    
    public function getItems()
    {
        $this->flattenCollection();
        return $this->_items;
    }
    
    public function getItem($index)
    {
        $this->flattenCollection();
        return $this->_items[$index];
    }
    
    // Data manipulation functions
    
    public function addFilter($key, $comp, $value, $is_value_field = false)
    {        
        $this->_filters[] = array(
            'key'               => $key,
            'comp'              => $comp,
            'value'             => $value,
            'is_value_field'    => $is_value_field
        );
        
        $this->_filtered = false;
        
        return $this;
    }
    
    public function operate($type)
    {
        end($this->_filters);
        $this->_filter_operations[] = array('index' => key($this->_filters), 'type' => $type); 
        return $this;
    }
    
    /*
     *  Applies Filters in RPN fashion
     */
    public function applyFilters()
    {
        if(!$this->_filtered){
            $result = array();
            foreach($this->_items as $item){
                reset($this->_filter_operations);
                $stack = array();
                foreach($this->_filters as $index => $filter){
                    array_push($stack, self::evaluateFilter($filter, $item));
                    $operation = current($this->_filter_operations);

                    while($operation && $operation['index'] == $index){

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
            $this->_filtered = true;
        }
        return $this;
    }
    
    public static function evaluateFilter($filter, $item)
    {
        if($item->hasKey($filter['key'])){
            $arg = $filter['is_value_field'] ? $item->getValue($filter['value']) : $filter['value'];
            return self::compareValues($item->getValue($filter['key']), $filter['comp'], $arg);
        }else{
            return false;
        }
    }
    
    public static function compareValues($left, $comp, $right){
        switch($comp){
            case 'eq'   : return $left == $right; break; 
            case 'neq'  : return $left != $right; break; 
            case 'gt'   : return $left >  $right; break; 
            case 'gteq' : return $left >= $right; break; 
            case 'lt'   : return $left <  $right; break; 
            case 'lteq' : return $left <= $right; break; 
            case 'in'   : return in_array($left, $right); break; 
            case 'like' : return preg_match($right, $left); break;
            default: throw new Exception('Comparison operator not recognized');
        }
    }


    public function addCollection($collection)
    {
        $this->_collections[] = $collection;
        $this->_flattened = false;
        return $this;
    }
    
    public function join($parent_key, $child_key, $type = self::JOIN_INNER)
    {
        end($this->_collections);
        $this->_collection_joins[] = array(
                    'index'         => key($this->_collections),
                    'type'          => $type,
                    'parent_key'    => $parent_key,
                    'child_key'     => $child_key
                );
        return $this;
    }
    
    public function flattenCollection()
    {
        if(!$this->_flattened && !$this->_flatten_running){
            $this->_flatten_running = true;
            $stack = array($this);
            reset($this->_collection_joins);
            foreach ($this->_collections as $index=>$collection){
                $stack[] = $collection;
                $join = current($this->_collection_joins);
                while($join && $join['index'] == $index){
                    
                    if(count($stack) < 2){ throw new Exception('Too few collections in stack to perform join'); }
                    $coll2 = array_pop($stack);
                    $coll1 = array_pop($stack);
                    
                    $stack[] = self::generalJoin($coll1, $coll2, $join['parent_key'], $join['child_key'], $join['type']);
                    
                    $join = next($this->_collection_joins);
                }
            }
            $new = array_pop($stack);
            $this->_items = $new->getItems();
            if(!$this->_keys_set){
                $this->_keys = $new->getItemKeys();
            }            
            $this->_flattened = true;
            $this->_flatten_running = false;
            $this->applyFilters();
        }
        
        return $this;
    }
    
    public static function indexCollection($collection, $key)
    {
        $result = array();
        
        foreach($collection as $row=>$item){
            if(array_key_exists($item->getValue($key), $result)){
                $result[$item->getValue($key)]['items'][] = $row;
            }else{
                $result[$item->getValue($key)] = array('items'=>array($row), 'used'=>false);
            }
        }
        
        return $result;
    }
    
    public static function generalJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        $result_collection_class    = get_class($left_collection);
        $result                     = new $result_collection_class();
        
        $loop_is_left = $left_collection->count() >= $right_collection->count();
        
        $loop_key           = $loop_is_left ? $left_key         : $right_key;
        $index_key          = $loop_is_left ? $right_key        : $left_key;
        $loop_collection    = $loop_is_left ? $left_collection  : $right_collection;
        $indexed_collection = $loop_is_left ? $right_collection : $left_collection;
        $null_left          = ($loop_is_left && $type == self::JOIN_LEFT)  || (!$loop_is_left && $type == self::JOIN_RIGHT) || $type == self::JOIN_OUTER;
        $null_right         = ($loop_is_left && $type == self::JOIN_RIGHT) || (!$loop_is_left && $type == self::JOIN_LEFT)  || $type == self::JOIN_OUTER;
        
        $indexed_set = self::indexCollection($indexed_collection, $index_key);
        
        foreach($loop_collection as $loop_item){
            if(array_key_exists($loop_item->getValue($loop_key), $indexed_set)){
                foreach($indexed_set[$loop_item->getValue($loop_key)]['items'] as $indexed_item){
                    $indexed_set[$loop_item->getValue($loop_key)]['used'] = true;
                    if($loop_is_left){
                        $result->addNewItem(array_merge($right_collection->getItem($indexed_item)->getData(), $loop_item->getData()));
                    }else{
                        $result->addNewItem(array_merge($loop_item->getData(), $left_collection->getItem($indexed_item)->getData()));
                    }
                }
            }else{
                if($null_left){
                    $result->addNewItem($loop_item->getData());
                }
            }
        }
                        
        if($null_right){
            foreach ($indexed_set as $index){
                if(!$index['used']){
                    foreach($index['items'] as $item){
                        $result->addNewItem($indexed_collection->getItem($item)->getData());
                    }
                }
            }
        }
        
        return $result;
    }

    // Iterator interface methods
    
    public function rewind()
    {
        $this->flattenCollection();
             
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
