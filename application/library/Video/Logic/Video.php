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
        $arrRet = array();
        for($i = $from; $i<=$to; $i++){
            $ret = $redis->hGetAll(Video_Keys::getVideoInfoName($sightId, $i));
            if(($status !== Video_Type_Type::ALL)&&($status !== $ret['status'])){
                $i--;
                continue;
            }
            if(empty($ret)){
                break;  
            }
            $arrRet[] = $ret;
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
        $sight = Sight_Api::getSightById($sightId);
        $name  = urlencode(trim($sight[0]['name']));
        $ret   = file_get_contents("http://so.iqiyi.com/so/q_".$name."_page_".$page);
        $arrRet = $this->getSubstr("<li class=\"list_item\"", "</li>", $ret);
        $rule = '/.*?name="(.*?)".*?catageory="(.*?)".*?/';
        $arrData = array();
        foreach ($arrRet as $key => $val){
            $info = new stdClass();
            $val  = str_replace(" ", "", $val);
            $name = $this->getSubstr("searchlist.*?name=\"", "\"", $val);
            $catageory = $this->getSubstr("catageory=\"", "\"", $val);
            $image = $this->getSubstr("src=\"", "\"", $val);
            $image = isset($image[0])?$image[0]:$image;
            $image = $this->uploadPic(self::TYPE_VIDEO, $sightId.$page.$key, $image);            
            
            $url = $this->getSubstr("href=\"", "\".*?target=\"_blank\"", $val);
        
            $diversity   = $this->getSubstr("searchlist-pagesize=\"", "\"",$val);
        
            $info->name      = strip_tags(isset($name[0])?$name[0]:$name);
            $info->catageory = strip_tags(isset($catageory[0])?$catageory[0]:$catageory);
            $info->image     = $image;
            $info->url       = isset($url[0])?$url[0]:$url;
            $info->diversity = intval(isset($diversity[0])?$diversity[0]:$diversity);
            $arrData[]       = $info;
            
            $redis = Base_Redis::getInstance();
            $index = ($page-1)*self::PAGE_SIZE+$key+1;
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'title',$info->name);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'from','爱奇艺');
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'url',$info->url);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'image',$info->image);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'type',($info->diversity > 1)?Video_Type_Type::ALBUM:Video_Type_Type::VIDEO);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'status',Video_Type_Status::NOTPUBLISHED);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'create_time',time());
            $redis->setTimeout(Video_Keys::getVideoInfoName($sightId, $index),self::REDIS_TIME_OUT);
            
        }
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