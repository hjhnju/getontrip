<?php
class Imagetopic_Logic_Imagetopic extends Base_Logic{
    
    const ORDER_NEW = 1;
    
    const ORDER_HOT = 2;
    
    //前端用到的逻辑层
    public function detail($id){
        $objImageTopic = new Imagetopic_Object_Imagetopic();
        $objImageTopic->setFileds(array('id','title','content','image','owner'));
        $objImageTopic->fetch(array('id' => $id));
        $arrRet =  $objImageTopic->toArray();
        $arrRet['id']    = strval($arrRet['id']);
        $arrRet['image'] = Base_Image::getUrlByName($arrRet['image']);
        
        //图文作者信息
        $user   = User_Api::getUserById($arrRet['owner']);
        $arrRet['username'] = isset($user['nick_name'])?trim($user['nick_name']):'';
        unset($arrRet['owner']);
        
        //收藏信息
        $logicCollect = new Collect_Logic_Collect();
        $num = $logicCollect->getTotalCollectNum(Collect_Type::IMAGETOPIC, $id);
        $arrRet['collectnum'] = strval($num);
        $arrRet['collected']  = strval($logicCollect->checkCollect(Collect_Type::IMAGETOPIC, $id));
        
        //点赞信息
        $logicPraise = new Praise_Logic_Praise();
        $num = $logicPraise->getPraiseNum($id, Praise_Type_Type::IMAGETOPIC);
        $arrRet['praisenum'] = strval($num);
        $arrRet['praised']   = strval($logicPraise->checkPraise(Praise_Type_Type::IMAGETOPIC, $id));
        
        //评论信息
        $logicComment = new Comment_Logic_Comment();
        $num = $logicComment->getTotalCommentNum($id, Comment_Type_Type::IMAGETOPIC);
        $arrRet['commentnum'] = strval($num);       
        
        return $arrRet;
    }
    
    public function add(){
        
    }
    
    public function getList($sightId, $order, $page, $pageSize){
        $model  = new ImagetopicModel();
        $arrRet = array();
        $arrIds = $model->getHotImageTopicIds($sightId, $page, $pageSize, $order);
        foreach ($arrIds as $val){
            $temp          = array();
            $objImageTopic = new Imagetopic_Object_Imagetopic();
            $objImageTopic->fetch(array('id' => $val['id']));
            $temp['id'] = $objImageTopic->id;
            $temp['title'] = $objImageTopic->title;
            $temp['image'] = Base_Image::getUrlByName($objImageTopic->image);
            
            //图文作者信息
            $user   = User_Api::getUserById($objImageTopic->owner);
            $temp['username']  = isset($user['nick_name'])?trim($user['nick_name']):'';
            $temp['userimage'] = isset($user['image'])?Base_Image::getUrlByName($user['image']):'';
             
            $arrRet[] = $temp;
        }
        return $arrRet;
    }
    
    
    
    
    //后端用到的逻辑层
    public function getImageTopicById($id){
        $objImageTopic = new Imagetopic_Object_Imagetopic();
        $objImageTopic->fetch(array('id' => $id));
        return $objImageTopic->toArray();
    }
    
    public function getImageTopicList($page, $pageSize, $arrInfo = array()){
        $listImageTopic = new Imagetopic_List_Imagetopic();
        if(!empty($arrInfo)){
            $listImageTopic->setFilter($arrInfo);
        }
        $listImageTopic->setPage($page);
        $listImageTopic->setPagesize($pageSize);
        return $listImageTopic->toArray();
    }
}