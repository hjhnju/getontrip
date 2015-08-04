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
     * 标签list
     *  
     */    
    public function listAction(){  

        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        $query =isset($_REQUEST['params']['content'])?$_REQUEST['params']['content']:'';
        
         
        
        $List = Topic_Api::search($arrParam,$page,$pageSize);

        //处理状态值 
        $tmpList = $List['list'];
        for($i=0; $i<count($tmpList); $i++) { 
            $tmpList[$i]["statusName"] = Topic_Type_Status::getTypeName($tmpList[$i]["status"]);  
         }

        //添加景点名称
        $sightArray=array(); 
        for($i=0; $i<count($tmpList); $i++){
          $sightlist = $tmpList[$i]['sights']; 
          for($j=0; $j<count($sightlist); $j++){ 
             $item = $sightlist[$j];
             $sight_id = $item['sight_id']; 
              if (!array_key_exists($sight_id,$sightArray)) {  
                    //根据ID查找景点名称
                    $sightInfo =(array) Sight_Api::getSightById($sight_id);
                     
                    $item['sight_name'] = $sightInfo['name']; 
                    //添加到数组
                    $sightArray[$sight_id]=$sightInfo['name'];  
              }
              else{ 
                   $item['sight_name']  = $sightArray[$sight_id];
              }
              $sightlist[$j] = $item;
          } 
           $tmpList[$i]['sights'] = $sightlist; 
        } 

          
        $List['list']=$tmpList;
        
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];  
		    return $this->ajax($retList);
         
    }

    /**
     * 编辑话题
     * @return [type] [description]
     */
    public function saveAction()
    {   
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       } 
       $bRet=Topic_Api::editTopic($postid,$_REQUEST);
       if($bRet){
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }
    
   /**
    * 添加话题
    */
    public function addAction(){  
       $bRet=Topic_Api::addTopic($_REQUEST);   
       if($bRet){
            return $this->ajax();
       } 
       return $this->ajaxError();
    }

   /**
   * 过滤器 添加话题
   */
    public function addByFilterAction(){
       $bRet=Topic_Api::addTopic($_REQUEST);   
       if($bRet){
            return $this->ajax();
       } 
       return $this->ajaxError();
    }

    /**
    * 删除话题
    */
    public function delAction(){
        //判断是否有ID
        $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;  
        $bRet =Topic_Api::delTopic($postid);
        if($bRet){
            return $this->ajax($postid);
        }
        return $this->ajaxError();
    }
     
 
}