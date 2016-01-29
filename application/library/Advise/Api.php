<?php
/**
 * 反馈意见接口
 * @author huwei
 *
 */
class Advise_Api{
    
    /**
     * 接口1：Advise_Api::getAdviseList($page,$pageSize,$arrParams = array())
     * 根据类型查询反馈意见
     * @param integer $arrParams,参数数组，其中status，意见类型：1 未解决，2 已经解决，全部类型就不用传
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getAdviseList($page,$pageSize,$arrParams = array()){
        $logicAdvise = new Advise_Logic_Advise();
        return $logicAdvise->getAdviseList($page,$pageSize,$arrParams);
    }
    
    /**
     * 接口2：Advise_Api::getAdviseById($adviseId)
     * 根据ID查询反馈意见
     * @param integer $adviseId
     * @return array
     */
    public function getAdviseById($adviseId){
        $logicAdvise = new Advise_Logic_Advise();
        return $logicAdvise->getAdviseById($adviseId);
    }
    
    /**
     * 接口3：Advise_Api::addAnswer($adviseId, $strContent,$status)
     * 对某个反馈进行处理
     * @param integer $adviseId
     * @param string  $strContent
     * @return boolean
     */
    public static function addAnswer($adviseId, $strContent,$status){
        $logicAdvise = new Advise_Logic_Advise();
        return $logicAdvise->addAnswer($adviseId, $strContent,$status);
    }
    
    /**
     * 接口4：Advise_Api::getAutoAnswer()
     * 获取自动回复模板
     * @return array
     */
    public static function getAutoAnswer(){
        $logicAdvise = new Advise_Logic_Advise();
    }
    
    public static function getAdviseNum($status = ''){
        $logicAdvise = new Advise_Logic_Advise();
        return $logicAdvise->getAdviseNum($status);
    }
}