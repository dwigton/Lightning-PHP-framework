<?php

class Lightning_Collection implements Iterator
{
    protected $item_type           = "Lightning_Model";
    protected $items               = array();
    protected $filters             = array();
    protected $filter_operations   = array();
    protected $keys                = array();
    protected $collections         = array();
    protected $collection_joins    = array();
    
    protected $filtered    = true;
    protected $flattened   = true;
    protected $flatten_running = false;
    protected $keys_set = false;

    const JOIN_OUTER    = 'outer';
    const JOIN_INNER    = 'inner';
    const JOIN_LEFT     = 'left';
    const JOIN_RIGHT    = 'right';
    
    public function setItemType($type)
    {
        $this->item_type = $type;
        return $this;
    }
    
    public function getItemType()
    {
        return $this->item_type;
    }
    
    public function addItem(Lightning_Model $item)
    {
        if (!$this->keys_set) {
            foreach ($item->getData() as $key => $data) {
                $this->keys[$key] = $key;
            }
        }
        $this->items[] = $item;
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
        return new $this->item_type();
    }
    
    public function count()
    {
        $this->flatten();
        return count($this->items);
    }
    
    public function getItemKeys()
    {
        $this->flatten();
        return $this->keys;
    }
    
    public function setItemKeys($key_array)
    {
        $this->keys = $key_array;
        $this->keys_set = true;
        return $this;
    }
    
    public function getItems()
    {
        $this->flatten();
        return $this->items;
    }
    
    public function getItem($index)
    {
        $this->flatten();
        if(array_key_exists($index, $this->items)){
            return $this->items[$index];
        }else{
            return $this->getNewItem();
        }
    }
    
    public function getValues($key)
    {
        $output = array();
        foreach ($this->items as $item) {
            $output[] = $item->getValue($key);
        }
        return $output;
    }
    
    public function clear()
    {
        $this->items = array();
    }
    
    
    // Data manipulation functions
    
    public function addFilter($key, $comp, $value, $is_value_field = false)
    {
        $this->filters[] = array(
            'key'               => $key,
            'comp'              => $comp,
            'value'             => $value,
            'is_value_field'    => $is_value_field
        );
        
        $this->filtered = false;
        
        return $this;
    }
    
    public function operate($type)
    {
        end($this->filters);
        $this->filter_operations[] = array('index' => key($this->filters), 'type' => $type);
        return $this;
    }
    
    /*
     *  Applies Filters in RPN fashion
     */
    public function applyFilters()
    {
        if (!$this->filtered) {
            $result = array();
            foreach ($this->items as $item) {
                reset($this->filter_operations);
                $stack = array();
                foreach ($this->filters as $index => $filter) {
                    array_push($stack, self::evaluateFilter($filter, $item));
                    $operation = current($this->filter_operations);

                    while ($operation && $operation['index'] == $index) {

                        switch ($operation['type']) {
                            case 'and':
                                if (count($stack) < 2) {
                                    throw new Exception("Too few items in RPN stack");
                                }
                                $arg1 = array_pop($stack);
                                $arg2 = array_pop($stack);
                                array_push($stack, $arg1 && $arg2);
                                break;
                            case 'or':
                                if (count($stack) < 2) {
                                    throw new Exception("Too few items in RPN stack");
                                }
                                $arg1 = array_pop($stack);
                                $arg2 = array_pop($stack);
                                array_push($stack, $arg1 || $arg2);
                                break;
                            case 'not':
                                if (count($stack) < 1) {
                                    throw new Exception("Too few items in RPN stack");
                                }
                                array_push($stack, ! array_pop($stack));
                                break;
                            default:
                                break;
                        }
                        $operation = next($this->filter_operations);
                    }

                    if (!$operation && count($stack) > 1) {
                        throw new Exception('Too few filter operations');
                    }
                }
                if (next($this->filter_operations)) {
                    throw new Exception('Too many filter operations');
                }
                if (array_pop($stack)) {
                    $result[] = $item;
                }
            }

            $this->items = $result;
            $this->filtered = true;
        }
        return $this;
    }
    
    // Should this be a filter class method?
    public static function evaluateFilter($filter, $item)
    {
        if ($item->hasKey($filter['key'])) {
            $arg = $filter['is_value_field'] ? $item->getValue($filter['value']) : $filter['value'];
            return self::compareValues($item->getValue($filter['key']), $filter['comp'], $arg);
        } else {
            return false;
        }
    }
    
    // Should this be a filter class method?
    public static function compareValues($left, $comp, $right)
    {
        switch ($comp) {
            case 'eq':
                return $left == $right;
                break;
            case 'neq':
                return $left != $right;
                break;
            case 'gt':
                return $left >  $right;
                break;
            case 'gteq':
                return $left >= $right;
                break;
            case 'lt':
                return $left <  $right;
                break;
            case 'lteq':
                return $left <= $right;
                break;
            case 'in':
                return in_array($left, $right);
                break;
            case 'like':
                return preg_match($right, $left);
                break;
            default:
                throw new Exception('Comparison operator not recognized');
        }
    }


