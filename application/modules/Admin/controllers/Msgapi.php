<?php
/**
 * 用户评论
 * fyy
 */
class MsgapiController extends Base_Controller_Api{
     
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
 
        $arrInfo =isset($_REQUEST['params'])?$_REQUEST['params']:array(); 
        $List =Msg_Api::queryMsg($page, $pageSize,$arrInfo);
 
        $tmpList=$List['list'];
        if (count($tmpList)>0) {
            $sightArray=array(); 
           
            for($i=0;$i<count($tmpList);$i++){ 
                //处理状态名称
                $tmpList[$i]['status_name'] = Msg_Type_Status::getTypeName($tmpList[$i]['status']);
                //处理类型名称
                $tmpList[$i]['type_name'] = Msg_Type_Type::getTypeName($tmpList[$i]['type']);
            }
        }
        $List['list'] = $tmpList;

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
		$this->ajax($retList);
         
    }


    /**
     * 发送系统消息
     * @return [type] [description]
     */
    public function sendAction()
    {   
        $arrParam =isset($_REQUEST)?$_REQUEST:array(); 
        $bRet = Msg_Api::sendmsg(Msg_Type_Type::SYSTEM,'', '', $arrParam,0); 

        if($bRet){
            return $this->ajax();
        }
        return $this->ajaxError(); 

    }
     
}