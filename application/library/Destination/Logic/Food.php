<?php
class Destination_Logic_Food extends Base_Logic{
    
    public function getDestList($page, $pageSize,$arrParam = array()){
        $listDest = new Destination_List_Food();
        if(!empty($arrParam)){
            $listDest->setFilter($arrParam);
        }
        $listDest->setPage($page);
        $listDest->setPagesize($pageSize);
        return $listDest->toArray();
    }
}