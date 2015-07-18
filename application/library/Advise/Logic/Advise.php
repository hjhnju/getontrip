<?php
/**
 * 意见反馈逻辑层
 * @author huwei
 *
 */
class Advise_Logic_Advise{
    
    const DEFAULT_SIZE = 5;
    
    protected $logicUser = '';
        
    public function __construct(){
        $this->logicUser = new User_Logic_User();
    }
    
    /**
     * 查询反馈意见
     * @param integer $deviceId
     * @return array
     */
    public function listAdvise($deviceId){
        $userId     = $this->logicUser->getUserId($deviceId);
        $listAdvise = new Advise_List_Advise();
        $listAdvise->setFilter(array('userid' => $userId));
        $listAdvise->setPagesize(self::DEFAULT_SIZE);
        $listAdvise->setOrder('create_time desc');
        return $listAdvise->toArray();
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
        $objAdvise->status  = Advise_Type::UNTREATED;
        return $objAdvise->save();
    }
}