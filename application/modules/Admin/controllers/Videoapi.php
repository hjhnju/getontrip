<?php
/**
 * 视频管理相关操作
 * author:fanyy
 */
class VideoapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
    /*
     书籍列表页
     */
    public function listAction(){
         //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX;

        $page=($start/$pageSize)+1;
         
        $sight_id =isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):1;

        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        
        $List =Video_Api::getVideos($sight_id,$page,$pageSize,$arrParam);
        
        $tmpList = $List['list'];
        for($i=0; $i<count($tmpList); $i++) {  
            //处理状态
            $tmpList[$i]['typeName']   = Video_Type_Type::getTypeName($tmpList[$i]["type"]);
            $tmpList[$i]['statusName'] = Video_Type_Status::getTypeName($tmpList[$i]["status"]);
            
            //处理景点名称
            $sightInfo = Sight_Api::getSightById($tmpList[$i]['sight_id']);
            $tmpList[$i]['sight_name'] = $sightInfo['name'];
            
            
        }
        $List['list']=$tmpList;

        $retList['recordsTotal']    = $List['total'];
        $retList['recordsFiltered'] = $List['total'];
        $retList['data'] = $List['list']; 
        return $this->ajax($retList);
    }

    /**
     * 添加视频
     */
    function addAction(){

        $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
        $dbRet =Video_Api::addVideo($_REQUEST);
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

     /**
     * 编辑保存视频
     */
    function saveAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            return $this->ajaxError();
        }
        $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
       
        $dbRet = Video_Api::editVideo($id, $_REQUEST);
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

     /**
     * 删除视频
     */
    function delAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            return $this->ajaxError();
        }
        $dbRet = Video_Api::delVideo($id);
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
        $id =isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        $to =isset($_REQUEST['to'])?intval($_REQUEST['to']):'';
        if(empty($id)||empty($to)){
            return $this->ajaxError();
        }
        $dbRet = Video_Api::changeWeight($id,$to);
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }
  

    /**
     * 裁剪话题背景图片
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
            $bRet=Video_Api::editVideo($postid,$params);
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
           $status = Video_Type_Status::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = Video_Type_Status::PUBLISHED;
           break;
        case 'BLACKLIST':
           $status = Video_Type_Status::BLACKLIST;
           break;
         default:
           $status = Video_Type_Status::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }
}