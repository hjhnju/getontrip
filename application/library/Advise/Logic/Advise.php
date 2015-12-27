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
    public function listAdvise($userId, $page, $pageSize){
        $arrRet     = array();
        $index      = ($page -1)*$pageSize;
        $image      = $this->logicUser->getUserAvatar($userId);      
        $listAdvise = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $userId,'type' => Advise_Type_Type::ADVISE));
        $listAdvise->setPage($page);
        if($page == 1){
            $listAdvise->setPagesize($pageSize - 1);
            $temp['id']          = '';
            $temp['image']       = '';
            $temp['type']        = strval(Advise_Type_Type::ANSWER);
            $temp['content']     = self::WELCOME;
            $temp['create_time'] = date('Y-m-d H:i',time());
            $arrRet[] = $temp;
        }else{
            $listAdvise->setPagesize($pageSize);
        }
        $ret =  $listAdvise->toArray();
        foreach ($ret['list'] as $val){
            $temp = array();
            $temp['id']          = strval($val['id']);
            $temp['type']        = strval(Advise_Type_Type::ADVISE);
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
        $listAdvise = new Advise_List_Advise();
        $listAdvise->setPage($page);
        $listAdvise->setPagesize($pageSize);
        $listAdvise->setFilter($arrParams);
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
    public function addAdvise($userId, $strData){
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
    public function addAnswer($adviseId,$strContent){
        $objAdvise = new Advise_Object_Advise();
        $objAdvise->userid  = $adviseId;
        $objAdvise->content = $strContent;
        $objAdvise->type    = Advise_Type_Type::ANSWER;
        $ret1 =  $objAdvise->save();
        
        $objAdvise->fetch(array('id' => $adviseId));
        $objAdvise->status     = Advise_Type_Status::SETTLED;
        $objAdvise->updateTime = time();
        $ret2 = $objAdvise->save();
        return $ret1&&$ret2;
    }
    
    public function getAutoAnswer(){
        return $this->_feedmsg;
    }
}