    public function addCollection($collection)
    {
        $this->collections[] = $collection;
        $this->flattened = false;
        return $this;
    }
    
    public function join($parent_key, $child_key, $type = self::JOIN_INNER)
    {
        end($this->collections);
        $this->collection_joins[] = array(
                    'index'         => key($this->collections),
                    'type'          => $type,
                    'parent_key'    => $parent_key,
                    'child_key'     => $child_key
                );
        return $this;
    }
    
    public function is_flattened()
    {
        return $this->flattened;
    }
    
    public function flatten()
    {
        if (!$this->flattened && !$this->flatten_running) {
            $this->flatten_running = true;
            $stack = array($this);
            reset($this->collection_joins);
            foreach ($this->collections as $index => $collection) {
                $stack[] = $collection;
                $join = current($this->collection_joins);
                while ($join && $join['index'] == $index) {
                    
                    if (count($stack) < 2) {
                        throw new Exception('Too few collections in stack to perform join');
                    }
                    
                    $coll2 = array_pop($stack);
                    $coll1 = array_pop($stack);
                    
                    $stack[] = $this->collectionJoin(
                        $coll1,
                        $coll2,
                        $join['parent_key'],
                        $join['child_key'],
                        $join['type']
                    );
                    
                    $join = next($this->collection_joins);
                }
            }
           
            $this->flattened = true;
            $this->flatten_running = false;
        }
        
        if(!$this->flatten_running){
            $this->applyFilters();
        }
        
        return $this;
    }
    
    public static function indexCollection($collection, $key)
    {
        $result = array();
        
        foreach ($collection as $row => $item) {
            if (array_key_exists($item->getValue($key), $result)) {
                $result[$item->getValue($key)]['items'][] = $row;
            } else {
                $result[$item->getValue($key)] = array('items'=>array($row), 'used'=>false);
            }
        }
        
        return $result;
    }
    
    public function collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        $result_collection_class    = get_class($left_collection);
        $result                     = new $result_collection_class();
        
        $loop_is_left = $left_collection->count() >= $right_collection->count();
        
        $loop_key           = $loop_is_left ? $left_key         : $right_key;
        $index_key          = $loop_is_left ? $right_key        : $left_key;
        $loop_collection    = $loop_is_left ? $left_collection  : $right_collection;
        $indexed_collection = $loop_is_left ? $right_collection : $left_collection;
        $null_left          =    ($loop_is_left && $type == self::JOIN_LEFT)
                              || (!$loop_is_left && $type == self::JOIN_RIGHT)
                              || $type == self::JOIN_OUTER;
        $null_right         =    ($loop_is_left && $type == self::JOIN_RIGHT)
                              || (!$loop_is_left && $type == self::JOIN_LEFT)
                              || $type == self::JOIN_OUTER;
        
        $indexed_set = self::indexCollection($indexed_collection, $index_key);
        
        foreach ($loop_collection as $loop_item) {
            if (array_key_exists($loop_item->getValue($loop_key), $indexed_set)) {
                foreach ($indexed_set[$loop_item->getValue($loop_key)]['items'] as $indexed_item) {
                    $indexed_set[$loop_item->getValue($loop_key)]['used'] = true;
                    if ($loop_is_left) {
                        $result->addNewItem(
                            array_merge(
                                $right_collection->getItem($indexed_item)->getData(),
                                $loop_item->getData()
                            )
                        );
                    } else {
                        $result->addNewItem(
                            array_merge(
                                $loop_item->getData(),
                                $left_collection->getItem($indexed_item)->getData()
                            )
                        );
                    }
                }
            } else {
                if ($null_left) {
                    $result->addNewItem($loop_item->getData());
                }
            }
        }
                        
        if ($null_right) {
            foreach ($indexed_set as $index) {
                if (!$index['used']) {
                    foreach ($index['items'] as $item) {
                        $result->addNewItem($indexed_collection->getItem($item)->getData());
                    }
                }
            }
        }
        
        $left_collection->items = $result->getItems();
        if (!$left_collection->keys_set) {
            $left_collection->keys = $result->getItemKeys();
        }
        
        return $left_collection;
    }

    // Iterator interface methods
    
    public function rewind()
    {
        $this->flatten();
             
        reset($this->items);
    }
  
    public function current()
    {
        return current($this->items);
    }
  
    public function key()
    {
        return key($this->items);
    }
  
    public function next()
    {
        return next($this->items);
    }
  
    public function valid()
    {
        $key = key($this->items);
        $var = ($key !== null && $key !== false);
        return $var;
    }
}
