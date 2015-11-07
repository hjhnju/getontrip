<?php
class Video_Api{
    
    /**
     * 接口1：Video_Api::getVideos($page,$pageSize,$arrParam = array())
     * 获取爱奇艺视频信息
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public static function getVideos($page,$pageSize,$arrParam = array()){
        $logicVideo = new Video_Logic_Video();
        return    $logicVideo->getVideos($page,$pageSize,$arrParam);
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
    
    /**
     * 接口5:Video_Api::addVideo($arrParam)
     * 添加视频
     * @param array $arrParam,array('title'=>'xxx','sight_id'=>1,...)
     */
    public static function addVideo($arrParam){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->addVideo($arrParam);
    }
    
    /**
     * 接口6:Video_Api::getVideoInfo($id)
     * 根据ID获取视频信息
     * @param string $id
     * @return array
     */
    public static function getVideoInfo($id){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->getVideoByInfo($id);
    }
    
    /**
     * 接口7：Video_Api::changeWeight($id,$to)
     * 修改某景点下的视频的权重
     * @param integer $id 视频ID
     * @param integer $to 需要排的位置
     * @return boolean
     */
    public static function changeWeight($id,$to){
        $logicVideo = new Video_Logic_Video();
        return $logicVideo->changeWeight($id,$to);
    }
}