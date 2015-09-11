<?php
/**
 * 话题相关操作
 * author:fyy
 */
class  ThemeapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
     
    /**
     * list
     *  
     */    
    public function listAction(){  

        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        
          
        $List = Theme_Api::searchTheme($arrParam,$page,$pageSize);

        //处理状态值 
        $tmpList = $List['list'];
        
        for($i=0; $i<count($tmpList); $i++) { 
            $tmpList[$i]["statusName"] = Theme_Type_Status::getTypeName($tmpList[$i]["status"]);  
        }  
        $List['list']=$tmpList;
        
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total'];  
        $retList['data'] = $List['list'];  
		    return $this->ajax($retList);
         
    }

    /**
     * 编辑
     * @return [type] [description]
     */
    public function saveAction()
    {   
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       } 
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);
       
       $bRet=Theme_Api::editTheme($postid,$_REQUEST);
       if($bRet){
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }
    
   /**
    * 添加
    */
    public function addAction(){  
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);   
       $bRet=Theme_Api::addTheme($_REQUEST); 
       if(!empty($bRet)){
            return $this->ajax($bRet);
       } 
       return $this->ajaxError();
    }
 

    /**
    * 删除
    */
    public function delAction(){
        //判断是否有ID
        $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;  
        $bRet =Theme_Api::delTheme($postid);
        if($bRet){
            return $this->ajax($postid);
        }
        return $this->ajaxError();
    }
    
    /*
      发布  或取消发布操作
    */
    public function publishAction(){
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       } 
       $bRet=Theme_Api::editTheme($postid,array('status'=>$this->getStatusByActionStr($_REQUEST['action'])));
       if($bRet){ 
            return $this->ajax();
       }
       return $this->ajaxError(); 
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
            $bRet=Theme_Api::editTheme($postid,$params);
            if($bRet){
               return $this->ajax($ret); 
            }
            return $this->ajaxError('');
            return $this->ajaxError('400','修改话题的图片hash错误');  
          }
          return $this->ajax($ret); 
        }
        return $this->ajaxError('401','裁剪图片错误错误');  
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