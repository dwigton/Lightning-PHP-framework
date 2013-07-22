<?php
class Lightning_Filter
{
    private $conditions = array();
    
    public function __construct($key, $value, $operator = 'eq')
    {
        $this->addCondition($key, $value, $operator, $type);
    }
    
    public function addCondition($key, $value, $operator = 'eq', $type = 'and')
    {
        $this->conditions[] = array(
            'key'       => $key,
            'value'     => $value,
            'operator'  => $operator
        );
    }
}
