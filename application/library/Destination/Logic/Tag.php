<?php
class Destination_Logic_Tag extends Base_Logic{
    
    public function getDestinationTags($destId, $destType, $page = 1, $pageSize = PHP_INT_MAX){
        $arrRet       = array();
        $listDestTags = new Destination_List_Tag();
        $listDestTags->setFilter(array('destination_id' => $destId,'destination_type' => $destType));
        $listDestTags->setPage($page);
        $listDestTags->setPagesize($pageSize);
        $arrDestTags  = $listDestTags->toArray();
        foreach ($arrDestTags['list'] as $val){
            $tag = Tag_Api::getTagInfo($val['tag_id']);
            $arrRet[] = array(
                'id'   => $tag['id'],
                'name' => $tag['name'],
                );
        }
        return $arrRet;
    }
}