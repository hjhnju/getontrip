<?php 
class Msg_Api {    
    
    /**
     * 接口1：Msg_Api::sendmsg($intType, $toid = '', $arrParam = array(), $fromid = 0)
     * 发送系统消息
     * @param integer $intType：消息类型，定义见 Msg_Type 
     * @param string  $image:消息的背景图片
     * @param integer $toid，收消息用户，如为空，则收消息人为全体用户
     * @param array   $arrParam
     * @return true|false 成功true, 失败 false
     */
    public static function sendmsg($intType, $image = '',$toid = '', $arrParam = array(), $fromid = 0) {
        $logicMsg = new Msg_Logic_Msg();
        return $logicMsg->sendmsg($intType, $image, $toid, $arrParam, $fromid);
    }
    
    /**
     * 接口2：Msg_Api::getUnreadMsgNum($uid)
     * 获取未读消息数
     * @param integer $uid,用户ID
     * @return integer 
     */
    public static function getUnreadMsgNum($uid){
        $logicMsg = new Msg_Logic_Msg();
        return  $logicMsg->getUnread($uid);
    }
    
    /**
     * 接口3：Msg_Api::queryMsg($intPage, $intPageSize,$arrParams)
     * 获取消息列表
     * @param integer $intPage
     * @param integer $intPageSize
     * @param array $arrParams
     * @return array
     */
    public static function queryMsg($intPage, $intPageSize,$arrParams){
        $logicMsg = new Msg_Logic_Msg();
        return $logicMsg->queryMsg($intPage, $intPageSize,$arrParams);
    }
}