<?php
/**
 * 标签相关操作
 * author :fyy
 */
class SourceapiController extends Base_Controller_Api{
     
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
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:10;

        $page=($start/$pageSize)+1;
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
       
        $List=Source_Api::searchSource($arrParam,$page, $pageSize);
    
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];

       /* $retList['start'] =$start;
        $retList['pageSize'] =$pageSize;
        $retList['page'] =$page;*/
		$this->ajax($retList);
         
    }

    /**
     * typelist
     *  
    */    
    public function typelistAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:10;

        $page=($start/$pageSize)+1;
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
       
        $List=Source_Api::listType($page,$pageSize,$arrParam);
    
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];

       /* $retList['start'] =$start;
        $retList['pageSize'] =$pageSize;
        $retList['page'] =$page;*/
        $this->ajax($retList);
         
    }

    public function addAction()
    {    
        $oldInfo=Source_Api::getSourceByName($_REQUEST['name']);
        if(isset($oldInfo['id'])){ 
            return $this->ajaxError(100,'该来源已经存在'); 
        }
        $bRet = Source_Api::addSource($_REQUEST);
        if($bRet){ 
            return $this->ajax();
        }
        return $this->ajaxError(); 
    }
    
    public function addAndReturnAction()
    {   
    	$oldInfo=Source_Api::getSourceByName($_REQUEST['name']);
    	if(isset($oldInfo['id'])){ 
    		return $this->ajaxError(100,'该来源已经存在'); 
    	}
        $bRet = Source_Api::addSource($_REQUEST);
        if($bRet){
        	$sourceInfo=Source_Api::getSourceByName($_REQUEST['name']);
            return $this->ajax($sourceInfo);
        }
        return $this->ajaxError(); 
    }
    

     public function editAction()
    {    
        $sourceId = $_REQUEST['id']; 
        $bRet = Source_Api::editSource($sourceId,$_REQUEST);
        if($bRet){ 
            return $this->ajax();
        }
        return $this->ajaxError(); 
    }
    public function delAction(){
        //判断是否有ID
        $sourceId=isset($_REQUEST['id'])?$_REQUEST['id']:0; 
        $bRet =Source_Api::delSource($sourceId);
        if($bRet){
            return $this->ajax();
        }
         return $this->ajaxError(100,'来源已经被引用'); 
    }
     

    /**
      * name模糊查询，用于自动完成下拉框
      * @return [type] [description]
    */
    public function getSourceListAction(){ 
        $str=isset($_REQUEST['query'])?$_REQUEST['query']:'';
        $arrConf = array('name' => $str); 
        //最大值 PHP_INT_MAX   
        $List = Source_Api::searchSource($arrConf,1,PHP_INT_MAX); 
      
        return $this->ajax($List['list']);   
    }


    /**
     * 添加新分组
     */
    public function addGroupAction()
    {    
        $oldInfo=Source_Api::getSourceByName($_REQUEST['name']);
        if(isset($oldInfo['id'])){ 
            return $this->ajaxError(100,'该来源已经存在'); 
        }
        $bRet = Source_Api::addType($_REQUEST);
        if($bRet){ 
            return $this->ajax();
        }
        return $this->ajaxError(); 
    }
    
    /**
     * 编辑分组
     */
    public function editGroupAction()
    {    
        $typeId = $_REQUEST['id']; 
        $bRet = Source_Api::editType($typeId,$_REQUEST);
        if($bRet){ 
            return $this->ajax();
        }
        return $this->ajaxError(); 
    }

    /**
     * 删除分组
     */
    public function delGroupAction(){
        //判断是否有ID
        $typeId=isset($_REQUEST['id'])?$_REQUEST['id']:0; 
        $bRet =Source_Api::delType($typeId);
        if($bRet){
            return $this->ajax();
        }
        return $this->ajaxError(); 
    }
}