<?php
/**
 * 景点编辑情况
 * @author fanyy
 *
 */
class SituationAction extends Yaf_Action_Abstract {
    public function execute() { 
    	$statusTypeArray=Topic_Type_Status::$names;
    	$this->getView()->assign('statusTypeArray', $statusTypeArray);

    	//处理传递过来的城市
        $city_id  = isset($_REQUEST['city_id'])?intval($_REQUEST['city_id']):'';
        if($city_id!=''){ 
           $city=City_Api::getCityById($city_id);  
           $this->getView()->assign('city', $city);
        }
    }
}
