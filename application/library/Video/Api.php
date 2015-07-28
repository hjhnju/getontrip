<?php
class Video_Api{
    
    const PAGE_SIZE = 20;
    
    /**
     * 接口1：Video_Api::getVideos($sightId,$page)
     * 获取爱奇艺视频信息
     * @param integer $sightId，景点ID
     * @param integer $page,页码，页面大小不可修改
     * @return array
     */
    public static function getVideos($sightId,$page,$status=Video_Type_Status::ALL){
        $logicVideo = new Video_Logic_Video();
        $arrVideo   = $logicVideo->getVideos($sightId, $page,self::PAGE_SIZE,$status);
        if(!empty($arrVideo)){
            return $arrVideo;
        }
        return $logicVideo->getAiqiyiSource($sightId,$page);
    }
}