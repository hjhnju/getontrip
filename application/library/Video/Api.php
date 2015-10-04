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
}