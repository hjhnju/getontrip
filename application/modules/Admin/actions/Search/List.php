<?php
/**
 * 搜索标签列表
 * @author huwei
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() { 
        $list = Search_Api::listLabel(1, PHP_INT_MAX);
        $this->getView()->assign('sightList', $list['list']);
        
        $arrCity = City_Api::queryCity(array('status' => City_Type_Status::PUBLISHED), 1, PHP_INT_MAX);
        foreach ($arrCity['list'] as $key => $val){
            $arrRet[$key]['id']   = $val['id'];
            $arrRet[$key]['name'] = $val['name'];
        }
        $this->getView()->assign('city',$arrRet);
            
        $arrSight = Sight_Api::getSightList(1, PHP_INT_MAX, Sight_Type_Status::PUBLISHED);
        foreach ($arrSight['list'] as $key => $val){
            $arrRet[$key]['id']   = $val['id'];
            $arrRet[$key]['name'] = $val['name'];
        }
        $this->getView()->assign('sight',$arrRet);
        $this->getView()->assign('formFilename','searchlabel_image.jpg'); 
    }
}
