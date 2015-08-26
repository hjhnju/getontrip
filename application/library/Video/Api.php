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
    
    /**
     * 接口2：Video_Api::editVideo($sightId,$id,$arrInfo)
     * 修改视频数据
     * @param integer $sightId,景点ID
     * @param integer $id,视频ID
     * @param array $arrInfo
     * @return boolean
     */
    public static function editVideo($sightId,$id,$arrInfo){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->editVideo($sightId, $id, $arrInfo);
    }
    
    /**
     * 接口3：Video_Api::delVideo($sightId,$id)
     * 删除视频数据
     * @param integer $sightId,景点ID
     * @param integer $id,视频ID
     * @return boolean
     */
    public function delVideo($sightId,$id){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->delVideo($sightId, $id);
    }
}