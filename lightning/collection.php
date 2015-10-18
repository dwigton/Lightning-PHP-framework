<?php

class Lightning_Collection implements Iterator
{
    protected $collection_name     = 'default';
    protected $flattened           = true;
    public    $keys_set            = false;
    
    protected $adapter;
    protected $source;
    protected $items               = array();
    protected $filters             = array();
    protected $filter_operations   = array();
    protected $keys                = array();
    protected $collections         = array();
    protected $collection_joins    = array();
    protected $order               = array();
    protected $limit               = array('amount' => 0, 'start' => 0);
    
    
    public function __construct(Lightning_Adapter $adapter = null)
    {
        if ( $adapter === null ) {
            $this->adapter = new Lightning_Adapter();
        } else {
            $this->adapter = $adapter;
        }
    }
    
    public function setAdapter(Lightning_Adapter $adapter)
    {
        $adapter->setCollection($this);
        $this->adapter = $adapter;
    }
    
    /**
    * Return collection Adapter
    *
    * @return Lightning_Adapter
    */
    public function getAdapter()
    {
        return $this->adapter;
    }
    
    public function setSource($source)
    {
        $this->source = $source;
        if( $this->collection_name == 'default' ){
            $this->collection_name = $source;
        }
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function setCollectionName($name)
    {
        $this->collection_name = $name;
        return $this;
    }
    
    public function getCollectionName()
    {
        return $this->collection_name;
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
        return $this->adapter->getNewModel($this->source);
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
    
    public function setItems($items)
    {
        $this->items = $items;
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
        $this->flatten();
        $output = array();
        foreach ($this->items as $item) {
            $output[] = $item->getValue($key);
        }
        return $output;
    }
    
    public function clear()
    {
        $this->items = array();
        $this->keys  = array();
    }
    
    public function reset()
    {
        $this->filters             = array();
        $this->filter_operations   = array();
        $this->collections         = array();
        $this->collection_joins    = array();
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
        
        $this->flattened = false;
        
        return $this;
    }
    
    public function getFilters()
    {
        return $this->filters;
    }
    
    public function operate($type)
    {
        if(count($this->filters) > 1 || (count($this->filters) > 1 && $type == 'not')){
            end($this->filters);
            $this->filter_operations[] = array('index' => key($this->filters), 'type' => $type);
        }
        return $this;
    }
    
    public function getFilterOperations()
    {
        return $this->filter_operations;
    }
    
    public function flatten()
    {
        if (!$this->flattened) {
            $this->beforeFlatten();
            $this->flattened = true;
            $this->getAdapter()->flatten($this);
        }
        
        return $this;
    }
    
    protected function beforeFlatten()
    {
        Lightning_Event::raiseEvent('Before_Collection_Flatten', $this);
    }

    public function addCollection($collection)
    {
        $this->collections[] = $collection;
        return $this;
    }
    
    public function getCollections()
    {
        return $this->collections;
    }
    
    public function join($parent_key, $child_key, $type = Lightning_Adapter::JOIN_INNER)
    {
        end($this->collections);
        $this->collection_joins[] = array(
                    'index'         => key($this->collections),
                    'type'          => $type,
                    'parent_key'    => $parent_key,
                    'child_key'     => $child_key
                );
        $this->flattened = false;
        return $this;
    }
    
    public function getCollectionJoins()
    {
        return $this->collection_joins;
    }
    
    public function isFlattened()
    {
        return $this->flattened;
    }
    
    public function orderBy($index, $dir = 'asc')
    {
        $this->flattened = false;
        $this->order[$index] = $dir;
        return $this;
    }
   
    public function limit($amount, $start = 0)
    {
        $this->flattened = false;
        $this->limit['amount'] = $amount;
        $this->limit['start'] = $start;
        return $this;
    }
    
    public function getOrder()
    {
        return $this->order;
    }
    
    public function getLimit()
    {
        return $this->limit;
    }
    
    // Iterator interface methods
    
    public function rewind()
    {
        $this->flatten();
             
        reset($this->items);
    }
  
    public function current()
    {
        $this->flatten();
        return current($this->items);
    }
  
    public function key()
    {
        $this->flatten();
        return key($this->items);
    }
  
    public function next()
    {
        $this->flatten();
        return next($this->items);
    }
  
    public function valid()
    {
        $key = key($this->items);
        $var = ($key !== null && $key !== false);
        return $var;
    }
}
