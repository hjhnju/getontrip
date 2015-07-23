<?php
class Video_Logic_Video extends Base_Logic{
    
    public function __construct(){
        
    }
    
    /**
     * 获取视频信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getVideos($sightId,$page,$pageSize){
        $listVideo = new Video_List_Video();
        $listVideo->setFilter(array('sight_id' => $sightId));
        $listVideo->setPage($page);
        $listVideo->setPagesize($pageSize);
        $arrRet = $listVideo->toArray();
        return $arrRet;
    }
}