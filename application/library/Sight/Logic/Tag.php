<?php
class Sight_Logic_Tag extends Base_Logic{
    
    protected $_logicTopic;
    
    protected $_logicTag;
    
    protected $_modelTopic;
    
    /**
     * -1 需要隐藏的标签
     * @var integer
     */
    const HIDE_TAG = -1;
    
    /**
     * 1 话题的显示标签
     * @var integer
     */
    const TOPIPC   = 1;
    
    /**
     * 10 景观标签
     * @var integer
     */
    const LANDSCAPE = 2;
    
    /**
     * 11 书籍标签
     * @var integer
     */
    const BOOK      = 3;
    
    /**
     * 12 视频标签
     * @var integer
     */
    const VIDEO     = 4;
        
    
    const STR_LANDSCAPE = 'landscape';
    
    const STR_VIDEO = 'video';
    
    const STR_BOOK = 'book';
    
    public function __construct(){
        $this->_logicTopic = new Topic_Logic_Topic();
        $this->_logicTag   = new Tag_Logic_Tag();
        $this->_modelTopic = new TopicModel();
    }
    
    public function getTagsBySight($sightId){ 
        $arrTopTag       = array();
        $arrCommonTag    = array();
        $arrLessTopicTag = array(); 
        $arrTopicIds     = array();
        
        /*$arrRet          = array();
        $redis           = Base_Redis::getInstance();
        $arrRet    = $redis->zRange(Sight_Keys::getSightShowTagIds($sightId),0,-1);
        if(!empty($arrRet)){
            foreach ($arrRet as $val){
                $temp = explode(",",$val);
                $arrData['id']   = $temp[0];
                $arrData['type'] = $temp[1];
                $arrData['name'] = $temp[2];
                $arrCommonTag[]  = $arrData;
            }
            return $arrCommonTag;
        }*/
        
        $limit_num       = Base_Config::getConfig('showtag')->topicnum; 
        $limit_general   = Base_Config::getConfig('showtag')->firstnum;
        $strTopicIds = $this->_logicTopic->getTopicIdBySight($sightId);
        if(!empty($strTopicIds)){
            $arrTopicIds = explode(",",$strTopicIds);
        }        
        foreach ($arrTopicIds as $id){
            $arrTags = $this->_logicTag->getTopicTags($id);
            foreach ($arrTags as $val){
                $temp = array();
                $tag  = $this->_logicTag->getTagByName($val);
                
                $temp['id']     = strval($tag['id']);
                $temp['name']   = $val;
                if(!empty($temp)){
                    $num = $this->_modelTopic->getTopicNumByTag($tag['id'], $sightId);
                    if($num >= $limit_num && $tag['weight'] !== self::HIDE_TAG){
                        if(!in_array($temp,$arrCommonTag) ){
                            $arrCommonTag[] = $temp;
                        }
                    }else{
                        $arrLessTopicTag[]  = $temp['id'];
                    }
                }               
            }
        }
        if(!empty($arrLessTopicTag)){
            $logic     = new Tag_Logic_Relation();
            $arrTopTag = $logic->groupByTop($arrLessTopicTag);
        }
        
        $listSightTag = new Sight_List_Tag();
        $listSightTag->setFilter(array('sight_id' => $sightId));
        $listSightTag->setPagesize(PHP_INT_MAX);
        $arrTemp = $listSightTag->toArray();
        foreach ($arrTemp['list'] as $key => $val){
            $num = $this->_modelTopic->getTopicNumByTag($val['tag_id'],$sightId);
            if($num < $limit_general){
                continue;
            }
            $temp['id']   = trim($val['tag_id']);
            $tag  = $this->_logicTag->getTagById($val['tag_id']);
            $temp['name'] = trim($tag['name']);
            
            $arrCommonTag[] =   $temp;
        }
        
        //判断有无视频,书籍,而增加相应标签      
        $wiki  = Keyword_Api::getKeywordNum($sightId);
        $book  = Book_Api::getBookNum($sightId);
        $video = Video_Api::getVideoNum($sightId);
        if(!empty($wiki)){
            //array_unshift($arrTopTag,array('id' => strval(self::STR_LANDSCAPE),'type' => strval(self::LANDSCAPE), 'name' => '景观')); 
            $arrCommonTag[] = array('id' => strval(self::STR_LANDSCAPE),'type' => strval(self::LANDSCAPE), 'name' => '景观');
        }
        if(!empty($book)){
            $arrCommonTag[] = array('id' => strval(self::STR_BOOK),'type' => strval(self::BOOK), 'name' => '书籍');
        }
        if(!empty($video)){
            $arrCommonTag[] = array('id' => strval(self::STR_VIDEO),'type' => strval(self::VIDEO), 'name' => '视频');
        }        
        $arrCommonTag = array_merge($arrTopTag,$arrCommonTag);
        foreach ($arrCommonTag as $key => $val){
            if(!isset($val['type'])){
                $arrCommonTag[$key]['type'] = strval(self::TOPIPC);
            }
        }
        
        /*foreach ($arrCommonTag as $index => $tag){
            $data = $tag['id'].",".$tag['type'].",".$tag['name'];
            $redis->zAdd(Sight_Keys::getSightShowTagIds($sightId),$index,$data);  
        }*/
        return $arrCommonTag;
    }
}