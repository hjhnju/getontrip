<?php
/**
 * 意见反馈逻辑层
 * @author huwei
 *
 */
class Advise_Logic_Advise{
    
    //反馈自动回复列表
    protected $_feedmsg = array(
        '有什么可以帮您？',
        '感谢您的反馈，我们将尽快处理',
        '欢迎关注微信号，一起讨论',
    );
    
    const WELCOME = '小途在这，有什么可以帮助您?';
    
    protected $logicUser = '';
        
    public function __construct(){
        $this->logicUser = new User_Logic_User();
    }
    
    /**
     * 查询反馈意见，前端使用
     * @return array
     */
    public function listAdvise( $page, $pageSize){
        $userId             = User_Api::getCurrentUser();
        if(empty($userId)){
            $userId =  session_id();;
        }
        $arrRet     = array();
        $index      = ($page -1)*$pageSize;
        $image      = $this->logicUser->getUserAvatar($userId);      
        $listAdvise = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $userId));
        $total = $listAdvise->getTotal();
        if(empty($total)){
            $objAdvise = new Advise_Object_Advise();
            $objAdvise->userid      = $userId;
            $objAdvise->type        = strval(Advise_Type_Type::ADVISE);
            $objAdvise->content     = self::WELCOME;
            $objAdvise->create_time = date('Y-m-d H:i',time());
            $objAdvise->save();
        }elseif($total == 1){
            $arrAdvise = $listAdvise->toArray();
            if($arrAdvise['list'][0]['type'] == Advise_Type_Type::ADVISE){
                $objAdvise = new Advise_Object_Advise();
                $objAdvise->fetch(array('userid' => $userId));
                $objAdvise->createTime = time();
                $objAdvise->save();
            }
        }
        
        $listAdvise = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $userId,'type' => Advise_Type_Type::ADVISE));
        $listAdvise->setPage($page);
        $listAdvise->setPagesize($pageSize);
        $ret =  $listAdvise->toArray();
        foreach ($ret['list'] as $val){
            $temp = array();
            $temp['id']          = strval($val['id']);
            if($val['content'] == self::WELCOME){
                $temp['type']        = strval(Advise_Type_Type::ANSWER);
            }else{
                $temp['type']        = strval(Advise_Type_Type::ADVISE);
            }
            $temp['image']       = $image;
            $temp['content']     = $val['content'];
            $temp['create_time'] = date('Y-m-d H:i',$val['create_time']);           
            $arrRet[] = $temp;
            
            //拼回答
            $ret = $this->getAnswer($val['id'], $index);
            
            $arrRet = array_merge($arrRet,$ret);
            $index += 1;                        
        }
        return $arrRet;
    }
    
    /**
     * 根据ID查询反馈意见
     * @param integer $adviseId
     * @return array
     */
    public function getAdviseById($adviseId){
        $arrRet     = array();
        $index      = 0;
        $objAdvise = new Advise_Object_Advise();
        $objAdvise->fetch(array('id' => $adviseId));
        $arrRet =  $objAdvise->toArray();
        
        $listAdvise = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $arrRet['id'],'type' => Advise_Type_Type::ANSWER));
        $listAdvise->setPagesize(PHP_INT_MAX);
        $arrAnswer  = $listAdvise->toArray();
        $arrRet['answer'] = $arrAnswer['list'];
        return $arrRet;
    }
    
    /**
     * 查询反馈意见，后端使用
     * @return array
     */
    public function getAdviseList($page,$pageSize,$arrParams = array()){
        $arrRet     = array();
        $filter     = '`content` !="'.self::WELCOME.'"';
        $listAdvise = new Advise_List_Advise();
        foreach ($arrParams as $key => $val){
            $filter .=" and `".$key."`=".$val;
        }
        $listAdvise->setPage($page);
        $listAdvise->setPagesize($pageSize);
        $listAdvise->setFilterString($filter);
        $arrRet =  $listAdvise->toArray();
        foreach ($arrRet['list'] as $key => $val){
            if($val['status'] !== Advise_Type_Status::SETTLED){
                $arrRet['list'][$key]['update_time'] = '';
                $arrRet['list'][$key]['update_user'] = '';
                $arrRet['list'][$key]['create_user'] = '';
            }
            $listAdvise = new Advise_List_Advise();
            $listAdvise->setPagesize(PHP_INT_MAX);
            $listAdvise->setFilter(array('userid' => $val['id'],'type' => Advise_Type_Type::ANSWER));
            $arrTmp =  $listAdvise->toArray();
            $arrRet['list'][$key]['answer'] = $arrTmp['list'];
        }
        return $arrRet;
    }
    
    /**
     * 添加反馈意见
     * @param integer $deviceId
     * @param array $arrData
     * @return boolean
     */
    public function addAdvise($strData){
        $userId             = User_Api::getCurrentUser();
        if(empty($userId)){
            $userId =  session_id();;
        }
        $objAdvise          = new Advise_Object_Advise();
        $objAdvise->userid  = $userId;
        $objAdvise->content = $strData;
        $objAdvise->status  = Advise_Type_Status::UNTREATED;
        $ret = $objAdvise->save();
        return $ret;
    }
    
    /**
     * 获取反馈的回复
     * @param integer $adviseId
     * @param integer $index
     * @return array
     */
    public function getAnswer($adviseId,$index){
        $listAdvise          = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $adviseId,'type' => Advise_Type_Type::ANSWER));
        $listAdvise->setFields(array('id','content','create_time'));
        $listAdvise->setPagesize(PHP_INT_MAX);
        $ret = $listAdvise->toArray();
        foreach ($ret['list'] as $key => $val){
            $ret['list'][$key]['id'] = strval($val['id']);
            $ret['list'][$key]['type'] = strval(Advise_Type_Type::ANSWER);
            $ret['list'][$key]['create_time'] = date('Y-m-d H:i',$val['create_time']);
        }
        return $ret['list'];
    }
    
    /**
     * 对反馈内容进行回复
     */
    public function addAnswer($adviseId,$strContent,$status){
        $objAdvise = new Advise_Object_Advise();
        $objAdvise->userid  = $adviseId;
        $objAdvise->content = $strContent;
        $objAdvise->type    = Advise_Type_Type::ANSWER;
        $ret1 =  $objAdvise->save();
        
        $objAdvise->fetch(array('id' => $adviseId));
        $objAdvise->status     = $status;
        $objAdvise->updateTime = time();
        $ret2 = $objAdvise->save();
        return $ret1&&$ret2;
    }
    
    public function getAutoAnswer(){
        return $this->_feedmsg;
    }
    
    public function getAdviseNum($status = ''){
        $listAdvise          = new Advise_List_Advise();
        if(!empty($status)){
            $listAdvise->setFilter(array('type' => Advise_Type_Type::ADVISE,'status' => $status));
        }else{
            $listAdvise->setFilterString("status !=". Advise_Type_Status::SETTLED." and status !=".Advise_Type_Status::DROP." and type = ".Advise_Type_Type::ADVISE);
        }
        return $listAdvise->getTotal();
    }
}