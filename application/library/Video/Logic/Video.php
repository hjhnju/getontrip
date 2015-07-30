<?php
class Video_Logic_Video extends Base_Logic{
    
    const PAGE_SIZE = 20;
    
    public function __construct(){
        
    }
    
    /**
     * 获取视频信息,供线上使用
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @param integer $status，视频状态类型
     * @return array
     */
    public function getVideos($sightId,$page,$pageSize,$status=Video_Type_Status::PUBLISHED){
        $redis  = Base_Redis::getInstance();
        $from   = ($page-1)*$pageSize+1;
        $to     = $page*$pageSize;
        $ret    = array();
        $arrRet = array();
        if($status == Video_Type_Status::ALL){
            for($i = $from; $i<=$to; $i++){
                $arrItem = array();
                $ret = $redis->hGetAll(Video_Keys::getVideoInfoName($sightId, $i));
                if(empty($ret)){
                    break;
                }
                $arrRet[] = $ret;
            }
        }else{
            $arrVideoKeys = $redis->keys(Video_Keys::getVideoInfoName($sightId, "*"));
            foreach ($arrVideoKeys as $index => $VideoKey){
                $ret = $redis->hGetAll($VideoKey);
                $num = $index + 1;
                if(($ret['status'] == $status)&&($num >= $from)&&($num <= $to)){
                    $arrRet[] = $ret;
                }
            }
        }        
        return $arrRet; 
    }
        
    /**
     * 从爱奇艺源获取数据
     * @param string $query
     * @param integer $page
     * @return array
     */
    public function getAiqiyiSource($sightId,$page){
        require_once(APP_PATH."/application/library/Base/HtmlDom.php");
        $sight = Sight_Api::getSightById($sightId);
        $name  = urlencode(trim($sight['name']));        
        $url = "http://so.iqiyi.com/so/q_".$name."?source=input";
        $html = file_get_html($url);
        foreach($html->find('li.list_item') as $key => $e){
            $info = new stdClass();
            $info->name      = $e->getAttribute('data-widget-searchlist-tvname');
            $diversity       = intval($e->getAttribute('data-widget-searchlist-pagesize'));
            $info->type      = ($diversity > 1)?Video_Type_Type::ALBUM:Video_Type_Type::VIDEO;
            $info->catageory = $e->getAttribute('data-widget-searchlist-catageory');
            $ret             = $e->find('a.figure',0);
            $info->url       = $ret->getAttribute("href");        
            $ret             = $e->find('a.figure img',0);
            $info->image     = $this->uploadPic(self::TYPE_VIDEO, $sightId.$page.$key, $ret->getAttribute("src"));
            $arrData[]       = $info;
            
            $redis = Base_Redis::getInstance();
            $index = ($page-1)*self::PAGE_SIZE+$key+1;
            $redis->delete(Video_Keys::getVideoInfoName($sightId, $index));
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'title',$info->name);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'from','爱奇艺');
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'url',$info->url);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'image',$info->image);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'type',$info->type);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'status',Video_Type_Status::NOTPUBLISHED);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'create_time',time());
            $redis->setTimeout(Video_Keys::getVideoInfoName($sightId, $index),self::REDIS_TIME_OUT);
        }
        $html->clear();
        return $arrData;
    }
    
    /**
     * 根据ID数组删除视频信息
     * @param array $arrIds
     * @return boolean
     */
    public function delVideos($arrIds){
        foreach ($arrIds as $id){
            $objVideo = new Video_Object_Video();
            $objVideo->fetch(array('id' => $id));
            $ret = $objVideo->remove();
        }
        return $ret;
    }
}