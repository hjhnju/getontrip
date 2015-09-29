<?php
/**
 * 编辑搜索标签
 * @author huwei
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $arrRet = array();
      	$type   = isset($_REQUEST['type'])?intval($_REQUEST['type']):Search_Type_Label::CITY;
      	if($type == Search_Type_Label::CITY){
      	    $arrCity = City_Api::queryCity(array('status' => City_Type_Status::PUBLISHED), 1, PHP_INT_MAX);
      	    foreach ($arrCity['list'] as $key => $val){
      	        $arrRet[$key]['id']   = $val['id'];
      	        $arrRet[$key]['name'] = $val['name'];
      	    }
      	}else{
      	    $arrSight = Sight_Api::getSightList(1, PHP_INT_MAX, Sight_Type_Status::PUBLISHED);
      	    foreach ($arrSight['list'] as $key => $val){
      	        $arrRet[$key]['id']   = $val['id'];
      	        $arrRet[$key]['name'] = $val['name'];
      	    }
      	}
        $this->_view->assign('data', $arrRet);
        $this->_view->assign('type',$type);
    }
}
