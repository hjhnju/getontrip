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
        $tagList = Tag_Api::getTagList(1, PHP_INT_MAX);
        $tagList=$tagList['list'];
      
        $sightList=array();

        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
       
        $List=Topic_Api::search(array("id"=>$postid),1,1); 

        if(count($List["list"])==0){
                $action='add';
        }else{

           $postInfo=$List["list"][0]; 
           //处理状态值  
           $postInfo["statusName"] = Topic_Type_Status::getTypeName($postInfo["status"]);  
           $this->getView()->assign('post', $postInfo);
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
                array_push($sightList,(array)$sight);
            }

         }
         
        
        $sourceList = Source_Api::listSource(1, PHP_INT_MAX);
        $sourceList=$sourceList['list']; 

        if($action==Admin_Type_Action::ACTION_VIEW){
            $this->getView()->assign('disabled', 'disabled');
        } 
        


        $this->getView()->assign('sourceList', $sourceList);
        $this->getView()->assign('tag_id_array', $tag_id_array);
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
        $this->getView()->assign('tagList', $tagList);
        $this->getView()->assign('sightList', $sightList);
    }
}
