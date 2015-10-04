<?php
class Sight_Logic_Tag extends Base_Logic{
    
    protected $_logicTopic;
    
    protected $_logicTag;
    
    public function __construct(){
        $this->_logicTopic = new Topic_Logic_Topic();
        $this->_logicTag   = new Tag_Logic_Tag();
    }
    
    public function getTagsBySight($sightId){ 
        $arrRet        = array();
        $arrCommonTag  = array();
        $arrGeneralTag = array();       
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
        $arrTopicIds = explode(",",$strTopicIds);
        foreach ($arrTopicIds as $id){
            $arrTags = $this->_logicTag->getTopicTags($id);
            foreach ($arrTags as $val){
                $temp = array();
                $tag  = $this->_logicTag->getTagByName($val);
                if($tag['type'] == Tag_Type_Tag::CLASSIFY || $tag['type'] == Tag_Type_Tag::GENERAL){
                    $temp['id']     = strval($tag['id']);
                    $temp['type']   = strval($tag['type']);
                    $temp['name']   = $val;  
                    if(!in_array($temp,$arrCommonTag)){
                        $arrCommonTag[] = $temp;
                    }
                }                    
            }
        }
        
        //判断有无视频,书籍,而增加相应标签
        $arrCommonTag[] = array('id' => 'wiki','type' => strval(Tag_Type_Tag::NORMAL), 'name' => '景观');
        
        $book  = Book_Api::getJdBooks($sightId, 1, 1);
        $video = Video_Api::getVideos($sightId, 1, 1);
        if(!empty($book['list'])){
            $arrCommonTag[] = array('id' => 'book','type' => strval(Tag_Type_Tag::NORMAL), 'name' => '书籍');
        }
        if(!empty($video['list'])){
            $arrCommonTag[] = array('id' => 'video','type' => strval(Tag_Type_Tag::NORMAL), 'name' => '视频');
        }        
        return array_merge($arrCommonTag,$arrGeneralTag);
    }
}