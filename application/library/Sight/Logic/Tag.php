<?php
class Sight_Logic_Tag extends Base_Logic{
    
    protected $_logicTopic;
    
    protected $_logicTag;
    
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
    }
    
    public function getTagsBySight($sightId){ 
        $arrRet        = array();
        $arrCommonTag  = array();
        $arrGeneralTag = array(); 
        $arrTopicIds   = array();
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
                if($tag['type'] == Tag_Type_Tag::CLASSIFY || $tag['type'] == Tag_Type_Tag::GENERAL){
                    $temp['id']     = strval($tag['id']);
                    $temp['type']   = strval($tag['type']);
                    $temp['name']   = $val;  
                    if(!in_array($temp,$arrCommonTag) && !empty($temp)){
                        $arrCommonTag[] = $temp;
                    }
                }                 
            }
        }
        //判断有无视频,书籍,而增加相应标签
        $arrCommonTag[] = array('id' => strval(self::STR_LANDSCAPE),'type' => strval(self::LANDSCAPE), 'name' => '景观');
        
        $book  = Book_Api::getJdBooks($sightId, 1, 1);
        $video = Video_Api::getVideos($sightId, 1, 1);
        if(!empty($book['list'])){
            $arrCommonTag[] = array('id' => strval(self::STR_BOOK),'type' => strval(self::BOOK), 'name' => '书籍');
        }
        if(!empty($video['list'])){
            $arrCommonTag[] = array('id' => strval(self::STR_VIDEO),'type' => strval(self::VIDEO), 'name' => '视频');
        }        
        return array_merge($arrCommonTag,$arrGeneralTag);
    }
}