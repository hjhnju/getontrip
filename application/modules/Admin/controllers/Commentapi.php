<?php
/**
 * 用户评论
 * fyy
 */
class CommentapiController extends Base_Controller_Api{
     
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
        $start =isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page = ($start/$pageSize)+1;

        $obj_id = isset($_REQUEST['obj_id'])?$_REQUEST['obj_id']:'';
        $type  = isset($_REQUEST['type'])?$_REQUEST['type']:'';
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        /*if(!empty($topicId)){
           $arrInfo=array('topicId' => $topicId); 
        } else{
             $arrInfo = array();
        }*/
        $List = Comment_Api::getComments($page,$pageSize,$arrParam,$type,$obj_id);
        $tmpList=$List['list'];
        if (count($tmpList)>0) { 
            for($i=0;$i<count($tmpList);$i++){ 
                //处理状态名称
                $tmpList[$i]['status_name'] = Comment_Type_Status::getTypeName($tmpList[$i]['status']);
                //被回复
                $subComment= $tmpList[$i]['subComment'];
                for($a=0;$a<count($subComment);$a++){ 
                   //处理状态名称
                   $subComment[$a]['status_name'] = Comment_Type_Status::getTypeName($subComment[$a]['status']);
                }
                $tmpList[$i]['subComment']=$subComment;
            }
        }
        $List['list'] =  $tmpList;

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
		return $this->ajax($retList);
         
    }


    /**
     *  修改评论状态
     * @return [type] [description]
     */
    public function changeStatusAction()
    {   
        //判断是否有ID
        $id=isset($_POST['id'])?$_POST['id']:''; 
        $action=isset($_POST['action'])?$_POST['action']:''; 
        if(empty($id)||empty($action)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR); 
        } 
        switch ($action) {
            case 'PUBLISHED':
                $status=Comment_Type_Status::PUBLISHED;
                break;
            case 'PUBLISHED':
                $status=Comment_Type_Status::NOTPUBLISHED;
                break;
            
            default:
                $status=Comment_Type_Status::NOTPUBLISHED;
                break;
        }
       
        $bRet = Comment_Api::changeCommentStatus($id,$status); 

        if($bRet){
            return $this->ajax();
        }
        return $this->ajaxError(); 

    }


    
  
}