<?php
/**
 * 视频管理相关操作
 * author:fanyy
 */
class VideoapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
    public function listAction(){
         //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $sight_id =isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):1;
        
        $List =Video_Api::getVideos($sight_id,$page,$pageSize);
        
        foreach ($List['list'] as $key => $val){
            $List['list'][$key]['typeName']   = Video_Type_Type::getTypeName($val["type"]);
            $List['list'][$key]['statusName'] = Video_Type_Status::getTypeName($val["status"]);
        }
        $retList['recordsTotal']    = $List['total'];
        $retList['recordsFiltered'] = $List['total'];
        $retList['data'] = $List['list']; 
        return $this->ajax($retList);
    }

    /**
     * 添加词条
     */
    function addAction(){
        $dbRet=Keyword_Api::addKeyword($_REQUEST);
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
       
        $dbRet=Video_Api::editVideo($id, $_REQUEST);
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

     /**
     * 删除词条
     */
    function delAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            return $this->ajaxError();
        }
        $dbRet=Keyword_Api::delKeyword($id);
        if ($dbRet) {
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