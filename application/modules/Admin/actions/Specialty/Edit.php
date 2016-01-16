<?php
/**
 * 编辑视频
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() { 

        $action = isset($_REQUEST['action'])?$_REQUEST['action']:'add';  

        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        
        $products = Specialty_Api::getProductList(1, PHP_INT_MAX,array('status' => Specialty_Type_Product::PUBLISHED));
        
        if($postid==''){
            $this->getView()->assign('post', '');
        }
        else{ 
           $postInfo  = Specialty_Api::getSpecialtyInfo($postid); 
           
           //处理状态值
           $postInfo["statusName"]=Specialty_Type_Status::getTypeName($postInfo['status']);
           
           //处理所选景点
           $logicDest = new Destination_Logic_Specialty();
           $arrDest   = $logicDest->getDestList(1, PHP_INT_MAX,array('specialty_id' => $postid));
           foreach ($arrDest['list'] as $val){
               if($val['destination_type'] == Destination_Type_Type::SIGHT){
                   $objSight = Sight_Api::getSightById($val['destination_id']);
                   $sight['id']   = $objSight['id'];
                   $sight['name'] = $objSight['name'];
                   $postInfo['sights'][] = $sight;
               }elseif($val['destination_type'] == Destination_Type_Type::CITY){
                   $objCity = City_Api::getCityById($val['destination_id']);
                   $city['id']   = $objCity['id'];
                   $city['name'] = $objCity['name'];
                   $postInfo['citys'][] = $city;
               }
           }
           
           foreach ($products['list'] as $key => $val){
               $objProduct = new Specialty_Object_Product();
               $objProduct->fetch(array('id' => $val['id'],'specialty_id' => $postid));
               if(!empty($objProduct->id)){
                   $products['list'][$key]['selected'] = 1;
               }
           }
           
           $this->getView()->assign('post', $postInfo);
        }
 
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 

        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
        $this->_view->assign('sightList', isset($postInfo['sights'])?$postInfo['sights']:array());
        $this->_view->assign('cityList', isset($postInfo['citys'])?$postInfo['citys']:array());

        $this->getView()->assign('products', $products['list']);
    }
}
