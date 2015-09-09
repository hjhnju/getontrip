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
    
    protected $logicUser = '';
        
    public function __construct(){
        $this->logicUser = new User_Logic_User();
    }
    
    /**
     * 查询反馈意见，前端使用
     * @param integer $deviceId
     * @return array
     */
    public function listAdvise($deviceId,$page,$pageSize){
        $arrRet     = array();
        $index      = 0;
        $userId     = $this->logicUser->getUserId($deviceId); 
        $image      = $this->logicUser->getUserAvatar($userId);      
        $listAdvise = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $userId));
        $listAdvise->setPage($page);
        $listAdvise->setPagesize($pageSize);
        $ret =  $listAdvise->toArray();
        foreach ($ret['list'] as $val){
            $temp['id']          = $val['id'];
            $temp['type']        = Advise_Type_Type::ADVISE;
            $temp['image']       = $image;
            $temp['content']     = $val['content'];
            $temp['create_time'] = date('Y-m-d H:i',$val['create_time']);           
            $arrRet[] = $temp;
            
            //拼回答,优先选择人工回答
            $ret = $this->getAnswer($val['id'], $index);
            foreach ($ret as $key => $val){
                if(empty($val['create_time'])){
                    $ret[$key]['create_time'] = $temp['create_time'];
                }
            }
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
    public function addAdvise($deviceId,$strData){
        $userId             = $this->logicUser->getUserId($deviceId);
        $objAdvise          = new Advise_Object_Advise();
        $objAdvise->userid  = $userId;
        $objAdvise->content = $strData;
        $objAdvise->status  = Advise_Type_Status::UNTREATED;
        $objAdvise->save();
        
        $listAdvise         = new Advise_List_Advise();
        $listAdvise->setPagesize(PHP_INT_MAX);
        $listAdvise->setFilter(array('userid' => $userId));
        $ret   = $listAdvise->toArray();
        $index = $ret['total'];
        if(isset($this->_feedmsg[$index])){
            return $this->_feedmsg[$index];
        }
        return '';
    }
    
    /**
     * 获取反馈的回复
     * @param integer $adviseId
     * @param integer $index
     * @return array
     */
    public function getAnswer($adviseId,$index){
        $listAdvise          = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $adviseId));
        $listAdvise->setPagesize(PHP_INT_MAX);
        $ret = $listAdvise->toArray();
        $arrRet = array();
        if(!empty($ret['list'])){
            return $ret['list'];
        }
        if(isset($this->_feedmsg[$index])){
            $temp['id']          = '';
            $temp['type']        = Advise_Type_Type::ANSWER;
            $temp['content']     = $this->_feedmsg[$index];
            $temp['create_time'] = '';
            $arrRet[] = $temp;
        }
        return $arrRet;
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
}