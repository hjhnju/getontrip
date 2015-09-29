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
     * list
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        //
        $start    = isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page     = ($start/$pageSize)+1;
        $labelId  = isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if(empty($labelId)){
            $list = Search_Api::listLabel(1, PHP_INT_MAX);
            $labelId = $list['list'][0]['id'];
        }
          
        $List     = Search_Api::getLabel($labelId, $page, $pageSize);

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] = $List['list'];        
		$this->ajax($retList);
         
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
        $type   = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $objId  = isset($_REQUEST['obj_id'])?intval($_REQUEST['obj_id']):'';
        $id     = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        Base_Log::notice("test".json_encode($_REQUEST));
        if(!empty($id) && !empty($objId) && !empty($type)){
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
            return $this->ajax($bRet);
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
     
}