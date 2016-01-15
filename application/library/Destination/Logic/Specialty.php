<?php
class Destination_Logic_Specialty extends Base_Logic{
    
    public function getDestList($page, $pageSize,$arrParam = array()){
        $listDest = new Destination_List_Specialty();
        if(!empty($arrParam)){
            $listDest->setFilter($arrParam);
        }
        $listDest->setPage($page);
        $listDest->setPagesize($pageSize);
        return $listDest->toArray();
    }
}