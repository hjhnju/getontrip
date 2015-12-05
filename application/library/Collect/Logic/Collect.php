<?php
/**
 * 收藏接口层
 * @author huwei
 */
class Collect_Logic_Collect{
    
    protected $logicUser;
    
    public function __construct(){
        $this->logicUser = new User_Logic_User();
    }
    
    /**
     * 添加收藏逻辑层,同时注意更新redis中的统计数据
     * @param integer $type
     * @param integer $user_id
     * @param integer $obj_id
     * @return boolean 
     */
    public function addCollect($type, $user_id, $obj_id){
        $obj         = new Collect_Object_Collect();
        $obj->type   = $type;
        $obj->objId  = $obj_id;
        $obj->userId = $user_id;
        $ret         = $obj->save();
        $redis       = Base_Redis::getInstance();
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($obj_id,'*'));
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($obj_id));    

        //更新下热度信息
        return $ret;
    }
    
    /**
     * 删除收藏逻辑层,同时注意更新redis中的统计数据
     * @param integer $type
     * @param integer $user_id
     * @param integer $obj_id
     * @return boolean
     */
    public function delCollect($type, $user_id, $obj_id){
        $obj         = new Collect_Object_Collect();
        $obj->fetch(array('type' => $type,'obj_id' => $obj_id,'user_id'=> $user_id));
        $ret         = $obj->remove();
        $redis       = Base_Redis::getInstance();
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($obj_id,'*'));
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($obj_id));    
        //更新下热度信息
        return $ret;
    }
    
    /**
     * 检测用户是否收藏过
     * @param integer $type
     * @param integer $obj_id
     * @return boolean
     */
    public function checkCollect($type, $obj_id){        
        $user_id     = User_Api::getCurrentUser();
        if(empty($user_id)){
            return false;
        }
        $obj = new Collect_Object_Collect();
        $obj->fetch(array(
            'type'    => $type,
            'user_id' => $user_id,
            'obj_id'  => $obj_id,
        ));
        if(!empty($obj->id)){
            return true;
        }
        return false;
    }
    
    /**
     * 获取收藏信息
     * @param integer $type
     * @param integer $user_id
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCollect($type, $user_id, $page, $pageSize){
        $arrRet      = array();
        if($type !== Collect_Type::COTENT){
            $listCollect = new Collect_List_Collect();
            $listCollect->setFilter(array(
                'type'    => $type,
                'user_id' => $user_id,
            ));
            $listCollect->setPage($page);
            $listCollect->setPagesize($pageSize);
            $arrCollect = $listCollect->toArray();
            if(empty($arrCollect['list'])){
                return array();
            }
        }        
        switch ($type){
            case Collect_Type::SIGHT:
                $logicSight = new Sight_Logic_Sight();
                foreach ($arrCollect['list'] as $val){
                    $temp['id']       = strval($val['obj_id']);
                    $sight            = $logicSight->getSightById($val['obj_id']);
                    if(empty($sight['name'])){
                        continue;
                    }
                    $temp['name']     = isset($sight['name'])?$sight['name']:'';
                    $temp['image']    = isset($sight['image'])?Base_Image::getUrlByName($sight['image']):'';
                    $temp['content']  = sprintf("%d个内容",$logicSight->getTopicNum($val['obj_id']));
                    $temp['collect']  = sprintf("%d人收藏",$this->getTotalCollectNum(Collect_Type::SIGHT, $val['obj_id']));
                    $arrRet[]         = $temp;
                }
                break;
            case Collect_Type::CITY:
                $logicCity = new City_Logic_City();
                foreach ($arrCollect['list'] as $val){
                    $temp['id']       = strval($val['obj_id']);
                    $city             = $logicCity->getCityById($val['obj_id']);
                    $temp['image']    = isset($city['image'])?Base_Image::getUrlByName($city['image']):'';
                    $temp['name']     = str_replace("市","",$city['name']);
                    $temp['content']  = sprintf("%d个内容",$logicCity->getTopicNum($val['obj_id']));
                    $temp['collect']  = sprintf("%d人收藏",$this->getTotalCollectNum(Collect_Type::CITY, $val['obj_id']));
                    $arrRet[]         = $temp;
                }
                break;
            case Collect_Type::COTENT:
                $listCollect = new Collect_List_Collect();
                $logicTopic  = new Topic_Logic_Topic();
                $logicPraise = new Praise_Logic_Praise();
                $strFilter   = "`user_id` =".$user_id." and `type` !=".Collect_Type::SIGHT." and `type` !=".Collect_Type::CITY;
                $listCollect->setFilterString($strFilter);
                $listCollect->setPage($page);
                $listCollect->setPagesize($pageSize);
                $arrCollect  = $listCollect->toArray();
                foreach ($arrCollect['list'] as $val){
                    $temp = array();
                    switch($val['type']){
                        case Collect_Type::TOPIC:                            
                            $temp['id']         = strval($val['obj_id']);
                            $topic              = $logicTopic->getTopicById($val['obj_id']);
                            if(empty($topic['title'])){
                                continue;
                            }
                            $temp['image']      = Base_Image::getUrlByName($topic['image']);
                            $temp['subtitle']   = trim($topic['subtitle']);
                            $temp['title']      = trim($topic['title']);
                            //内容收藏数
                            $temp['collect']    = strval($this->getTotalCollectNum(Collect_Type::TOPIC, $val['obj_id']));
                            
                            $temp['praise']    = strval($logicPraise->getPraiseNum($val['obj_id']));
                            //内容访问数
                            $temp['visit']      = strval($logicTopic->getTotalTopicVistPv($val['obj_id']));
                            $temp['type']       = strval(Collect_Type::TOPIC);
                            $arrRet[]           = $temp;
                            break;
                        case Collect_Type::BOOK:
                            $objBook = new Book_Object_Book();
                            $objBook->fetch(array('id' => $val['obj_id']));
                            $temp['id']     = strval($val['obj_id']);
                            $temp['title']  = $objBook->title;
                            $temp['author'] = $objBook->author;
                            $temp['image']  = Base_Image::getUrlByName($objBook->image);
                            $temp['collect']= strval($this->getTotalCollectNum(Collect_Type::BOOK, $val['obj_id']));
                            //内容访问数
                            $logicVisit = new Tongji_Logic_Visit();
                            $temp['visit']  = strval($logicVisit->getVisitCount(Collect_Type::BOOK, $val['obj_id']));
                            $temp['type']   = strval(Collect_Type::BOOK);
                            $arrRet[]       = $temp;
                            break;
                        case Collect_Type::VIDEO:
                            $objVideo = new Video_Object_Video();
                            $objVideo->fetch(array('id' => $val['obj_id']));
                            $temp['id']     = strval($val['obj_id']);
                            $temp['title']  = $objVideo->title;
                            $temp['image']  = Base_Image::getUrlByName($objVideo->image);
                            $temp['collect']= strval($this->getTotalCollectNum(Collect_Type::VIDEO, $val['obj_id']));
                            $temp['url']    = Base_Config::getConfig('web')->root.'/video/detail?id='.$temp['id'];
                            //内容访问数
                            $logicVisit = new Tongji_Logic_Visit();
                            $temp['visit']  = strval($logicVisit->getVisitCount(Collect_Type::VIDEO, $val['obj_id']));
                            $temp['type']   = strval(Collect_Type::VIDEO);
                            $arrRet[]       = $temp;
                            break;
                        default:
                            break;
                    }
                }
                break;
            default:
                break;                
        }
        return $arrRet;
    }
    
    /**
     * 根据type获取景点或话题或答案或主题收藏的人数
     * @param integer $type
     * @param integer $objId
     * @return integer
     */
    public function getTotalCollectNum($type,$objId){
        $redis = Base_Redis::getInstance();
        $ret = $redis->hGet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($objId));
        if(!empty($ret)){
            return $ret;
        }
        $list = new Collect_List_Collect();
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilter(array('type' => $type,'obj_id' => $objId));
        $arrRet = $list->toArray();
        $redis->hSet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($objId),$arrRet['total']);
        return $arrRet['total'];
    }
    
    /**
     * 获取最近的收藏量
     * @param unknown $type
     * @param unknown $objId
     */
    public function getLateCollectNum($type,$objId,$periods='',$dateType= 'DAY'){
        $redis = Base_Redis::getInstance();
        $count = 0;
        $end = time();
        if(empty($periods)){
            $start = 0;
        }else{
            if($dateType == 'DAY'){
                $start = strtotime($periods.' days ago');
            }else{
                $start = time() - 60*$periods;
            }
        }
        if($dateType == 'DAY'){
            $ret = $redis->hGet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($objId,$periods));
        }else{
            $ret = $redis->hGet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateMinuteKeyName($objId,$periods));
        }
        if(!empty($ret)){
            $count = $ret;
        }else{
            $list = new Collect_List_Collect();
            $filter = "`type` = $type and `obj_id` = $objId and `create_time` >= ".$start;
            $list->setPagesize(PHP_INT_MAX);
            $list->setFilterString($filter);
            $arrRet = $list->toArray();
            $count  = $arrRet['total'];
            if($dateType == 'DAY'){
                $redis->hSet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($objId,$periods),$count);
            }else{
                $redis->hSet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateMinuteKeyName($objId,$periods),$count);
            }
            
        }        
        return $count;
    }
    
    /**
     * 获取收藏信息
     * @param integer $page
     * @param integer $pageSize
     * @param array   $arrInfo
     * @return array
     */
    public function getCollectList($page, $pageSize, $arrInfo = array()){
        $list = new Collect_List_Collect();
        $list->setPage($page);
        $list->setPagesize($pageSize);
        if(!empty($arrInfo)){
            $list->setFilter($arrInfo);
        }
        return $list->toArray();
    }
}