<?php
class Lightning_Adapter
{
    protected $collection;
    
    protected static $comparison;
    
    const JOIN_OUTER    = 'outer';
    const JOIN_INNER    = 'inner';
    const JOIN_LEFT     = 'left';
    const JOIN_RIGHT    = 'right';
    
    public function setCollection(Lightning_Collection $collection) {
        $this->collection = $collection;
    }
    
    public function getCollection(){
        return $this->collection;
    }
    
    public function applyFilters()
    {
        $operations = $this->collection->getFilterOperations();
        $result = array();
        $filtered = false;
        
        foreach ($this->collection->getItems() as $item) {
            reset($operations);
            $stack = array();
            foreach ($this->collection->getFilters() as $index => $filter) {
                
                $filtered = true;
                
                array_push($stack, self::evaluateFilter($filter, $item));
                $operation = current($operations);

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
                    $operation = next($operations);
                }

                if (!$operation && count($stack) > 1) {
                    throw new Exception('Too few filter operations');
                }
            }
            if (next($operations)) {
                throw new Exception('Too many filter operations');
            }
            if (array_pop($stack)) {
                $result[] = $item;
            }
        }
        
        if($filtered){
            $this->collection->setItems($result);
        }

        return $this;
    }
    
    public function order()
    {
        self::$comparison = $this->collection->getOrder();
        $items = $this->collection->getItems();
        usort($items, array('Lightning_Adapter', 'itemCompare'));
        $this->collection->setItems($items);
    }
    
    public function limit()
    {
        $limit = $this->collection->getLimit();
        if ($limit['amount'] !== 0) {
            $items = $this->collection->getItems();
            $this->collection->setItems(array_slice($items, $limit['start'], $limit['amount']));
        }
    }
    
    public static function itemCompare($item1, $item2){
        $order = self::$comparison;
        
        foreach ($order as $index=>$dir) {
            $val1 = $item1->getValue($index);
            $val2 = $item2->getValue($index);
            if ($val1 != $val2) {
                if (is_numeric($val1) && is_numeric($val2)) {
                    return $dir == 'asc' ? $item1->getValue($index) - $item2->getValue($index)
                            : $item2->getValue($index) - $item1->getValue($index);
                } else {
                    return $dir == 'asc' ? strcmp($val1, $val2) : strcmp($val2, $val1);
                }
            }
        }
        return 0;
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
    
    public function flatten()
    {
        $stack = array($this->collection);
        $joins = $this->collection->getCollectionJoins();
        reset($joins);
        foreach ($this->collection->getCollections() as $index => $collection) {
            $stack[] = $collection;
            $join = current($joins);
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

                $join = next($joins);
            }
        }
        
        $this->applyFilters();
        $this->order();
        $this->limit();
        $this->collection->reset();
        
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
                            self::mergeData(
                                $loop_item->getData(),
                                $right_collection->getItem($indexed_item)->getData(),
                                $right_collection->getCollectionName()
                            )
                        );
                    } else {
                        $result->addNewItem(
                            self::mergeData(
                                $left_collection->getItem($indexed_item)->getData(),
                                $loop_item->getData(),
                                $right_collection->getCollectionName()
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
        
        $left_collection->setItems($result->getItems());
        if (!$left_collection->keys_set) {
            $left_collection->setItemKeys($result->getItemKeys());
        }
        
        return $left_collection;
    }
    
    protected static function mergeData($parent, $child, $collection_name)
    {
        $duplicates = array_intersect_key($child, $parent);
        $output = array_merge($child, $parent);
        foreach($duplicates as $key=>$value){
            $output[$collection_name.'.'.$key] = $value;
        }
        return $output;
    }
}