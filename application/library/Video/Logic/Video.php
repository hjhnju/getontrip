<?php
class Video_Logic_Video extends Base_Logic{
    
    const PAGE_SIZE = 20;
    
    public function __construct(){
        
    }
    
    /**
     * 获取视频信息,供线上使用     
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
            
            $objVideo          = new Video_Object_Video();
            $objVideo->sightId = $sightId;
            $objVideo->title   = $info['title'];
            $objVideo->from    = $info['from'];
            $objVideo->url     = $info['url'];
            $objVideo->image   = $info['image'];
            $objVideo->type    = $info['type'];
            $objVideo->status  = $info['status'];
            $objVideo->len     = $info['len'];
            $objVideo->save();
            
            $info['id']      = $objVideo->id;
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
        foreach ($arrVideo as $key => $val){
            $video = $this->getVideoByInfo($val['id']);
            $arrVideo[$key]['name']  = trim($video['title']);
            $arrVideo[$key]['image'] = isset($video['image'])?Base_Image::getUrlByName($video['image']):'';
        }
        return $arrVideo;
    }
    
    public function getVideoNum($sighId){
        $listVideo = new Video_List_Video();
        $listVideo->setFilter(array('sight_id' => $sighId));
        $listVideo->setPagesize(PHP_INT_MAX);
        return $listVideo->countAll();
    }
}