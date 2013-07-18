<?php
class Lightning_Filter
{
    private $_conditions = array();
    
    public function __construct($key, $value, $operator = 'eq')
    {
        $this->addCondition($key, $value, $operator, $type);
    }
    
    public function ($key, $value, $operator = 'eq')
    {
        
    }
    
    public function addCondition($key, $value, $operator = 'eq', $type = 'and')
    {
        $this->_conditions[] = array(
            'key'       => $key,
            'value'     => $value,
            'operator'  => $operator
        );
    }
}