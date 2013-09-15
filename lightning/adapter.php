<?php
class Lightning_Adapter
{
    public function buildCollection($collection)
    {
        $stack = array($collection);
        $joins = $collection->getCollectionJoins();
        reset($joins);
        foreach ($collection->getCollections() as $index => $child_collection) {
            $stack[] = $child_collection;
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

                $join = next($this->collection_joins);
            }
        }

        $this->flattened = true;
        $this->flatten_running = false;
        $this->applyFilters();
        
        return $this;
    }
}