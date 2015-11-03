<?php
/**
 * 城市管理相关操作
 */
class UserapiController extends Base_Controller_Api{
     
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
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();

        $List =User_Api::getUserList($page,$pageSize,$arrParam);

        $tmpList=$List['list'];

         
        foreach($tmpList as $key=>$item){
            //处理性别
            $tmpList[$key]['sex_name'] = User_Type_Info::getTypeName($item['sex']);           
        } 

        $List['list']=$tmpList;
         
    
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
        return $this->ajax($retList);
         
    }


    /**
     * 获取用户注册时间的的柱形图
     * @return [type] [description]
     */
    public function getUserRegTimeLineAction()
    {
        $userName = $this->objUser->displayname;
         

          $arrRet = Invest_Api::getEarningsMonthly($this->userid);                          
          foreach ($arrRet as $key => $value) {
              $x[] = $key;
              $y[] = intval($value);
          }
          $ret = array(
              'x' => $x,
              'y' => $y,
          );         
          if($ret==false) {
              $this->outputError(Account_RetCode::GET_PROFIT_CURVE_FAIL,
                  Account_RetCode::getMsg(Account_RetCode::GET_PROFIT_CURVE_FAIL));
              return;
          }                        
          $this->output($ret);
    }
 
}