<?php
/**
 * 编辑景点
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
    	$actionArray = array(
               "add"=>"新建",
               "edit"=>"编辑",
               "view"=>"查看"
    	);
        $levelArray = array(
              "5A","4A","3A","2A","1A"
        );
        $action = isset($_REQUEST['action'])?$_REQUEST['action']:'add';  

        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if($postid==''){
            $this->getView()->assign('post', '');
        }
        $postInfo  = Sight_Api::getSightById($postid); 
        if($postInfo){ 
           $postInfo=(array)$postInfo;
           //获取城市名称
           $cityInfo=City_Api::getCityById($postInfo["city_id"]);
           $postInfo["city_name"]=$cityInfo["name"];
           $postInfo["level"]=trim($postInfo["level"]);
           $this->getView()->assign('post', $postInfo);
        }

        $this->_view->assign('action', $actionArray[$action]);
        $this->_view->assign('levelArray', $levelArray);
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 
        
      
    }
}
