<?php
/**
 * 用户评论
 * fyy
 */
class AdviseapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
 
    /**
     * list
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据 
        $start =isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page = ($start/$pageSize)+1;
 
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        //默认全部查询提问 
        $arrParam = array_merge(array('type'=>Advise_Type_Type::ADVISE),$arrParam);
 
        $List = Advise_Api::getAdviseList($page,$pageSize,$arrParam);

        $tmpList=$List['list'];
        if (count($tmpList)>0) { 
            for($i=0;$i<count($tmpList);$i++){ 
                //处理状态名称
                $tmpList[$i]['status_name'] = Advise_Type_Status::getTypeName($tmpList[$i]['status']);
                 //处理类型名称
                $tmpList[$i]['type_name'] = Advise_Type_Type::getTypeName($tmpList[$i]['type']);
                //被回复
                $answer= $tmpList[$i]['answer'];
                for($a=0;$a<count($answer);$a++){ 
                   //处理状态名称
                   $answer[$a]['status_name'] = Advise_Type_Status::getTypeName($answer[$a]['status']);
                   //处理类型名称
                   $answer[$a]['type_name'] = Advise_Type_Type::getTypeName($answer[$a]['type']);
                }
                $tmpList[$i]['answer']=$answer;
            }
        }
        $List['list'] =  $tmpList;

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
		return $this->ajax($retList);
         
    }


    /**
     *  对某个反馈进行处理
     * @return [type] [description]
     */
    public function addAnswerAction()
    {   
        //判断是否有ID
        $id=isset($_POST['id'])?intval($_POST['id']) :0; 
        $content=isset($_POST['content'])?$_POST['content']:''; 
        if(empty($id)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR); 
        } 
         
        $bRet = Advise_Api::addAnswer($id, $content); 

        if($bRet){
            return $this->ajax();
        }
        return $this->ajaxError(); 

    }


    
  
}