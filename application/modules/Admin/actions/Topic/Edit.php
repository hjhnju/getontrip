<?php
/**
 * 新建编辑 话题
 * @author fanyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    public function execute() {

    	$postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if(empty($postid)){
            $this->getView()->assign('post', '');
        }

        //获取所有标签
        $tagList = Tag_Api::getTagList(1, PHP_INT_MAX);
        $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
    	$this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
    	$this->getView()->assign('tagList', $tagList['list']);
    }
}
