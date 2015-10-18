<?php
class Lightning_Stored_Collection extends Lightning_Collection
{    
    public function __construct($adapter = null)
    {
        parent::__construct($adapter);
        $this->flattened = false;
    }
    
    protected function beforeFlatten()
    {
        Lightning_Event::raiseEvent('Before_Stored_Collection_Flatten', $this);
    }
}