<?php
/**
 * 词条列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() { 
    	$statusTypeArray=Keyword_Type_Status::$names;
      $statusTypeArray=array_reverse($statusTypeArray,true);
    	$this->getView()->assign('statusTypeArray', $statusTypeArray);
    	
    	$levelTypeArray=Keyword_Type_Level::$names;
    	$this->getView()->assign('levelTypeArray', $levelTypeArray);

        //处理传递过来的景点
          $sight_id  = isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):'';
          if($sight_id!=''){
             $sight=Sight_Api::getSightById($sight_id); 
             $this->getView()->assign('sight', $sight);
          } 
    }
}
