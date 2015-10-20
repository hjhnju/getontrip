<?php
class Video_Api{
    
    /**
     * 接口1：Video_Api::getVideos($sightId,$page,$pageSize,$arrParam = array())
     * 获取爱奇艺视频信息
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public static function getVideos($sightId,$page,$pageSize,$arrParam = array()){
        $logicVideo = new Video_Logic_Video();
        return    $logicVideo->getVideos($sightId, $page,$pageSize,$arrParam);
    }
    
    /**
     * 接口2：Video_Api::getVideoNum($sighId)
     * 根据景点ID获取视频数量
     * @param integer $sighId
     * @param integer $status
     * @return number
     */
    public static function getVideoNum($sighId, $status = Video_Type_Status::PUBLISHED){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->getVideoNum($sighId, $status);
    }
    
    /**
     * 接口3:Video_Api::editVideo($id, $arrParam)
     * 修改视频信息
     * @param integer $id
     * @param array $arrParam
     */
    public static function editVideo($id, $arrParam){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->editVideo($id, $arrParam);
    }
    
    /**
     * 接口4:Video_Api::delVideo($id)
     * 删除视频
     * @param integer $id
     */
    public static function delVideo($id){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->delVideo($id);
    }
}