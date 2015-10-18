<?php
/**
 * 话题列表
 * @author fanyy
 *
 */
class FilterAction extends Yaf_Action_Abstract {
    public function execute() { 

        //热门搜索来源
        $hotSourceList =  Source_Api::getHotSource(); 
        $this->getView()->assign('hotSourceList', $hotSourceList);

        //搜索来源，按照分组
        $sourceList= Source_Api::listType(1,PHP_INT_MAX,array());
        $this->getView()->assign('sourceList', $sourceList['list']);

        //所有标签
    	$tagList = Tag_Api::getTagList(1, PHP_INT_MAX);
        $tagList=$tagList['list'];
        $this->getView()->assign('tagList', $tagList);

        //获取普通标签
        $normalTag = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::NORMAL));
        $normalTag=$normalTag['list'];
        $this->getView()->assign('normalTag', $normalTag);

        //获取通用标签
        $generalTag = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::GENERAL));
        $generalTag = $generalTag['list']; 
        $this->getView()->assign('generalTag',$generalTag); 

        //获取分类标签
        $classifyTag = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::CLASSIFY));
        $classifyTag = $classifyTag['list'];
        $this->getView()->assign('classifyTag',$classifyTag);
    	
    	/*  $sourceList = Source_Api::searchSource(array("type"=>2),1, PHP_INT_MAX); 
        $sourceList=$sourceList['list']; 
        //过滤掉url为空的情况 
        for($i=0;$i<count($sourceList);$i++){
           if(empty($sourceList[$i]['url'])){ 
               array_splice($sourceList, $i, 1);
           }
        }  */
        
        
 
        /*//
        $keywordsList = Source_Api::listSource(1, PHP_INT_MAX);
        $keywordsList = $keywordsList['list']; 
        $this->getView()->assign('keywordsList', $keywordsList);*/

        //处理传递过来的景点
        $sight_id  = isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):'';
        if($sight_id!=''){
           $sight=Sight_Api::getSightById($sight_id); 
           $this->getView()->assign('sight', $sight);
        } 

    }
}
