<?php
/**
 * 点赞接口层
 * @author huwei
 */
class Praise_Logic_Praise{
    
    protected $logicUser;
    
    public function __construct(){
        $this->logicUser = new User_Logic_User();
    }
    
    /**
     * 添加点赞逻辑层,同时注意更新redis中的统计数据
     * @param integer $type
     * @param integer $device_id
     * @param integer $obj_id
     * @return boolean 
     */
    public function addPraise($device_id, $obj_id,$type=1){
        $obj = new Praise_Object_Praise();
        $obj->type   = $type;
        $obj->objId  = $obj_id;
        $obj->userId = $this->logicUser->getUserId($device_id);
        $ret1 = $obj->save();
        $redis = Base_Redis::getInstance();
        $ret2 = $redis->hAdd(Praise_Keys::getHashKeyByType($type),$obj_id,time());
        return $ret1&&$ret2;
    }
    
    
    /**
     * 根据type获取点赞的人数
     * @param integer $type
     * @param integer $objId
     * @return integer
     */
    public function getPraiseNum($objId,$type=1){
        $redis = Base_Redis::getInstance();
        $ret = $redis->zRangeByScore(Praise_Keys::getHashKeyByType($type),$objId,$objId);
        if(empty($ret)){
            return 0;
        }
        return count($ret);
    }
    
    /**
     * 获取最近一个月的点赞
     * @param unknown $type
     * @param unknown $objId
     */
    public function getLatePraiseNum($objId,$type=1){
        $redis = Base_Redis::getInstance();
        $count = 0;
        $end = time();
        $start = strtotime("-1 month");
        $ret = $redis->zRangeByScore(Praise_Keys::getHashKeyByType($type),$objId,$objId);
        if(empty($ret)){
            return 0;
        }
        foreach ($ret as $key => $val){
            if(($val >= $start) && ($val <= $end)){
                $count += 1;
            }
        }
        return $count;
    }
}