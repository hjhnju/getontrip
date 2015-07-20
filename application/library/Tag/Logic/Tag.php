<?php
class Tag_Logic_Tag{
    public function __construct(){
        
    }
    
    /**
     * 获取标签信息列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getTagList($page, $pageSize){
        $listTag = new Tag_List_Tag();
        $listTag->setPage($page);
        $listTag->setPagesize($pageSize);
        return $listTag->toArray();
    }
    
    /**
     * 获取热门标签列表，根据话题所有的标签数量排序作为热度
     * @param integer $size
     * @return array
     */
    public function getHotTags($size){
        $redis    = Base_Redis::getInstance();
        $arrCount = array();
        $listTag  = new Tag_List_Tag();
        $listTag->setPagesize(PHP_INT_MAX);
        $arrTag = $listTag->toArray();
        foreach ($arrTag['list'] as $val){
            $arrCount[] = $redis->hGet(Tag_Keys::getTagInfoKey($val['id']),'num');
        }
        array_multisort($arrCount, SORT_DESC , $arrTag['list']);
        $arrRet = array_slice($arrTag['list'],0,$size);
        return $arrRet;
    }
}