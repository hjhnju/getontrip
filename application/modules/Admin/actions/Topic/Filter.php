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
    	
    	$sourceList = Source_Api::listSource(1, PHP_INT_MAX);
        $sourceList=$sourceList['list']; 

        $keywordsList = Source_Api::listSource(1, PHP_INT_MAX);
        $keywordsList=$keywordsList['list']; 

    	$this->getView()->assign('tagList', $tagList);
    	$this->getView()->assign('sourceList', $sourceList);
    	$this->getView()->assign('keywordsList', $keywordsList);
    }
}
