<?php
/**
 * 列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() {  
    	$obj_id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
    	$tableName= isset($_REQUEST['table'])?$_REQUEST['table']:'';
    	$statusArray=Comment_Type_Status::$names;
        $statusArray=array_reverse($statusArray,true);

        $typeArray=Comment_Type_Type::$names;
        $typeArray=array_reverse($typeArray,true);

        switch ($tableName) {
        	case 'topic':
        		$type=Comment_Type_Type::TOPIC;
        		$typeName=Comment_Type_Type::getTypeName(Comment_Type_Type::TOPIC);
        		break;
        	case 'book':
        		$type=Comment_Type_Type::BOOK;
        		$typeName=Comment_Type_Type::getTypeName(Comment_Type_Type::BOOK);
        		break;
        	case 'video':
        		$type=Comment_Type_Type::VIDEO;
        		$typeName=Comment_Type_Type::getTypeName(Comment_Type_Type::VIDEO);
        		break;
        	case 'wiki':
        		$type=Comment_Type_Type::WIKI;
        		$typeName=Comment_Type_Type::getTypeName(Comment_Type_Type::WIKI);
        		break;
        	default:
        		$type = '';
        		$typeName = '评论';
        		break;
        } 

        $this->getView()->assign('statusArray', $statusArray);
    	$this->getView()->assign('typeArray', $typeArray);
    	$this->getView()->assign('type', $type);
    	$this->getView()->assign('typeName', $typeName); 
    	$this->getView()->assign('obj_id', $obj_id);
 
    }
}
