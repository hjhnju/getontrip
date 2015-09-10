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
        $arrVideoKeys= array();        
        $num    = 1;
        $arrVideoKeys = $redis->keys(Video_Keys::getVideoInfoName($sightId, "*"));
        $arrVideoKeys = $this->keySort($arrVideoKeys);
        $count        = count($arrVideoKeys);
        foreach ($arrVideoKeys as  $key){
            $ret = $redis->hGetAll($key);
            if (($num >= $from)&&($num <= $to)){
                if ($status == Video_Type_Status::ALL || $status == $ret['status']){
                    $ret['totalNum'] = $count;
                    $arrRet[] = $ret;
                }
            }
            $num += 1;
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
        $arrData = array();
        $sight = Sight_Api::getSightById($sightId);
        $name  = urlencode(trim($sight['name']));        
        $url = "http://so.iqiyi.com/so/q_".$name."_page_".$page;
        $html = file_get_html($url);
        
        //视频总数
        //$item  = $html->find('div.mod-page a',-2);
        //$count = $item->getAttribute('data-key')*self::PAGE_SIZE;
        
        $logicBlack = new Black_Logic_Black();
        $arrBlackId = $logicBlack->getList(Black_Type_Type::VIDEO);
        $key        = 0;
        
        foreach($html->find('li.list_item') as $e){
            $strMark = $e->getAttribute('data-searchpingback-position');
            preg_match('/target=(.*?)&/is', $strMark,$match);
            if(isset($match[1])){
                $sign = $match[1];
            }
            $info = array();
            $info['title']     = trim(html_entity_decode($e->getAttribute('data-widget-searchlist-tvname')));
            $diversity         = intval($e->getAttribute('data-widget-searchlist-pagesize'));
            $info['type']      = ($diversity > 1)?Video_Type_Type::ALBUM:Video_Type_Type::VIDEO;
            $info['catageory'] = html_entity_decode($e->getAttribute('data-widget-searchlist-catageory'));
            $ret               = $e->find('a.figure',0);
            $info['url']       = trim($ret->getAttribute("href"));        
            $ret               = $e->find('a.figure img',0);
            $info['image']     = Base_Image::getUrlByName($this->uploadPic($ret->getAttribute("src"),$url));
            $info['status']    = Video_Type_Status::PUBLISHED;
            $info['from']      = '爱奇艺';
            $info['create_time'] = time();          
            
            $id = md5($info['title'].$info['url']);
            if(in_array($id,$arrBlackId)){
                continue;
            }
            
            if(Video_Type_Type::VIDEO == $info['type']){
                $ele = $e->find('p.viedo_rb span.v_name',0);
                if($ele){
                    $info['len'] = $ele->innertext;
                }else{
                    $info['len'] = '1';
                }
            }else{
                $ele  = $e->find('li.album_item a',-1);
                if(!empty($ele)){
                    $data = $ele->getAttribute("title");
                }                
                if(empty($data) || stristr($data,"更多")){
                    $ele = $e->find('li.album_item a',-2);
                }
                if($ele){
                    $strLen = $ele->getAttribute("title");
                    sscanf($strLen,"第%d集",$intLen);
                }else{
                    $intLen = 1;
                }
                $info['len'] = strval($intLen);
            }
       
            $redis = Base_Redis::getInstance();
            $index = ($page-1)*self::PAGE_SIZE+$key+1;
            $picName = $redis->hget(Video_Keys::getVideoInfoName($sightId, $index),'image');
            if(!empty($picName)){
                $this->delPic($picName);
            }
            $redis->delete(Video_Keys::getVideoInfoName($sightId, $index));
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'id',$index);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'title',$info['title']);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'from',$info['from']);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'url',$info['url']);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'image',$info['image']);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'type',$info['type']);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'status',$info['status']);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'create_time',$info['create_time']);
            $redis->hset(Video_Keys::getVideoInfoName($sightId, $index),'len',$info['len']);
            
            $info['id']      = $index;
            $arrData[]       = $info;
            $key            += 1;
        }
        $html->clear();
        return $arrData;
    }
    
    /**
     * 修改视频数据
     * @param integer $sightId,景点ID
     * @param integer $id,视频ID
     * @param array $arrInfo
     * @return boolean
     */
    public function editVideo($sightId,$id,$arrInfo){
        $redis   = Base_Redis::getInstance();
        $ret     = false;
        $arr     = $redis->hGetAll(Video_Keys::getVideoInfoName($sightId, $id));
        $arrKeys  = array_keys($arr);
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$arrKeys)){
                $arr[$key] = $val;
            }
        }
        $ret = $redis->hMset(Video_Keys::getVideoInfoName($sightId, $id),$arr);
        return $ret;       
    }
    
    /**
     * 删除视频数据
     * @param integer $sightId,景点ID
     * @param integer $id,视频ID
     * @return boolean
     */
    public function delVideo($sightId,$id){
        $redis    = Base_Redis::getInstance();
        $logic    = new Black_Logic_Black();
        $picName  = $redis->hget(Video_Keys::getVideoInfoName($sightId, $id),'image');
        $title    = $redis->hget(Video_Keys::getVideoInfoName($sightId, $id),'title');
        $url      = $redis->hget(Video_Keys::getVideoInfoName($sightId, $id),'url');
        $id       = md5($title.$url);
        $logic->addBlack($id, Black_Type_Type::VIDEO);
        if(!empty($picName)){
            $this->delPic($picName);
        }
        $ret      = $redis->delete(Video_Keys::getVideoInfoName($sightId, $id));
        return $ret;
    }
}