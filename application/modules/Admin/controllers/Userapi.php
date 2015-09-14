<?php
/**
 * 城市管理相关操作
 */
class UserapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
 
    /**
     * list
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        //
        $start =isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page = ($start/$pageSize)+1; 
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();

        $List =User_Api::getUserList($page,$pageSize,$arrParam);

        $tmpList=$List['list'];

        //添加城市名称
        $cityArray=array(); 
        foreach($tmpList as $key=>$item){   
           $city_id=$item['city_id']; 
            if (!array_key_exists($city_id,$cityArray)) {  
                  //根据ID查找城市名称
                  $cityInfo = City_Api::getCityById($item['city_id']); 
                  $tmpList[$key]['city_name'] = isset($cityInfo['name'])?$cityInfo['name']:'-'; 
                  //添加到数组
                  $cityArray[$city_id]=$tmpList[$key]['city_name'];  
            }
            else{
                 
                 $tmpList[$key]['city_name']  = $cityArray[$item['city_id']];
            }
        } 

        $List['list']=$tmpList;
         
    
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
        return $this->ajax($retList);
         
    }
 
}