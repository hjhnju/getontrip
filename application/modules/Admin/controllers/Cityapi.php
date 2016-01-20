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

        $arrInfo = isset($_REQUEST['params'])?$_REQUEST['params']: array();
       /* $pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:"";
        $type = isset($_REQUEST['raw']);*/
        
        if(isset($_REQUEST['is_china'])){
             $arrInfo = array_merge($arrInfo,array('is_china' => intval($_REQUEST['is_china']))); 
        }
        $List =City_Api::queryCity($arrInfo,$page, $pageSize);
        
        foreach ($List['list'] as $key => $val){
            $List['list'][$key]['statusName'] = City_Type_Status::getTypeName($val["status"]); 
        }
    
        $retList['recordsFiltered'] = $List['total'];
        $retList['recordsTotal']    = $List['total']; 
        $retList['data']            = $List['list'];
 
		    return $this->ajax($retList);
         
    }
    
    public function listhotAction(){
        $arrRet  = array();
        $china   = isset($_REQUEST['is_china'])?intval($_REQUEST['is_china']): 0;
        $List    = City_Api::getHotCityIds();
        if(!empty($china)){
            $List = isset($List['inland'])?$List['inland']:array();
        }else{
            $List = isset($List['outer'])?$List['outer']:array();
        }
    
        foreach ($List as $key => $val){
            $city = City_Api::getCityById($val);
            $arrRet[$key]['id']          = $val;
            $arrRet[$key]['name']        = $city['name'];
            $arrRet[$key]['is_china']    = $china;
            $arrRet[$key]['create_time'] = $city['create_time'];
            $arrRet[$key]['update_time'] = $city['update_time'];
        }
    
        $retList['recordsFiltered'] = count($arrRet);
        $retList['recordsTotal']    = count($arrRet);
        $retList['data']            = $arrRet;
    
        return $this->ajax($retList);
         
    }
    public function delHotAction(){
        $id = isset($_REQUEST['cityId'])?intval($_REQUEST['cityId']):'';
        if(!empty($id)){
            $ret = City_Api::delHotCity($id);
        }
        return $this->ajax();
    }
    
    public function addHotAction(){
        $arrIds  = isset($_REQUEST['city_id_inner'])?$_REQUEST['city_id_inner']:$_REQUEST['city_id_outer'];
        foreach ($arrIds as $id){
            $ret = City_Api::setHotCity($id);
        }
        return $this->ajax($ret);;
    }


    /**
     * 编辑城市信息
     * @return [type] [description]
     */
    public function saveAction()
    {   
       //判断是否有ID
       $cityId=isset($_REQUEST['id'])?$_REQUEST['id']:'';  
       if(empty($cityId)){
          return $this->ajaxError(); 
       }
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);
       $bRet = City_Api::editCity($cityId,$_REQUEST); 

       if($bRet){
            return $this->ajax();
       }
       return $this->ajaxError(); 

    }

     /**
     * 添加城市信息
     * @return [type] [description]
     */
    public function addAction()
    {   
       //判断是否有ID 
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);
       
       $bRet = City_Api::addCity($_REQUEST); 

       if($bRet){
            return $this->ajax();
       }
       return $this->ajaxError(); 

    }

    /**
     * 获取省份信息  用于下拉框
     * @return [type] [description]
    */
    public function getcountryListAction(){
      $str=$_REQUEST['query'];
        //最大值 PHP_INT_MAX  
        $List =City_Api::queryCountryPrefix($str,1,PHP_INT_MAX);
        return $this->ajax($List["list"]);  
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
        $List =City_Api::queryCityPrefix($str,1,PHP_INT_MAX); 

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
           $status = City_Type_Status::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = City_Type_Status::PUBLISHED;
           break;
         default:
           $status = City_Type_Status::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }
    
    public function situationListAction(){
        $arrInfo  = array();
        $start    = isset($_REQUEST['start'])?$_REQUEST['start']:0;
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX;
        $page     = ($start/$pageSize)+1;
        $city_id  = isset($_REQUEST['city_id'])?intval($_REQUEST['city_id']):'';
        $user_id  = isset($_REQUEST['create_user'])?intval($_REQUEST['create_user']):'';
        if(!empty($city_id)){
            $arrInfo = array_merge($arrInfo,array('id' => $city_id));
        }
        if(!empty($user_id)){
            $arrInfo = array_merge($arrInfo,array('create_user' => $user_id));
        }
        $arrInfo = array_merge(array('status' => City_Type_Status::PUBLISHED,$arrInfo));
        $List = Tongji_Api::city($arrInfo, $page, $pageSize);
        
        $retList['recordsFiltered'] = $List['total'];
        $retList['recordsTotal']    = $List['total'];
        $retList['data']            = $List['list'];
        
        $this->ajax($retList);
    }



    /**
     * 裁剪背景图片
     * @return [type] [description]
    */
    public function cropPicAction(){
        $postid=isset($_REQUEST['id'])?intval($_REQUEST['id']):''; 
        $oldhash=$_REQUEST['image'];
        $x=$_REQUEST['x'];
        $y=$_REQUEST['y']; 
        $width=$_REQUEST['width'];
        $height=$_REQUEST['height']; 
        $ret=Base_Image::cropPic($oldhash,$x,$y,$width,$height); 
        if($ret){
          if(!empty($postid)){
            $params = array('image'=>$ret['image']);
            //修改话题的图片hash
            $bRet = City_Api::editCity($postid,$params);
            if($bRet){
               return $this->ajax($ret); 
            } 
            return $this->ajaxError('400','修改话题的图片hash错误');  
          }
          return $this->ajax($ret); 
        }
        return $this->ajaxError('401','裁剪图片错误错误');  
    }
}