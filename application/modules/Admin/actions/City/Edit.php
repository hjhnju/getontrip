<?php
/**
 * 新建编辑 城市
 * @author fanyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    public function execute() {

        $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
         
        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
       
        $postInfo=City_Api::getCityById($postid); 
        
        //获取通用标签
        $generalTag = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::GENERAL));
        $generalTag = $generalTag['list'];
        

        if(empty($postInfo)){
            $action='add'; 

        }else { 
            if(empty($postInfo["status"])){
                $postInfo["x"]=0;
                $postInfo["y"]=0; 
                $postInfo["status"]='';
            }  
           $this->getView()->assign('post', $postInfo); 
           
           //处理被选中的标签
           $tagSelected=$postInfo['tags'];
           $tag_id_array=array();
           for($i=0; $i<count($tagSelected); $i++) {
               array_push($tag_id_array, $tagSelected[$i]['id']);
           }
           for($i=0; $i<count($generalTag); $i++) {
               if(in_array($generalTag[$i]["id"],$tag_id_array)){
                   $generalTag[$i]["selected"]="selected";
               }
           }

        } 
        if($action==Admin_Type_Action::ACTION_VIEW){
            $this->getView()->assign('disabled', 'disabled');
        } 
         
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action)); 
        
        $this->getView()->assign('generalTag',$generalTag);
    }
}
