<?php
/**
 * 城市管理
 * @author fyy
 *
 */
class HotAction extends Yaf_Action_Abstract {
    
    public function execute() {        
        $arrCity = City_Api::queryCity(array('status' => City_Type_Status::PUBLISHED,'is_china'=>1), 1, PHP_INT_MAX);
        foreach ($arrCity['list'] as $key => $val){
            $arrRet[$key]['id']   = $val['id'];
            $arrRet[$key]['is_china']   = 1;
            $arrRet[$key]['name'] = $val['name'];
        }
        $this->getView()->assign('city_inner',$arrRet);
        
        $arrCity = City_Api::queryCity(array('status' => City_Type_Status::PUBLISHED), 1, PHP_INT_MAX);
        $arrRet = array();
        foreach ($arrCity['list'] as $key => $val){
            if(intval($val['is_china']) !==1){
                $arrRet[$key]['id']   = $val['id'];
                $arrRet[$key]['is_china']   = 0;
                $arrRet[$key]['name'] = $val['name'];
            }
        }
        $this->getView()->assign('city_outer',$arrRet);
    }
}
