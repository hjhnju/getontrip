<?php
/**
 * 视频列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() { 
        $statusArray=Video_Type_Status::$names;
        $statusArray=array_reverse($statusArray,true);
        $this->getView()->assign('statusArray', $statusArray);

        $typeArray=Video_Type_Type::$names;
        $typeArray=array_reverse($typeArray,true);
        $this->getView()->assign('typeArray', $typeArray); 
        
        //处理传递过来的景点
        $sight_id  = isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):'';
        if($sight_id!=''){
           $sight=Sight_Api::getSightById($sight_id); 
           $this->getView()->assign('sight', $sight);
        } 
    }
}
