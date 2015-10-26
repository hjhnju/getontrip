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
     * 5 景观标签
     * @var integer
     */
    const LANDSCAPE = 5;
    
    /**
     * 6 视频标签
     * @var integer
     */
    const VIDEO    = 6;
    
    /**
     * 7 书籍标签
     * @var integer
     */
    const BOOK    = 7;
    
    const STR_LANDSCAPE = 'landscape';
    
    const STR_VIDEO = 'video';
    
    const STR_BOOK = 'book';
    
    public function __construct(){
        $this->_logicTopic = new Topic_Logic_Topic();
        $this->_logicTag   = new Tag_Logic_Tag();
        $this->_modelTopic = new TopicModel();
    }
    
    public function getTagsBySight($sightId){ 
        $arrRet          = array();
        $arrCommonTag    = array();
        $arrLessTopicTag = array(); 
        $arrTopicIds     = array();
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
        }
        
        $limit_num       = Base_Config::getConfig('showtag')->topicnum;
        $listSightTag = new Sight_List_Tag();
        $listSightTag->setFilter(array('sight_id' => $sightId));
        $listSightTag->setPagesize(PHP_INT_MAX);
        $arrTemp = $listSightTag->toArray();
        foreach ($arrTemp['list'] as $key => $val){
            $arrGeneralTag[$key]['id']   = trim($val['tag_id']);
            $tag  = $this->_logicTag->getTagById($val['tag_id']);
            $arrGeneralTag[$key]['type'] = strval(Tag_Type_Tag::GENERAL);
            $arrGeneralTag[$key]['name'] = trim($tag['name']);
        }
        $strTopicIds = $this->_logicTopic->getTopicIdBySight($sightId);
        if(!empty($strTopicIds)){
            $arrTopicIds = explode(",",$strTopicIds);
        }        
        foreach ($arrTopicIds as $id){
            $arrTags = $this->_logicTag->getTopicTags($id);
            foreach ($arrTags as $val){
                $temp = array();
                $tag  = $this->_logicTag->getTagByName($val);
                //通用标签，如果话题数达不到条件范围则不显示
                if($tag['type'] == Tag_Type_Tag::GENERAL){
                    $temp['id']     = strval($tag['id']);
                    $temp['type']   = strval($tag['type']);
                    $temp['name']   = $val;
                    if(!in_array($temp,$arrCommonTag) && !empty($temp)){
                        $num = $this->_modelTopic->getTopicNumByTag($tag['id'], $sightId);
                        if( $num >= $limit_num){
                            $arrCommonTag[] = $temp;
                        }                        
                    }
                }else{//分类标签，可能需要归结到一级分类标签来显示
                    $temp['id']     = strval($tag['id']);
                    $temp['type']   = strval($tag['type']);
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
        }
        if(count($arrLessTopicTag) >= $limit_num){
            $logic  = new Tag_Logic_Relation();
            $arrCommonTag = array_merge($arrCommonTag,$logic->groupByTop($arrLessTopicTag));
        }
        
        //判断有无视频,书籍,而增加相应标签      
        $wiki  = Keyword_Api::getKeywordNum($sightId);
        $book  = Book_Api::getBookNum($sightId);
        $video = Video_Api::getVideoNum($sightId);
        if(!empty($wiki)){
            $arrCommonTag[] = array('id' => strval(self::STR_LANDSCAPE),'type' => strval(self::LANDSCAPE), 'name' => '景观');
        }
        if(!empty($book)){
            $arrCommonTag[] = array('id' => strval(self::STR_BOOK),'type' => strval(self::BOOK), 'name' => '书籍');
        }
        if(!empty($video)){
            $arrCommonTag[] = array('id' => strval(self::STR_VIDEO),'type' => strval(self::VIDEO), 'name' => '视频');
        }        
        foreach ($arrCommonTag as $index => $tag){
            $data = $tag['id'].",".$tag['type'].",".$tag['name'];
            $redis->zAdd(Sight_Keys::getSightShowTagIds($sightId),$index,$data);  
        }
        return $arrCommonTag;
    }
}