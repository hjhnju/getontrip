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
        
        $shops = Food_Api::getShopList(1, PHP_INT_MAX,array('status' => Food_Type_Shop::PUBLISHED,'food_id'=>''));
        if(!empty($postid)){
            $tmp = Food_Api::getShopList(1, PHP_INT_MAX,array('status' => Food_Type_Shop::PUBLISHED,'food_id'=>$postid));
            $shops['list'] = array_merge($shops['list'],$tmp['list']);
        }
        
        
        
        if($postid==''){
            $this->getView()->assign('post', '');
        }
        else{ 
           $postInfo  = Food_Api::getFoodInfo($postid); 
           
           //处理状态值
           $postInfo["statusName"]=Food_Type_Status::getTypeName($postInfo['status']);
           
           //处理所选景点
           $logicDest = new Destination_Logic_Food();
           $arrDest   = $logicDest->getDestList(1, PHP_INT_MAX,array('food_id' => $postid));
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
           
           foreach ($shops['list'] as $key => $val){
               $objShop = new Food_Object_Shop();
               $objShop->fetch(array('id' => $val['id'],'food_id' => $postid));
               if(!empty($objShop->id)){
                   $shops['list'][$key]['selected'] = 1;
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

        $this->getView()->assign('shops', $shops['list']);
    }
}
