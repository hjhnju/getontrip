<?php
/**
 * 反馈意见接口
 * @author huwei
 *
 */
class Advise_Api{
    
    /**
     * 接口1：Advise_Api::listAdvise($type,$page,$pageSize)
     * 根据类型查询反馈意见
     * @param integer $type，意见类型：1 未解决，2 已经解决
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function listAdvise($type,$page,$pageSize){
        $logicAdvise = new Advise_Logic_Advise();
        return $logicAdvise->listAdvise('');
    }
    
    /**
     * 接口2：Advise_Api::addAnswer($adviseId, $strContent)
     * 对某个反馈进行处理
     * @param integer $adviseId
     * @param string  $strContent
     * @return boolean
     */
    public static function addAnswer($adviseId, $strContent){
        $logicAdvise = new Advise_Logic_Advise();
        return $logicAdvise->addAnswer($adviseId, $strContent);
    }
}