<?php
/**
 * 编辑景点
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
      	$actionArray = array(
                 "add"=>"新建",
                 "edit"=>"编辑",
                 "view"=>"查看"
      	);
        $levelArray = array(
              "","5A","4A","3A","2A","1A"
        );
        $action = isset($_REQUEST['action'])?$_REQUEST['action']:'add';

        //获取通用标签
        $generalTag = Tag_Api::getTagList(1, PHP_INT_MAX, array('type' => Tag_Type_Tag::GENERAL));
        $generalTag = $generalTag['list'];   

        $postid   = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        $postInfo = array();
        if($postid==''){
            $this->getView()->assign('post', '');
        }else{
            $postInfo  = Sight_Api::getSightById($postid); 
        }
        if(!empty($postInfo)){ 
           //获取城市名称
           $cityInfo=City_Api::getCityById($postInfo["city_id"]);
           $postInfo["city_name"]=$cityInfo["name"];
           $postInfo["level"]=trim($postInfo["level"]);
           $this->getView()->assign('post', $postInfo);

           //处理被选中的标签
            $tagSelected=$postInfo['tags']; 
            $tag_id_array=array();
            for($i=0; $i<count($tagSelected); $i++) {
              array_push($tag_id_array, $tagSelected[$i]['tag_id']);
            }
            for($i=0; $i<count($generalTag); $i++) {      
                if(in_array($generalTag[$i]["id"],$tag_id_array)){ 
                    $generalTag[$i]["selected"]="selected";
                }  
            }
        }

        $this->_view->assign('action', $actionArray[$action]);
        $this->_view->assign('levelArray', $levelArray);
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 


        $this->getView()->assign('generalTag',$generalTag);
    }
}
