<?php
/**
 * 标签相关操作
 * author:fyy
 */
class TagapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    

    /*      $List = array(
                 array('id'=>1, 'name'=>'hj**jh0', 'create_time'=>strtotime("2015-04-15 00:00:00"), 'update_time'=>strtotime("2015-05-15 00:00:00")),
                 array('id'=>2, 'name'=>'hj**jh1', 'create_time'=>strtotime("2015-04-16 00:00:00"), 'update_time'=>strtotime("2015-05-16 00:00:00")),
                 array('id'=>2, 'name'=>'hj**h2', 'create_time'=>strtotime("2015-04-17 00:00:00"), 'update_time'=>strtotime("2015-05-17 00:00:00")),
    ); */
    /**
     * 标签list
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:10;

        $page=($start/$pageSize)+1;
         
       
        $List=Tag_Api::getTagList($page, $pageSize);
    
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];

       /* $retList['start'] =$start;
        $retList['pageSize'] =$pageSize;
        $retList['page'] =$page;*/
		$this->ajax($retList);
         
    }

    public function saveAction()
    {   
      //判断是否有ID
       $id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
       $name = isset($_REQUEST['name'])?$_REQUEST['name']:''; 
       if($id!=""){
           $bRet = Tag_Api::editTag($id,$name);
       }else{ 
           $bRet = Tag_Api::saveTag($name);
       } 

        if($bRet){
            //根据名称返回标签信息 
            $tagInfo = Tag_Api::getTagByName($name);
            return $this->ajax($tagInfo);
        }
        return $this->ajaxError();
    }
    
    public function delAction(){
        //判断是否有ID
        $id=isset($_REQUEST['id'])?$_REQUEST['id']:''; 
        $bRet =Tag_Api::delTag($id);
        if($bRet){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
    
}