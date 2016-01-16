<?php
/**
 * 商品管理相关操作
 * author:fanyy
 */
class ProductapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
    /*
     商品列表页
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
        
         $List =Specialty_Api::getProductList($page,$pageSize,$arrParam);
        
         $tmpList = $List['list'];
         /*for($i=0; $i<count($tmpList); $i++) {   
            //处理状态
            $tmpList[$i]['statusName'] = Specialty_Type_Product::getTypeName($tmpList[$i]["status"]);
             
            //若查询参数包含景点id  处理景点权重
            $sightlist = $tmpList[$i]['sights'];
            for ($j=0; $j < count($sightlist); $j++) {  
               if (isset($arrParam['sight_id'])&&$arrParam['sight_id']==$sightlist[$j]['id']) {
                    $tmpList[$i]['weight'] = $sightlist[$j]['weight'];
               }
            }  
         }*/
         $List['list']=$tmpList;

         $retList['recordsTotal']    = $List['total'];
         $retList['recordsFiltered'] = $List['total'];
         $retList['data'] = $List['list']; 
         return $this->ajax($retList); 
    }
 

    /**
     * 添加商品
     */
    function addAction(){
         
        $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
        
        $dbRet =Specialty_Api::addProduct($_REQUEST);
        if (!empty($dbRet)) {
            return $this->ajax($dbRet);
        }
        return $this->ajaxError();
    }

     /**
     * 编辑保存商品
     */
    function saveAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            return $this->ajaxError();
        }
        $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
       
        $dbRet = Specialty_Api::editProduct($id, $_REQUEST);

        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

     /**
     * 删除商品
     */
    function delAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if(empty($id)){
            return $this->ajaxError();
        }
        $dbRet = Specialty_Api::delProduct($id);
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
        $dbRet =  Specialty_Api::changeWeight($sightId, $id, $to);
 
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
           $bRet = Specialty_Api::editProduct($postid,array('status'=>$status)); 
           if(!$bRet){  
              return $this->ajaxError('501','ID:【'.$postid.'】修改状态失败');
           }
       }
       return $this->ajax();
   }
  

    /**
     * 裁剪商品背景图片
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
            $bRet=Specialty_Api::editProduct($postid,$params);
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
           $status = Specialty_Type_Product::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = Specialty_Type_Product::PUBLISHED;
           break;
        case 'BLACKLIST':
           $status = Specialty_Type_Product::BLACKLIST;
           break;
         default:
           $status = Specialty_Type_Product::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }
}