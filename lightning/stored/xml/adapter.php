<?php
class Lightning_Stored_Xml_Adapter extends Lightning_Stored_Adapter
{    
    public function flattenCollection($collection)
    {
        $xml = simplexml_load_file($this->database."/".$collection->getSource().".xml");
        
        foreach($xml->rows->row as $row){
            $data = array();
            foreach($row as $column=>$value){
                $data[(string)$column] = (string)$value;
            }
            $collection->addNewItem($data);
        }
        
        return parent::flatten($collection);
    }
    
    public function saveModel(Lightning_Stored_Model $model )
    {
        $file = $this->connection->getDatabase()."/".$model->getSource().".xml";
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
    
    public function deleteModel(Lightning_Stored_Model $model )
    {
        $file = $this->database."/".$model->getSource().".xml";
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
    
    private function sxml_append(SimpleXMLElement $to, SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
    
    private function formatXml($file){
        $contents = file_get_contents($file);
        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($contents);
        $dom->formatOutput = TRUE;
        $contents = $dom->saveXml();
        file_put_contents($file, $contents);
    }
}