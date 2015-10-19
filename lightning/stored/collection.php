<?php namespace Lightning;

class Stored_Collection extends Collection
{    
    public function __construct($adapter = null)
    {
        parent::__construct($adapter);
        $this->flattened = false;
    }
    
    protected function beforeFlatten()
    {
        Event::raiseEvent('Before_Stored_Collection_Flatten', $this);
    }
}
