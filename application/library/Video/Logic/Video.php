<?php
class Video_Logic_Video extends Base_Logic{
    
    const PAGE_SIZE = 20;
    
    protected $fields = array('sight_id', 'title', 'url', 'image', 'from', 'len', 'type', 'status', 'create_time', 'update_time', 'create_user', 'update_user');
    
    public function __construct(){
        
    }
    
    /**
     * 获取视频信息,供后端使用     
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public function getVideos($page,$pageSize,$arrParam = array()){
        $arrVideo          = array();
        if(isset($arrParam['sight_id'])){
            $sightId    = $arrParam['sight_id'];
            $arrVideoIds = array();            
            $listSightVideo = new Sight_List_Video();
            $listSightVideo->setFilter(array('sight_id' => $sightId));
            if(isset($arrParam['order'])){
                $listSightVideo->setOrder($arrParam['order']);
                unset($arrParam['order']);
            }
            $listSightVideo->setPagesize(PHP_INT_MAX);
            $ret = $listSightVideo->toArray();
            foreach ($ret['list'] as $val){
                $arrVideoIds[] = $val['video_id'];
            }
            unset($arrParam['sight_id']);
            $filter     = "`id` in (".implode(",",$arrVideoIds).")";
            $listVideo = new Video_List_Video();
            if(isset($arrParam['title'])){
                $filter .= " and `title` like '".$arrParam['title']."%'";
            }
            unset($arrParam['title']);
            foreach ($arrParam as $index => $val){
                $filter .= " and `".$index."` = ".$val;
            }            
            $listVideo->setFilterString($filter);
            $listVideo->setPagesize(PHP_INT_MAX);
            $arrVideo = $listVideo->toArray();
            foreach ($arrVideoIds as $key => $id){
                $arrVideo['list'][$key] = $id;
            }            
            $arrVideo['list'] = array_slice($arrVideo['list'], ($page-1)*$pageSize,$pageSize);
        }else{
            $listVideo = new Video_List_Video();
            if(!empty($arrParam)){
                if(isset($arrParam['order'])){
                    $listVideo->setOrder($arrParam['order']);
                    unset($arrParam['order']);
                }
                $filter = '1';
                if(isset($arrParam['title'])){
                    $filter .= " and `title` like '".$arrParam['title']."%'";
                }
                unset($arrParam['title']);
                foreach ($arrParam as $index => $val){
                    $filter .= " and `".$index."` = ".$val;
                } 
                if(!empty($filter)){
                    $listVideo->setFilterString($filter);
                }
            }          
            $listVideo->setPage($page);
            $listVideo->setPagesize($pageSize);
            $arrVideo = $listVideo->toArray();  
            foreach ($arrVideo['list'] as $key => $val){
                $arrVideo['list'][$key] = $val['id'];   
            }
        }
        foreach($arrVideo['list'] as $key => $val){
            $temp = array();
            $arrVideo['list'][$key] = Video_Api::getVideoInfo($val);
            $listSightVideo = new Sight_List_Video();
            $listSightVideo->setFilter(array('video_id' => $val));
            $listSightVideo->setPagesize(PHP_INT_MAX);
            $arrSightVideo  = $listSightVideo->toArray();
            foreach ($arrSightVideo['list'] as $data){
                $sight          = Sight_Api::getSightById($data['sight_id']);
                $temp['id']     = $data['sight_id'];
                $temp['name']   = $sight['name'];
                $temp['weight'] = $data['weight'];
            }
            $arrVideo['list'][$key]['sights'][] = $temp;
        }
        return $arrVideo;
    }
    
    /**
     * 获取视频信息,供前端使用
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public function getVideoList($sightId,$page,$pageSize,$arrParam = array()){
        $listSightVideo = new Sight_List_Video();
        $listSightVideo->setFilter(array('sight_id' => $sightId));
        $listSightVideo->setOrder('`weight` asc');
        $listSightVideo->setPagesize(PHP_INT_MAX);
        $ret = $listSightVideo->toArray();
        foreach ($ret['list'] as $val){
            $arrVideoIds[] = $val['video_id'];
        }
        $filter     = "`id` in (".implode(",",$arrVideoIds).")";
        $listVideo = new Video_List_Video();
        foreach ($arrParam as $key => $val){
            $filter .=" and `".$key."` =".$val;
        }
        $listVideo->setFilterString($filter);
        $listVideo->setPagesize(PHP_INT_MAX);
        $arrVideo = $listVideo->toArray();
        foreach ($arrVideoIds as $key => $id){
            $arrVideo['list'][$key] = $id;
        }
        $arrVideo['list'] = array_slice($arrVideo['list'], ($page-1)*$pageSize,$pageSize);

        foreach($arrVideo['list'] as $key => $val){
            $arrRet['list'][$key]['id']    = strval($val);
            $video = Video_Api::getVideoInfo($val);
            $arrRet['list'][$key]['title'] = Base_Util_String::getHtmlEntity($video['title']);
            $arrRet['list'][$key]['image'] = Base_Image::getUrlByName($video['image']);
            if($val['type'] == Video_Type_Type::ALBUM){
                $arrRet['list'][$key]['len'] = sprintf("合辑：共%d集",$video['len']);
            }else{
                $arrRet['list'][$key]['len'] = sprintf("时长：%s",$video['len']);
            }
            $arrRet['list'][$key]['type']    = strval($video['type']);
        }
        return $arrRet['list'];
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
        $count = 0;
        foreach($html->find('li.list_item') as $index => $e){           
            $info = array();
            $info['title']     = $e->getAttribute('data-widget-searchlist-tvname');
            if(empty($info['title'])){
                $test = $html->find("h3.result_title a",$index);
                $info['title'] = $test->getAttribute('title');
            }
            $diversity         = intval($e->getAttribute('data-widget-searchlist-pagesize'));
            $info['type']      = ($diversity > 1)?Video_Type_Type::ALBUM:Video_Type_Type::VIDEO;
            $info['catageory'] = html_entity_decode($e->getAttribute('data-widget-searchlist-catageory'));
            $ret               = $e->find('a.figure',0);
            $info['url']       = trim($ret->getAttribute("href"));        
            $ret               = $e->find('a.figure img',0);
            $info['image']     = $this->uploadPic($ret->getAttribute("src"),$url);
            $info['status']    = Video_Type_Status::PUBLISHED;
            $info['from']      = '爱奇艺';
            $info['create_time'] = time();          
            
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
            
            $guid = md5($info['title'].$info['url']);
            $id   = $this->getVideoByGuid($guid);            
            if(empty($id)){
                $objVideo          = new Video_Object_Video();
                $objVideo->title   = Base_Util_String::delStartEmpty(Base_Util_String::getHtmlEntity($info['title']));
                if(empty($objVideo->title)){
                    $this->delPic($info['image']);
                    continue;
                }
                $objVideo->from    = $info['from'];
                $objVideo->url     = $info['url'];
                $objVideo->image   = $info['image'];
                $objVideo->type    = $info['type'];
                $objVideo->status  = $info['status'];
                $objVideo->len     = $info['len'];
                $objVideo->guid    = $guid;
                $objVideo->save();
                
                $objSightVideo = new Sight_Object_Video();
                $objSightVideo->sightId = $sightId;
                $objSightVideo->videoId = $objVideo->id;
                $objSightVideo->weight  = $this->getAllVideoNum($sightId);
                $objSightVideo->save();
              
            }else{//删除上传了的图片，其它字段不改变
                $this->delPic($info['image']);
            }
            $arrData[]       = $info;
            $count += 1;
        }
        if(empty($count)){
            Base_Log::error('sight '.$sightId.' can not get aqiyi videos!');
        }
        $html->clear();
        return $arrData;
    }    
    
    public function getVideoByInfo($videoId){
        $objVideo = new Video_Object_Video();
        $objVideo->fetch(array('id' => $videoId));
        return $objVideo->toArray();
    }
    
    public function search($query, $page, $pageSize){
        $arrVideo  = Base_Search::Search('video', $query, $page, $pageSize, array('id'));
        $num       = $arrVideo['num'];
        $arrVideo  = $arrVideo['data'];
        foreach ($arrVideo as $key => $val){
            $video = $this->getVideoByInfo($val['id']);            
            $arrVideo[$key]['title'] = empty($val['title'])?trim($video['title']):$val['title'];
            $arrVideo[$key]['image'] = isset($video['image'])?Base_Image::getUrlByName($video['image']):'';
            $arrVideo[$key]['url']   = isset($video['url'])?trim($video['url']):'';
            $arrVideo[$key]['from']  = isset($video['from'])?trim($video['from']):'';
        }
        return array('data' => $arrVideo, 'num' => $num);
    }
    
    public function getAllVideoNum($sightId){
        $maxWeight  = 0;    
        $listSightVideo = new Sight_List_Video();
        $listSightVideo->setFilter(array('sight_id' => $sightId));
        $listSightVideo->setPagesize(PHP_INT_MAX);
        $arrSightVideo  = $listSightVideo->toArray();
        foreach ($arrSightVideo['list'] as $val){
            if($val['weight'] > $maxWeight){
                $maxWeight = $val['weight'];
            }
        }
        return $maxWeight + 1;
    }
    
    public function getVideoNum($sighId, $status = Video_Type_Status::PUBLISHED){
        if($status == Video_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::VIDEO);
            if(!empty($ret)){
                return $ret;
            }
        }
        $count          = 0;
        $listSightVideo = new Sight_List_Video();
        $listSightVideo->setFilter(array('sight_id' => $sighId));
        $listSightVideo->setPagesize(PHP_INT_MAX);
        $arrSightVideo  = $listSightVideo->toArray();
        foreach ($arrSightVideo['list'] as $val){
            if($val['statu'] == $status){
                $count += 1;
            }
        }
        if($status == Video_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::VIDEO,$count);
        }
        return $count;
    }
    
    /**
     * 修改视频信息
     * @param integer $id
     * @param array $arrParam
     */
    public function editVideo($id, $arrParam){
        $arrSight = array();
        if(isset($arrParam['sight_id'])){
            $listSightVideo = new Sight_List_Video();
            $listSightVideo->setFilter(array('video_id' => $id));
            $listSightVideo->setPagesize(PHP_INT_MAX);
            $arrSightVideo  = $listSightVideo->toArray();
            foreach ($arrSightVideo['list'] as $val){
                $objSightVideo = new Sight_Object_Video();
                $objSightVideo->fetch(array('id' => $val['id']));
                $objSightVideo->remove();
            }
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        $objVideo = new Video_Object_Video();
        $objVideo->fetch(array('id' => $id));
        
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                if(($key == 'image') && ($objVideo->image !== $val)){
                    $this->delPic($objVideo->image);
                }
                $objVideo->$key = $val;
            }
        }
        
