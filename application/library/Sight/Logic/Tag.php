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
            $arrGeneralTag[$key]['id']   = trim($val['id']);
            $arrGeneralTag[$key]['name'] = trim($val['name']);
        }
       
        $strTopicIds = $this->_logicTopic->getTopicIdBySight($sightId);
        $arrTopicIds = explode(",",$strTopicIds);
        foreach ($arrTopicIds as $id){
            $arrTags = $this->_logicTag->getTopicTags($id);
            foreach ($arrTags as $val){
                $tag = $this->_logicTag->getTagByName($val);
                $temp['id']     = $tag['id'];
                $temp['name']   = $val;
                if(!in_array($temp,$arrCommonTag)){
                    $arrCommonTag[] = $temp;
                }
            }
        }
        
        //判断有无视频,书籍,而增加相应标签
        $arrCommonTag[] = array('id' => 'wiki','name' => '景观');
        
        $book  = Book_Api::getJdBooks($sightId, 1, 1);
        $video = Video_Api::getVideos($sightId, 1, 1);
        if(!empty($book['list'])){
            $arrCommonTag[] = array('id' => 'book','name' => '书籍');
        }
        if(!empty($video['list'])){
            $arrCommonTag[] = array('id' => 'video','name' => '视频');
        }
        $arrRet['common']  = $arrCommonTag;
        $arrRet['general'] = $arrGeneralTag;        
        return $arrRet;
    }
}