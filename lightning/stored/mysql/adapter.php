<?php
class Lightning_Stored_Mysql_Adapter extends Lightning_Stored_Adapter
{    
    protected $select;
    protected $from;
    protected $where;
    protected $order;
    protected $limit;

    public function flatten()
    {        
        parent::flatten();
        
        return $this;
    }
    
    public function collectionJoin($left_collection, $right_collection, $left_key, $right_key, $type)
    {
        
    }
    
    public function saveModel( $model )
    {
        $file = $this->connection->getDatabase()."/".$model->getTable().".xml";
        $xml = simplexml_load_file($file);
        $id = (string)$xml->id;
        
        if($model->getValue($id) == null){
            $model->setValue($id, (int)$xml->increment);
            $xml->increment = (int)$xml->increment + 1;
        }
        
        $data = new SimpleXMLElement('<row></row>');
        
        foreach($xml->columns->column as $column){
            $data->addChild((string)$column, $model->getValue((string)$column));
        }
        
        $duplicate = null;
        
        foreach($xml->rows->row as $row){
            if((int)$row->$id == $model->getValue($id)){
                $duplicate = $row;
            }
        }
        
        unset($duplicate[0][0]);
        
        $this->sxml_append($xml->rows, $data);
        
        $xml->asXml($file);
        
        $this->formatXml($file);
    }
    
    public function deleteModel( $model )
    {
        $file = $this->connection->getDatabase()."/".$model->getTable().".xml";
        $xml = simplexml_load_file($file);
        $id = (string)$xml->id;
        
        if($model->getValue($id) != null){        
            $target = null;

            foreach($xml->rows->row as $row){
                if((int)$row->$id == $model->getValue($id)){
                    $target = $row;
                }
            }

            unset($target[0][0]);

            $xml->asXml($file);

            $this->formatXml($file);
        }
        $model->setData(array());
    }
}