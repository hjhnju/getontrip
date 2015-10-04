<?php
/**
 * 新建编辑 话题
 * @author fanyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    public function execute() {

       $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
        
 
        //获取所有标签
        $tagList = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::NORMAL));
        $tagList=$tagList['list'];

        //获取通用标签
        $generalTag = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::GENERAL));
        $generalTag = $generalTag['list'];
      
        $generalTag = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::GENERAL));
        $generalTag = $generalTag['list'];
        
        
        $sightList=array();

        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
       
        $postInfo=Topic_Api::getTopicById($postid); 

        if(empty($postInfo)){
            $action='add';

            //处理传递过来的景点
            $sight_id  = isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):'';
            if($sight_id!=''){
               $sight=Sight_Api::getSightById($sight_id);
               array_push($sightList,$sight); 
            } 

        }else{
 
           //处理状态值  
           $postInfo["statusName"] = Topic_Type_Status::getTypeName($postInfo["status"]);  
          
           //处理被选中的标签
             $tagSelected=$postInfo['tags']; 
            $tag_id_array=array();
            for($i=0; $i<count($tagSelected); $i++) {
            	array_push($tag_id_array, $tagSelected[$i]['tag_id']);
            }
            for($i=0; $i<count($tagList); $i++) {   
                if(in_array($tagList[$i]["id"],$tag_id_array)){ 
                    $tagList[$i]["selected"]="selected";
                }    
            }

            //处理所选景点
            $sightSelected=$postInfo['sights'];  
            for($i=0; $i<count($sightSelected); $i++) {
                $sight=Sight_Api::getSightById($sightSelected[$i]['sight_id']);
                array_push($sightList,$sight);
            } 
            //处理来源名称、类型
            $sourceInfo = Source_Api::getSourceInfo($postInfo['from']);
            $postInfo["fromName"]=$sourceInfo['name'];
            $postInfo["fromType"]=empty($sourceInfo['url'])?3:$sourceInfo['type'];

  
            //处理图片名称 分割为hash 和 img_type 
            if(!empty($postInfo["image"])){
               $img=Base_Image::getImgParams($postInfo["image"]);
               $postInfo["img_hash"] = $img['img_hash'];
               $postInfo["img_type"] = $img['img_type'];
            }  

            $this->getView()->assign('post', $postInfo);
           

         }
         
        
        /*$sourceList = Source_Api::searchSource(array("type"=>2),1, PHP_INT_MAX);
        $sourceList=$sourceList['list']; */
        $sourceList =  Source_Api::getHotSource();


        if($action==Admin_Type_Action::ACTION_VIEW){
            $this->getView()->assign('disabled', 'disabled');
        } 
        
        

        $this->getView()->assign('sourceList', $sourceList);
        $this->getView()->assign('tag_id_array', $tag_id_array);
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
        $this->getView()->assign('tagList', $tagList);
        $this->getView()->assign('generalTag',$generalTag);
        $this->getView()->assign('sightList', $sightList);
        $this->getView()->assign('generalTag',$generalTag); 
    }
}
