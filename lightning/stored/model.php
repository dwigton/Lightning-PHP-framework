<?php
class Lightning_Stored_Model extends Lightning_Model
{
    public function load($key, $value)
    {
        $item = $this->getCollection()
                ->addFilter($key, 'eq', $value)
                ->getItem(0);
        $this->setData($item->getData());
        
        return $this;
    }
    
    public function save()
    {
        $this->getAdapter()->saveModel($this);
    }
    
    public function delete()
    {
        $this->getAdapter()->deleteModel($this);
    }
}