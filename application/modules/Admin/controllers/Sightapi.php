<?php
/**
 * 景点相关操作
 */
class  SightapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
     
    /**
     * 标签list
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $arrInfo =isset($_REQUEST['params'])?$_REQUEST['params']:array();
        
     
        $List =Sight_Api::querySights($arrInfo,$page, $pageSize);
    
    
        
        //添加城市名称
        $cityArray=array(); 
        $tmpList=$List['list'];
        foreach($tmpList as $key=>$item){   
           $city_id=$item['city_id']; 
            if (!array_key_exists($city_id,$cityArray)) {  
                  //根据ID查找城市名称
                  $cityInfo = City_Api::getCityById($item['city_id']);
                  $tmpList[$key]['city_name'] = $cityInfo['name']; 
                  //添加到数组
                  $cityArray[$city_id]=$cityInfo['name'];  
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

    public function saveAction()
    {   
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
        if($postid <= 0){
            $this->ajaxError();
       }
       
       $bRet=Sight_Api::editSight($postid,$_REQUEST);  
       if($bRet){
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }
    
   /**
    * 添加景点信息
    */
    public function addAction(){ 
       $bRet=Sight_Api::addSight($_REQUEST);   
       if($bRet){
            return $this->ajax();
       } 
       return $this->ajaxError();
    }

    public function delAction(){
        //判断是否有ID
        $id=isset($_REQUEST['id'])?$_REQUEST['id']:''; 
        $bRet =Sight_Api::delSight($id);
        if($bRet){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
    
}