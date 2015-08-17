<?php
/**
 * 话题列表
 * @author fanyy
 *
 */
class FilterAction extends Yaf_Action_Abstract {
    public function execute() { 

    	$tagList = Tag_Api::getTagList(1, PHP_INT_MAX);
        $tagList=$tagList['list'];
    	
    	/*  $sourceList = Source_Api::searchSource(array("type"=>2),1, PHP_INT_MAX); 
        $sourceList=$sourceList['list']; 
        //过滤掉url为空的情况 
        for($i=0;$i<count($sourceList);$i++){
           if(empty($sourceList[$i]['url'])){ 
               array_splice($sourceList, $i, 1);
           }
        }  */

        $sourceList =  Source_Api::getHotSource();

        $keywordsList = Source_Api::listSource(1, PHP_INT_MAX);
        $keywordsList = $keywordsList['list']; 

        //处理传递过来的景点
        $sight_id  = isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):'';
        if($sight_id!=''){
           $sight=Sight_Api::getSightById($sight_id); 
           $this->getView()->assign('sight', $sight);
        } 

    	$this->getView()->assign('tagList', $tagList);
    	$this->getView()->assign('sourceList', $sourceList);
    	$this->getView()->assign('keywordsList', $keywordsList);
    }
}
