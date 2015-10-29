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
    public function getVideos($sightId,$page,$pageSize,$arrParam = array()){
       $list = new Video_List_Video();
       $arrFilter = array_merge(array('sight_id' => $sightId),$arrParam);
       $list->setFilter($arrFilter);
       $list->setPage($page);
       $list->setPagesize($pageSize);
       return $list->toArray();
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
        $list = new Video_List_Video();
        $arrFilter = array_merge(array('sight_id' => $sightId),$arrParam);
        $list->setFields(array('id','title','url','image','len','type'));
        $list->setFilter($arrFilter);
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $arrRet = $list->toArray();
        foreach($arrRet['list'] as $key => $val){
            $arrRet['list'][$key]['id']    = strval($val['id']);
            $arrRet['list'][$key]['title'] = Base_Util_String::getHtmlEntity($val['title']);
            $arrRet['list'][$key]['image'] = Base_Image::getUrlByName($val['image']);
            if($val['type'] == Video_Type_Type::ALBUM){
                $arrRet['list'][$key]['len'] = sprintf("合辑：共%d集",$val['len']);
            }else{
                $arrRet['list'][$key]['len'] = sprintf("时长：%s",$val['len']);
            }
            unset($arrRet['list'][$key]['type']);
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
        
        foreach($html->find('li.list_item') as $e){           
            $info = array();
            $info['title']     = trim(html_entity_decode($e->getAttribute('data-widget-searchlist-tvname')));
            $diversity         = intval($e->getAttribute('data-widget-searchlist-pagesize'));
            $info['type']      = ($diversity > 1)?Video_Type_Type::ALBUM:Video_Type_Type::VIDEO;
            $info['catageory'] = html_entity_decode($e->getAttribute('data-widget-searchlist-catageory'));
            $ret               = $e->find('a.figure',0);
            $info['url']       = trim($ret->getAttribute("href"));        
            $ret               = $e->find('a.figure img',0);
            $info['image']     = $this->uploadPic($ret->getAttribute("src"),$url);
            $info['status']    = Video_Type_Status::NOTPUBLISHED;
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
            
            $guid = md5($info['title'].$info['url'].$sightId);
            $id   = $this->getVideoByGuid($guid);            
            if(empty($id)){
                $objVideo          = new Video_Object_Video();
                $objVideo->sightId = $sightId;
                $objVideo->title   = Base_Util_String::getHtmlEntity($info['title']);
                $objVideo->from    = $info['from'];
                $objVideo->url     = $info['url'];
                $objVideo->image   = $info['image'];
                $objVideo->type    = $info['type'];
                $objVideo->status  = $info['status'];
                $objVideo->len     = $info['len'];
                $objVideo->guid    = $guid;
                $objVideo->weight  = $this->getVideoNum($sightId) + 1;
                $objVideo->save();
            }else{//删除上传了的图片，其它字段不改变
                $this->delPic($info['image']);
            }
            $arrData[]       = $info;
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
    
    public function getVideoNum($sighId, $status = Video_Type_Status::PUBLISHED){
        if($status == Video_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::VIDEO);
            if(!empty($ret)){
                return $ret;
            }
        }
        $listVideo = new Video_List_Video();
        if(!empty($status)){
            $listVideo->setFilter(array('sight_id' => $sighId, 'status' => $status));
        }else{
            $listVideo->setFilter(array('sight_id' => $sighId));
        }
        $listVideo->setPagesize(PHP_INT_MAX);
        $count = $listVideo->countAll();
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
        $objVideo = new Video_Object_Video();
        $objVideo->fetch(array('id' => $id));
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                if(($key == 'image') && !empty($objVideo->image)){
                    $this->delPic($objVideo->image);
                }
                $objVideo->$key = $val;
            }
        }
        return $objVideo->save();
    }
    
    /**
     * 添加视频
     * @param array $arrParam
     */
    public function addVideo($arrParam){
        $objVideo = new Video_Object_Video();
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                $objVideo->$key = $val;
            }
        }
        $objVideo->weight = $this->getVideoNum($arrParam['sight_id'])+1;
        return $objVideo->save();
    }
    
    /**
     * 删除视频
     * @param integer $id
     */
    public function delVideo($id){
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
    public function changeWeight($id,$to){
        $objVideo = new Video_Object_Video();
        $objVideo->fetch(array('id' => $id));
        $from       = $objVideo->weight;
        $objVideo->weight = $to;
    
        $bAsc = ($to > $from)?1:0;
        $min  = min(array($from,$to));
        $max  = max(array($from,$to));
        $listVideo = new Video_List_Video();
        $listVideo->setPagesize(PHP_INT_MAX);
        $listVideo->setFilter(array('sight_id' => $objVideo->sightId));
        $listVideo->setOrder('weight asc');
        $arrVideo = $listVideo->toArray();
        $arrVideo = array_slice($arrVideo['list'],$min-1+$bAsc,$max-$min);
        $ret = $objVideo->save();
        foreach ($arrVideo as $key => $val){
            $objVideo->fetch(array('id' => $val['id']));
            if($bAsc){
                $objVideo->weight = $min + $key ;
            }else{
                $objVideo->weight = $max - $key;
            }
            $objVideo->save();
        }
        return $ret;
    }
}