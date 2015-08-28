<?php
/**
 * 黑名单API
 * @author huwei
 *
 */
class Black_Api{
    
    /**
     * 接口1：Black_Api::getList($type,$page,$pageSize)
     * 获取黑名单列表
     * @param integer $type
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getList($type,$page,$pageSize){
        $logicBlack = new Black_Logic_Black();
        return $logicBlack->getList($type,$page,$pageSize);
    }
    
    /**
     * 接口2：Black_Api::addBlack($id,$type)
     * 添加黑名单
     * @param integer $id
     * @param integer $type
     * @return boolean
     */
    public static function addBlack($id,$type){
        $logicBlack = new Black_Logic_Black();
        return $logicBlack->addBlack($id, $type);
    }
    
    /**
     * 接口3：Black_Api::cancelBlack($id,$type)
     * 删除黑名单
     * @param integer $id
     * @param integer $type
     * @return boolean
     */
    public static function cancelBlack($id,$type){
        $logicBlack = new Black_Logic_Black();
        return $logicBlack->cancelBlack($id, $type);
    }
}