        foreach ($arrSight as $sightId){
            $objSightVideo = new Sight_Object_Video();
            $objSightVideo->sightId = $sightId;
            $objSightVideo->videoId = $id;
            $objSightVideo->weight  = $this->getAllVideoNum($sightId);
            $objSightVideo->save();
        }
        return $objVideo->save();
    }
    
    /**
     * 添加视频
     * @param array $arrParam
     */
    public function addVideo($arrParam){
        $arrSight = array();
        if(isset($arrParam['sight_id'])){
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        $objVideo = new Video_Object_Video();
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                $objVideo->$key = $val;
            }
        }
        $objVideo->guid   = md5($arrParam['title'].$arrParam['url']);
        $ret =             $objVideo->save();
        
        foreach ($arrSight as $sightId){
            $objSightVideo = new Sight_Object_Video();
            $objSightVideo->sightId = $sightId;
            $objSightVideo->videoId = $objVideo->id;
            $objSightVideo->weight  = $this->getAllVideoNum($sightId);
            $objSightVideo->save();
        }
        return $objVideo->id;
    }
    
    /**
     * 删除视频
     * @param integer $id
     */
    public function delVideo($id){
        $listSightVideo = new Sight_List_Video();
        $listSightVideo->setFilter(array('video_id' => $id));
        $listSightVideo->setPagesize(PHP_INT_MAX);
        $arrSightVideo  = $listSightVideo->toArray();
        foreach ($arrSightVideo['list'] as $val){
            $objSightVideo = new Sight_Object_Video();
            $objSightVideo->fetch(array('id' => $val['id']));
            $objSightVideo->remove();
        }
        
        $objVideo = new Video_Object_Video();
        $objVideo->fetch(array('id' => $id));
        if(!empty($objVideo->image)){
            $this->delPic($objVideo->image);
        }
        return $objVideo->remove();
    }
    
    /**
     * 根据GUID获取视频ID
     * @param string $strGuid
     * @return number|''
     */
    public function getVideoByGuid($strGuid){
        $objVideo = new Video_Object_Video();
        $objVideo->fetch(array('guid' => $strGuid));
        if($objVideo->id){
            return $objVideo->id;
        }
        return '';
    }
    
    /**
     * 修改某景点下的视频的权重
     * @param integer $id 视频ID
     * @param integer $to 需要排的位置
     * @return boolean
     */
    public function changeWeight($sightId,$id,$to){
        $objSightVideo = new Sight_Object_Video();
        $objSightVideo->fetch(array('sight_id'=>$sightId,'video_id' => $id));
        $from          = $objSightVideo->weight;
        $objSightVideo->weight = $to;
    
        $bAsc = ($to > $from)?1:0;
        $min  = min(array($from,$to));
        $max  = max(array($from,$to));
        $listSightVideo = new Sight_List_Video();
        $listSightVideo->setPagesize(PHP_INT_MAX);
        $listSightVideo->setFilter(array('sight_id' => sightId));
        $listSightVideo->setOrder('weight asc');
        $arrSightVideo = $listSightVideo->toArray();
        $arrSightVideo = array_slice($arrSightVideo['list'],$min-1+$bAsc,$max-$min);
        $ret = $objSightVideo->save();
        foreach ($arrSightVideo as $key => $val){
            $objSightVideo->fetch(array('id' => $val['id']));
            if($bAsc){
                $objSightVideo->weight = $min + $key ;
            }else{
                $objSightVideo->weight = $max - $key;
            }
            $objSightVideo->save();
        }
        return $ret;
    }
}