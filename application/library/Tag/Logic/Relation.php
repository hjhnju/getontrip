<?php
class Tag_Logic_Relation extends Base_Logic{
    
    protected  $_logicTag;
    
    public function __construct(){
        $this->_logicTag = new Tag_Logic_Tag();
    }
    
    public function groupByTop($arrTags){
        $arrTemp   = array();
        $arrRet    = array();
        $limit_num = Base_Config::getConfig('showtag')->firstnum;
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
                if(!isset($tag['name'])){
                    continue;
                }
                $tmp['id']   = strval($key);
                //$tmp['type'] = strval(Tag_Type_Tag::TOP_CLASS);
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
        $ret     = true;
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
    
    public function getAllClassTags($page, $pageSize){
        $listTag = new Tag_List_Tag();
        $listTag->setFilter(array('type' => Tag_Type_Tag::TOP_CLASS));
        $listTag->setFields(array('id','name','type'));
        $listTag->setPage($page);
        $listTag->setPagesize(PHP_INT_MAX);
        $arrTag = $listTag->toArray();
        foreach ($arrTag['list'] as $key => $val){
             $sub = Tag_Api::getTagRelation($val['id'], 1, PHP_INT_MAX);
             foreach ($sub['list'] as $val){
                 $temp['id']   = $val['classifytag_id'];
                 $tag          = Tag_Api::getTagInfo($temp['id']);
                 $temp['name'] = $tag['name'];
                 $arrTag['list'][$key]['subtags'][] = $temp;                 
             }
        }
        return $arrTag;
    }
}