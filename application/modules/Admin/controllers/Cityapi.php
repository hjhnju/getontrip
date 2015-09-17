<?php
/**
 * 城市管理相关操作
 */
class CityapiController extends Base_Controller_Api{
     
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

        $pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:"";
        
        if(!empty($pid)){
           $arrInfo=array('pid' => $pid); 
        } else{
             $arrInfo = array();
        }
        $List =City_Api::queryCity($arrInfo,$page, $pageSize);

        $tmpList=$List['list'];
        //处理状态值 
        for($i=0; $i<count($tmpList); $i++) { 
            $tmpList[$i]["statusName"] = City_Type_Status::getTypeName($tmpList[$i]["status"]);  
        }
        $List['list']=$tmpList;
    
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
		$this->ajax($retList);
         
    }


    /**
     * 保存城市坐标信息
     * @return [type] [description]
     */
    public function saveAction()
    {   
      //判断是否有ID
       $cityId=isset($_POST['id'])?$_POST['id']:''; 
       $arrInfo = array(
            'x' => $_POST['x'], 
            'y'  => $_POST['y'] 
       );
       $bRet = City_Api::editCity($cityId,$arrInfo); 

        if($bRet){
            return $this->ajax();
        }
        return $this->ajaxError(); 

    }

    
    /**
     * 获取省份信息  用于下拉框
     * @return [type] [description]
     */
    public function getProvinceListAction(){ 
        $str=$_REQUEST['query'];
        //最大值 PHP_INT_MAX  
        $List =City_Api::queryProvincePrefix($str,1,PHP_INT_MAX);
        return $this->ajax($List["list"]);  
    }
    

    /**
     * 获取城市信息  用于下拉框
     * @return [type] [description]
     */
    public function getCityListAction(){ 
        $str=$_REQUEST['query'];
        //最大值 PHP_INT_MAX  
        $List =City_Api::queryCityPrefix($str,1,PHP_INT_MAX,array('status'=>City_Type_Status::PUBLISHED)); 

        return $this->ajax($List["list"]);  
    }

    /*
      发布  或取消发布操作
    */
    public function publishAction(){
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       } 
       $bRet=City_Api::editCity($postid,array('status'=>$this->getStatusByActionStr($_REQUEST['action'])));
       if($bRet){ 
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }

    /**
     * 获取保存的状态
     * @param  [type] $action [description]
     * @return [type]         [description]
    */
    public function getStatusByActionStr($action){
        switch ($action) {
         case 'NOTPUBLISHED':
           $status = Theme_Type_Status::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = Theme_Type_Status::PUBLISHED;
           break;
         default:
           $status = Theme_Type_Status::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }
}