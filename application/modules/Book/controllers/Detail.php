<?php
/**
 * 书籍详情页面
 * 2015-11-4
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
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if(empty($postid)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
       }
        
       //增加访问统计
       $logicVisit = new Tongji_Logic_Visit();
       $logicVisit->addVisit(Tongji_Type_Visit::BOOK,$postid);
        
       $logic    = new Book_Logic_Book();
       $postInfo      = $logic->getBookById($postid); 
         
       if(!isset($postInfo['id'])){
          $this->getView()->assign('post', array()); 
       }else{ 
        
           
           //处理背景图片
           /*if(!empty($postInfo["image"])){
             $imgParams = Base_Image::getImgParams($postInfo["image"]);
             $postInfo["img_hash"] = $imgParams['img_hash'];
             $postInfo["img_type"] = $imgParams['img_type'];
           }*//*else{
              $postInfo["image"] = $web->stroot . '/v1/' . $web->version . '/asset/common/img/imgloading.png'; 
           }*/
           
            

           $this->getView()->assign('post', $postInfo); 
       } 
       
       //判断是否来自移动端
       $isMobile = Base_Util_Mobile::isMobile();
       //判断是否来自app
        
       $this->getView()->assign('isMobile', $isMobile); 
         
    }
     

 
}
