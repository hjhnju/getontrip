<?php
class Tag_Logic_Relation extends Base_Logic{
    
    protected  $_logicTag;
    
    public function __construct(){
        $this->_logicTag = new Tag_Logic_Tag();
    }
    
    public function groupByTop($arrTags){
        $arrTemp   = array();
        $arrRet    = array();
        $limit_num = Base_Config::getConfig('showtag')->topicnum;
        foreach ($arrTags as $tag){
            $objTagRelation = new Tag_Object_Relation();
            $objTagRelation->fetch(array('classifytag_id' => $tag));
            if(isset($arrRet[$objTagRelation->toptagId])){
                $arrTemp[$objTagRelation->toptagId] += 1;
            }else{
                $arrTemp[$objTagRelation->toptagId]  = 1;
            }
        }
        foreach ($arrTemp as $key => $val){
            if($val >= $limit_num){
                $tmp  = array();
                $tag  = $this->_logicTag->getTagById($key);
                $tmp['id']   = $key;
                $tmp['type'] = strval(Tag_Type_Tag::TOP_CLASS);
                $tmp['name'] = trim($tag['name']);
                $arrRet[]  = $tmp;
            }
        }
        return $arrRet;
    }
    
    public function getTagRelation($topTagId, $page, $pageSize){
        $listTagRelation = new Tag_List_Relation();
        $listTagRelation->setFilter(array('toptag_id' => $topTagId));
        $listTagRelation->setPage($page);
        $listTagRelation->setPagesize($pageSize);
        return $listTagRelation->toArray();
    }
    
    public function editTagRelation($topTagId, $arrTagIds){
        $arrTags = $this->getTagRelation($topTagId, 1, PHP_INT_MAX);
        foreach ($arrTags['list'] as $val){
            $objTagRelation = new Tag_Object_Relation();
            $objTagRelation->fetch(array('id' => $val['id']));
            $objTagRelation->remove();
        }
        
        foreach ($arrTagIds as $tagId){
            $objTagRelation = new Tag_Object_Relation();
            $objTagRelation->toptagId = $topTagId;
            $objTagRelation->classifytagId = $tagId;
            $ret = $objTagRelation->save();
        }
        return $ret;
    }
}