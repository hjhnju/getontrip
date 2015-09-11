<?php
/**
 * 后台首页
 * @author jiangsongfang
 *
 */
class IndexAction extends Yaf_Action_Abstract {
	/**
	 * TODO：Admin首页展示数据获取
	 * @author yibing
	 *
	 */
	public function execute() { 

          //查询景点总数
          $sightNum = Sight_Api::getSightNum(array('status'=>Sight_Type_Status::PUBLISHED));
          $this->getView()->assign('sightNum', $sightNum); 

          //查询已经发布话题总数
          $arrInfo = array(
             'status'=>Topic_Type_Status::PUBLISHED
          );
          $topicNum = Topic_Api::getTopicNum($arrInfo);
          $this->getView()->assign('topicNum', $topicNum); 

          //查询用户总数
         /* $sightNum = User_Api::getUserList($page,$pageSize,$arrParams = array());
          $this->getView()->assign('sightNum', $sightNum); 
*/
   }
}




