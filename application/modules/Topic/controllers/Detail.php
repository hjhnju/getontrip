<?php
/**
 * 话题详情页面
 * @author fyy
 */
class DetailController extends Base_Controller_Page {
    
     
   
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
    }
    
    /**
     *  详情
     */
    public function indexAction() {    
       $request = $this->getRequest();
       $param   = $request->getParams();
       $postid   = isset($param['id'])? trim($param['id']) : intval($_REQUEST['id']);
       $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
       $sightId   = isset($_REQUEST['sightId'])?trim($_REQUEST['sightId']):''; 
       
       $tagId   = isset($_REQUEST['tagId'])?trim($_REQUEST['tagId']):'';  
       $sd   = isset($_REQUEST['sd'])?trim($_REQUEST['sd']):''; 
       $cd   = isset($_REQUEST['cd'])?trim($_REQUEST['cd']):'';  
       //根据标签id获取标签名称 
       if (!empty($tagId)&&(!empty($sd)||!empty($cd))) {
          $tag = Tag_Api::getTagById($tagId);
          $href = empty($sd)?'/m/city?id='.$cd:'/m/sight?id='.$sd;
          $this->getView()->assign('tagName', $tag['name']); 
          $this->getView()->assign('href',  $href.'&tagId='.$tagId);
       }
      


       if(empty($postid)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
       }
     
       $logic      = new Topic_Logic_Topic();
       if(is_int($postid) && $postid < 1448){
           $postInfo    = $logic->getTopicDetail($postid,$sightId);
       }else{
           $postInfo    = $logic->getTopicDetail(Base_Util_Secure::decryptForUuap(Base_Util_Secure::PASSWD_KEY,$postid),$sightId);
       }
       //$postInfo = Topic_Api::getTopicById($postid);
       if(!isset($postInfo['id'])){
          $this->getView()->assign('post', array()); 
       }else{ 
          //增加访问统计
          $logicVisit = new Tongji_Logic_Visit();
          $logicVisit->addVisit(Tongji_Type_Visit::TOPIC,$postInfo['id']);
          
          //记一条业务日志
          $arrLog = array(
              'type'     => 'visit',
              'uid'      => User_Api::getCurrentUser(),
              'obj_id'   => $postInfo['id'],
              'obj_type' => 'topic',
          );
          Base_Log::NOTICE($arrLog);

           //处理来源 
          /* $sourceInfo = Source_Api::getSourceInfo($postInfo['from']);
           $postInfo['from_name'] = $sourceInfo['name'];*/

           //处理背景图片
           if(!empty($postInfo["image"])){
             $imgParams = Base_Image::getImgParams($postInfo["image"]);
             $postInfo["img_hash"] = $imgParams['img_hash'];
             $postInfo["img_type"] = $imgParams['img_type'];
           }/*else{
              $postInfo["image"] = $web->stroot . '/v1/' . $web->version . '/asset/common/img/imgloading.png'; 
           }*/
           
           //处理正文图片
           if($postInfo['content'] != ""){
               $spider  = Spider_Factory::getInstance("Filterimg",$postInfo['content'],Spider_Type_Source::STRING);
               $postInfo['content'] = $spider->getContentToDis();
           }

           $this->getView()->assign('post', $postInfo);
           $this->getView()->assign('title', $postInfo['title']);  
       } 
       
       //判断是否来自移动端
       $isMobile = Base_Util_Mobile::isMobile();
       //判断是否来自app
       $isapp = isset($_REQUEST['isapp'])?$_REQUEST['isapp'] : 0; 
       $this->getView()->assign('isMobile', $isMobile); 
       $this->getView()->assign('isapp', $isapp); 

       //$this->getView()->assign('isfromApp', $isapp); 

       
    }
    

    /**
     *  预览页面
    */
    public function previewAction() {  
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
       return $this->redirect('/topic/detail?id='.$postid); 

       
       $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):''; 
       $logic      = new Topic_Logic_Topic();
       $postInfo    = $logic->getTopicDetail($postid,$deviceId); 
       //$postInfo = Topic_Api::getTopicById($postid);
       if(!isset($postInfo['id'])){
          $this->getView()->assign('post', array()); 
       }else{ 
        
           //处理来源 
          /* $sourceInfo = Source_Api::getSourceInfo($postInfo['from']);
           $postInfo['from_name'] = $sourceInfo['name'];*/

           //处理背景图片
           if(!empty($postInfo["image"])){
             $imgParams = Base_Image::getImgParams($postInfo["image"]);
             $postInfo["img_hash"] = $imgParams['img_hash'];
             $postInfo["img_type"] = $imgParams['img_type'];
           }

           //处理正文图片
           $content = $postInfo['content'];  
           if($content != ""){
              $spider = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
              $postInfo['content'] = $spider->getContentToDis(); 
           }

           $this->getView()->assign('post', $postInfo); 
       } 
       
       //判断是否来自移动端
       $isMobile = Base_Util_Mobile::isMobile();
       //判断是否来自app
       $isapp = isset($_REQUEST['isapp'])? $_REQUEST['isapp'] : 0; 
       $this->getView()->assign('isMobile', $isMobile); 
       $this->getView()->assign('isapp', $isapp);   
    }
}
