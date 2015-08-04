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
        
        $List =Video_Api::getVideos($sight_id,$page);
        

        $tmpList=$List;
        if (count($tmpList)>0) { 
            for($i=0;$i<count($tmpList);$i++){
                //处理类型
               $tmpList[$i]['typeName'] = Video_Type_Type::getTypeName($tmpList[$i]["type"]);
               //处理状态值
                $tmpList[$i]["statusName"] = Video_Type_Status::getTypeName($tmpList[$i]["status"]); 
            } 
            $retList['recordsTotal'] = $List[0]['totalNum'];
            $retList['recordsFiltered'] =$List[0]['totalNum'];  
        }
        $List =  $tmpList;
 
        $retList['data'] =$List; 
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
     * 编辑保存词条
     */
    function saveAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            return $this->ajaxError();
        }
        $dbRet=Keyword_Api::editKeyword($id,$_REQUEST);
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
     * 根据景点ID获取百科词条
     * @return [type] [description]
     */
    public function getKeywordsBySightIdAction(){
      
        $sight_id = isset($_REQUEST['sight_id'])? intval($_REQUEST['sight_id']) : 1;  
        $keywordsList = Keyword_Api::queryKeywords($sight_id,1, PHP_INT_MAX);
        return $this->ajax($keywordsList['list']);
    }
}