<?php
/**
 * 收藏列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() {  
        $obj_id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
        $tableName= isset($_REQUEST['table'])?strtolower($_REQUEST['table']):'';
        $typeArray=Collect_Type::$names;
        $typeArray=array_reverse($typeArray,true);
 
        switch ($tableName) {
            case 'content':
                $type =Collect_Type::COTENT;
                $typeName = '内容收藏';
                break;
            case 'book':
                $type = Collect_Type::BOOK;
                $typeName = '书籍收藏';
                break;
            case 'sight':
                $type = Collect_Type::SIGHT;
                $typeName = '景点收藏';
                break;
            case 'city':
                $type = Collect_Type::CITY;
                $typeName = '城市收藏';
                break;
            case 'topic':
                $type = Collect_Type::TOPIC;
                $typeName = '话题收藏';
                break;
            default:
                $type = '';
                $typeName = '收藏';
                break;
        } 

        $this->getView()->assign('typeArray', $typeArray);
        $this->getView()->assign('type', $type);
        $this->getView()->assign('typeName', $typeName); 
        $this->getView()->assign('obj_id', $obj_id);
 
    }
}
