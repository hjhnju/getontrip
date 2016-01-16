<?php
/**
 * 店铺管理相关操作
 * author:fanyy
 */
class ShopapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
    /*
     店铺列表页
     */
    public function listAction(){
            
         //第一条数据的起始位置，比如0代表第一条数据
         $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
         $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX;

         $page=($start/$pageSize)+1;
         
        
         $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();

         if (isset($arrParam['action'])) {
            $arrParam['status'] = $this->getStatusByActionStr($arrParam['action']);
         }
        
         $List =Food_Api::getShopList($page,$pageSize,$arrParam);

         $retList['recordsTotal']    = $List['total'];
         $retList['recordsFiltered'] = $List['total'];
         $retList['data'] = $List['list']; 
         return $this->ajax($retList); 
    }
 

    /**
     * 添加店铺
     */
    function addAction(){
         
        $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
        
        $dbRet =Food_Api::addShop($_REQUEST);
        if (!empty($dbRet)) {
            return $this->ajax($dbRet);
        }
        return $this->ajaxError();
    }

     /**
     * 编辑保存店铺
     */
    function saveAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            return $this->ajaxError();
        }
        $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
       
        $dbRet = Food_Api::editShop($id, $_REQUEST);

        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

     /**
     * 删除店铺
     */
    function delAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if(empty($id)){
            return $this->ajaxError();
        }
        $dbRet = Food_Api::delShop($id);
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

    /*
      修改权重
    */
    public function changeWeightAction()
    {
         
        $sightId =isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        $id =isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        $to =isset($_REQUEST['to'])?intval($_REQUEST['to']):'';
        if(empty($sightId)||empty($id)||empty($to)){
            return $this->ajaxError();
        }
        $dbRet =  Food_Api::changeWeight($sightId, $id, $to);
 
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

    /*
      批量修改状态
     */
    public function changeStatusAction(){
       $idArray = isset($_REQUEST['idArray'])? $_REQUEST['idArray'] : array(); 
 
       $status = $this->getStatusByActionStr($_REQUEST['action']);
       for ($i=0; $i < count($idArray); $i++) { 
           $postid = intval($idArray[$i]);
           $bRet = Food_Api::editShop($postid,array('status'=>$status)); 
           if(!$bRet){  
              return $this->ajaxError('501','ID:【'.$postid.'】修改状态失败');
           }
       }
       return $this->ajax();
   }
  

    /**
     * 裁剪店铺背景图片
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
            $bRet=Food_Api::editShop($postid,$params);
            if($bRet){
               return $this->ajax($ret); 
            } 
            return $this->ajaxError('400','修改图片hash错误');  
          }
          return $this->ajax($ret); 
        }
        return $this->ajaxError('401','裁剪图片错误');  
    }

   /**
     * 获取保存的状态
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    public function getStatusByActionStr($action){
        switch ($action) {
         case 'NOTPUBLISHED':
           $status = Food_Type_Shop::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = Food_Type_Shop::PUBLISHED;
           break;
        case 'BLACKLIST':
           $status = Food_Type_Shop::BLACKLIST;
           break;
         default:
           $status = Food_Type_Shop::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }
}