<?php
/**
 * 搜索标签
 * huwei
 */
class SearchapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
 
    /**
     * 获取某个搜索标签信息
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据 
        $start    = isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page     = ($start/$pageSize)+1;
        $labelId  = isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if(empty($labelId)){
            $list = Search_Api::listLabel(1, PHP_INT_MAX);
            $labelId = isset($list['list'][0]['id'])?$list['list'][0]['id']:'';
        }
        if(!empty($labelId)){ 
            $List     = Search_Api::getLabel($labelId, $page, $pageSize);  
        }else{
            $List['total'] = 0;
            $List['list']  = array();
        } 
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] = $List['list'];        
        return   $this->ajax($retList);
         
    }

    /**
     * 查询搜索标签列表标签list
     *  
     */    
    public function labellistAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        //
        $start    = isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page     = ($start/$pageSize)+1;

        $List = Search_Api::listLabel(1, PHP_INT_MAX);
         
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] = $List['list'];        
        return $this->ajax($retList);
         
    }
    
    /**
     * 删除搜索标签关系
     */
    public function delLabelAction(){
        $labelId = isset($_REQUEST['labelId'])?$_REQUEST['labelId']:'';
        $objId   = isset($_REQUEST['objId'])?$_REQUEST['objId']:'';
        if(!empty($labelId) && !empty($objId)){
            $bRet    = Search_Api::delLabel($labelId, $objId);
            return $this->ajax($bRet);
        }        
        return $this->ajaxError();
    }
    
    /**
     * 添加搜索标签关系
     */
    public function addLabelAction(){
        $type   = isset($_REQUEST['type'])?intval($_REQUEST['type']):Search_Type_Label::SIGHT;
        $sight_id  = isset($_REQUEST['sight_id'])?$_REQUEST['sight_id']:array();
        $city_id  = isset($_REQUEST['city_id'])?$_REQUEST['city_id']:array();
        switch ($type) {
            case Search_Type_Label::SIGHT:
                $objId=$sight_id;
                break;
             case Search_Type_Label::CITY:
                $objId=$city_id;
                break;
            default:
                $objId=$sight_id;
                break;
        }
        $id     = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        if(!empty($id) && !empty($objId) && !empty($type)){
            if(!is_array($objId)){
                $objId = array($objId);
            }
            $ret = Search_Api::addLabel($objId, $type, $id);
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }
    
    /**
     * 删除标签
     */
    public function delTagAction(){
        $tagId  = isset($_REQUEST['tagId'])?$_REQUEST['tagId']:'';
        if(!empty($tagId)){
            $bRet    = Tag_Api::delTag($tagId);
            return $this->ajax($tagId);
        }
        return $this->ajaxError();
    }


    /**
     * 添加标签
     * @return [type] [description]
     */
    public function addAction(){   
        $name = isset($_REQUEST['name'])?trim($_REQUEST['name']):'';
        $type = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $arrObjIds = isset($_REQUEST['objid'])?$_REQUEST['objid']:array();
        if(!is_array($arrObjIds)){
            $arrObjIds = array($arrObjIds);
        }
        $ret = Search_Api::addNewTag($name,$type , $arrObjIds);
        $this->ajax($ret);
    }

    /**
     * 获取标签关联的对象
     * @return [type] [description]
     */
    public function listLabelAction()
    { 
        $start    = isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page     = ($start/$pageSize)+1;
        $labelId  = isset($_REQUEST['id'])?$_REQUEST['id']:'';
        
        $List = Search_Api::listLabel($page, $pageSize, array('id' =>$labelId));
        

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] = $List['list'];        
                $this->ajax($retList);

    }


    
    /**
     * 修改搜索标签的权重
     * @return [type] [description]
    */
    public function changeWeightAction(){
       $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       $to = isset($_REQUEST['to'])? intval($_REQUEST['to']) : 0;
        
       $dbRet = Tag_Api::changeOrder($id, $to);
       if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

    /**
     * 搜索热词列表
     * @return [array] [description]
     */
    public function wordListAction()
    {
        //第一条数据的起始位置，比如0代表第一条数据 
        $start    = isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page     = ($start/$pageSize)+1;
        $arrConf = isset($_REQUEST['params'])?$_REQUEST['params']:array(); 
        

        $List = Search_Api::getQueryWords($page, $pageSize, $arrConf);

        $tmpList=$List['list'];
        for ($i=0; $i < count($tmpList) ; $i++) { 
            //处理状态
            $tmpList[$i]['statusName'] = Search_Type_Word::getTypeName($tmpList[$i]['status']);
             
        } 
        $List['list'] =  $tmpList;
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];    

        return   $this->ajax($retList);

    }

    /*
      修改热词的状态
     */
    public function changeWordStatusAction()
    {
        $word = isset($_REQUEST['word'])? $_REQUEST['word'] : ''; 
        $action = isset($_REQUEST['action'])? $_REQUEST['action'] : 'AUDITING';
       
        $status = $this->getStatusByActionStr($action); 
 
        $dbRet = Search_Api::editQueryWordStatus($word, $status);
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
         case 'AUDITING':
           $status = Search_Type_Word::AUDITING;
           break;
         case 'AUDITPASS':
           $status = Search_Type_Word::AUDITPASS;
           break;
        case 'AUDITFAILED':
           $status = Search_Type_Word::AUDITFAILED;
           break;
         default:
           $status = Search_Type_Word::AUDITING;
           break;
       } 
       return   $status;
    }
     
}