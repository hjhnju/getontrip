<?php
class Sight_Logic_Sight extends Base_Logic{
    
    private $_fileds = array('id','name','describe','level','city_id','x','y','image','hastopic','create_user','create_time','update_user','update_time','status');    
    
    protected $logicTopic;
    
    protected $logicSightTag;
    
    protected $modelSight;
    
    const DEFAULT_HOT_PERIOD = 30;
    
    const REDIS_TIMEOUT = 3600;
    
    const ORDER_HOT = 1;
    
    const ORDER_NEW = 2;
    
    public function __construct(){
        $this->logicTopic    = new Topic_Logic_Topic();
        $this->logicSightTag = new Sight_Logic_Tag();
        $this->modelSight    = new SightModel();
    }
    
    /**
     * 根据景点ID获取景点详情
     * @param integer $sightId
     * @return array
     */
    public function getSightById($sightId){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' => $sightId));
        $arrSight =  $objSight->toArray();
        
        $listSightTag = new Sight_List_Tag();
        $listSightTag->setFilter(array('sight_id' => $sightId));
        $arrTags      = $listSightTag->toArray();
        $arrSight['tags'] = $arrTags['list'];
        return $arrSight;
    }
    
    /**
     * 根据景点ID获取景点及话题信息，支持带标签筛选及热度的时间范围设置
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @param string $strTags
     * @return array
     */
    public function getSightDetail($sightId,$page,$pageSize,$order,$strTags = ''){
        $arrRet      = array();
        $arrDataTags = array();
        $arrDataTags = $this->logicSightTag->getTagsBySight($sightId);
        if(empty($strTags)){//默认选中第一个标签来筛选话题
            $strTags = isset($arrDataTags[0]['id'])?$arrDataTags[0]['id']:'';
        }
        
        if($strTags == Tag_Type_Tag::STR_LANDSCAPE){
            $logic      = new Keyword_Logic_Keyword();
            $arrRet     = $logic->getKeywordList($sightId,$page,$pageSize);
        }elseif($strTags == Tag_Type_Tag::STR_BOOK){
            $logic      = new Book_Logic_Book();
            $arrRet     = $logic->getBookList($sightId,$page,$pageSize,array('status' => Book_Type_Status::PUBLISHED));
        }elseif($strTags == Tag_Type_Tag::STR_VIDEO){
            $logic      = new Video_Logic_Video();
            $arrRet     = $logic->getVideoList($sightId,$page,$pageSize);
        }else{
            $redis       = Base_Redis::getInstance();
            if(self::ORDER_NEW == $order){
                $arrRet =  $this->logicTopic->getNewTopic($sightId,self::DEFAULT_HOT_PERIOD,$page,$pageSize,$strTags);
            }else{
                $arrRet =  $this->logicTopic->getHotTopic($sightId,self::DEFAULT_HOT_PERIOD,$page,$pageSize,$strTags);
            }
            //$logicTag = new Tag_Logic_Tag();
            // foreach ($arrRet as $key => $val){
            //     $arrRet[$key]['tags'] = $logicTag->getTopicTags($val['id']);
            // }
        }
        return array(
            'tags'=>$arrDataTags,
            'data'=>$arrRet,           
        );
    }
    
    /**
     * 获取景点列表
     * @param integer $page
     * @param integer $pageSize
     * @param integer $cityId
     * @return array
     */
    public function getSightListByCity($page,$pageSize,$cityId){
        $arrRet    = array();
        $listSight = new Sight_List_Sight();
        if(!empty($cityId)){
            $listSight->setFilter(array('city_id' => $cityId));
        }        
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $arrRet  = $listSight->toArray();  
        return $arrRet;
    }
    
    /**
     * 获取景点列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getSightList($page,$pageSize,$status){
        $listSight = new Sight_List_Sight();
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $listSight->setFilter(array('status' => $status));
        $arrRet = $listSight->toArray();
        return $arrRet;
    }
    
    /**
     * 根据话题ID获取景点id数组
     * @param integer $topicId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getSightByTopic($topicId,$page=1,$pageSize=PHP_INT_MAX){
        $listSightTopic = new Sight_List_Topic();
        $listSightTopic->setFields(array('sight_id'));
        $listSightTopic->setFilter(array('topic_id' => $topicId));
        $listSightTopic->setPage($page);
        $listSightTopic->setPagesize($pageSize);
        $ret = $listSightTopic->toArray();
        return $ret;
    }
    
    /**
     * 根据条件数组筛选景点
     * @param array $arrInfo，条件数组，如:array('id'=1);
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function querySights($arrInfo,$page,$pageSize){        
        $listSight = new Sight_List_Sight();
        if(isset($arrInfo['status']) && ($arrInfo['status'] == Sight_Type_Status::ALL)){
            unset($arrInfo['status']);
        }
        $listSight->setFilter($arrInfo);
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $arrRet = $listSight->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $arrRet['list'][$key]['tags']      = Tag_Api::getTagBySight($val['id']);
            $arrRet['list'][$key]['book_num']  = intval(Book_Api::getBookNum($val['id']));
            $arrRet['list'][$key]['video_num'] = intval(Video_Api::getVideoNum($val['id']));
        }
        return $arrRet;
    }
    
    /**
     * 对景点进行搜索
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function search($query,$page,$pageSize){
        $logicCollect = new Collect_Logic_Collect();
        $logicComment = new Comment_Logic_Comment();
        $logicTopic   = new Topic_Logic_Topic();  
        $arrSight     = Base_Search::Search('sight', $query, $page, $pageSize, array('id'));
        foreach ($arrSight as $key => $val){
            $sight = $this->getSightById($val['id']);
            $arrSight[$key]['name']  = empty($val['name'])?trim($sight['name']):$val['name'];
            $arrSight[$key]['image'] = isset($sight['image'])?Base_Image::getUrlByName($sight['image']):'';
        
            $strTopicIds   = $logicTopic->getTopicIdBySight($val['id']);
            $arrTopicIds   = explode(",",$strTopicIds);
            //评论数
            $count         = 0;
            foreach ($arrTopicIds as $id){
                $count    += $logicComment->getTotalCommentNum($val['id']);
            }
            //话题数
            $topic_num     = $this->getTopicNum($val['id']);
            
            //书籍数
            $book_num      = Book_Api::getBookNum($val['id']);
            
            //视频数
            $video_num     = Video_Api::getVideoNum($val['id']);
            
            //景观数
            $keyword_num   = Keyword_Api::getKeywordNum($val['id']);
            
            $arrSight[$key]['desc']  = sprintf("%d个内容，%d个话题",$count,$topic_num);
        }
        return $arrSight;
    }
    
    public function querySightByPrefix($query,$page,$pageSize){
        $arrRet = array();
        $filter = "`name` like '$query"."%'";
        $listSight = new Sight_List_Sight();
        $listSight->setFilterString($filter);
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $ret = $listSight->toArray();
        foreach ($ret['list'] as $val){
            $arrRet[] = array(
                'id'   => $val['id'],
                'name' => $val['name'],
            );
        }
        return $arrRet;
    }
    
    /**
     * 根据ID删除景点信息
     * @param integer $id
     * @return boolean
     */
    public function delSight($id){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' => $id));
        $ret = $objSight->remove();
        
        //删除景点话题关系
        $redis = Base_Redis::getInstance();
        $listSightTopic = new Sight_List_Topic();
        $listSightTopic->setFilter(array('sight_id' => $id));
        $listSightTopic->setPagesize(PHP_INT_MAX);
        $arrSightTopic = $listSightTopic->toArray();
        foreach ($arrSightTopic['list'] as $val){
            $objSightTopic = new Sight_Object_Topic();
            $objSightTopic->fetch(array('id' => $val['id']));
            $objSightTopic->remove();
        }
        //删除redis缓存
        $redis->delete(Sight_Keys::getSightTopicKey($id));
        $redis->delete(Sight_Keys::getSightTongjiKey($id));
        
        //删除景点书籍关系
        $listSightBook = new Sight_List_Book();
        $listSightBook->setFilter(array('sight_id' => $id));
        $listSightBook->setPagesize(PHP_INT_MAX);
        $arrSightBook = $listSightBook->toArray();
        foreach ($arrSightBook['list'] as $val){
            $objSightBook = new Sight_Object_Book();
            $objSightBook->fetch(array('id' => $val['id']));
            $objSightBook->remove();
        }
        
        //删除景点标签关系
        $listSightTag = new Sight_List_Tag();
        $listSightTag->setFilter(array('sight_id' => $id));
        $listSightTag->setPagesize(PHP_INT_MAX);
        $arrSightTag = $listSightTag->toArray();
        foreach ($arrSightTag['list'] as $val){
            $objSightTag = new Sight_Object_Tag();
            $objSightTag->fetch(array('id' => $val['id']));
            $objSightTag->remove();
        }
        
        //删除景点搜索标签关系        
        $listSightSearch = new Search_List_Label();
        $listSightSearch->setFilter(array('obj_id' => $id,'type' => Search_Type_Label::SIGHT));
        $listSightSearch->setPagesize(PHP_INT_MAX);
        $arrSightSearch = $listSightSearch->toArray();
        foreach ($arrSightSearch['list'] as $val){
            $objSightSearch = new Search_Object_Label();
            $objSightSearch->fetch(array('id' => $val['id']));
            $objSightSearch->remove();
        }
        
        //删除景点词条
        $listKeyword = new Keyword_List_Keyword();
        $listKeyword->setFilter(array('sight_id' => $id));
        $listKeyword->setPagesize(PHP_INT_MAX);
        $arrKeyword  = $listKeyword->toArray();
        foreach ($arrKeyword['list'] as $val){
            $objKeyword = new Keyword_Object_Keyword();
            $objKeyword->fetch(array('id' => $val['id']));
            $objKeyword->remove();
        }
        $keys = $redis->keys(Keyword_Keys::getWikiInfoName($id, '*'));
        $keys = array_merge($keys,$redis->keys(Keyword_Keys::getWikiCatalogName($id, '*','*')));
        foreach ($keys as $key){
            $redis->delete($key);
        }
        return $ret;
    }
    
    /**
     * 根据$arrInfo添加景点
     * @param array $arrInfo:array('name' => 'xxx','level' => 'xxx');
     * @return integer:更新影响的行数，返回非零值正确
     */
    public function addSight($arrInfo){
        $objSight = new Sight_Object_Sight();
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fileds)){
                $key = $this->getprop($key);           
                $objSight->$key = $val;
            }           
        }
        $ret = $objSight->save();
        if(isset($arrInfo['tags'])){
            $arrTags      = $arrInfo['tags'];
            foreach ($arrTags as $id){
                $objSightTag = new Sight_Object_Tag();
                $objSightTag->sightId = $objSight->id;
                $objSightTag->tagId   = $id;
                $objSightTag->save();
            }
        }
        if($ret && isset($arrInfo['status']) && ($arrInfo['status'] == Sight_Type_Status::PUBLISHED)){
            $data = $this->modelSight->query(array('name' => $arrInfo['name']), 1, 1);
            $conf = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
            $url  = $_SERVER["HTTP_HOST"]."/InitData?sightId=".$data[0]['id']."&type=All&num=".$conf['thirddata'] ['initnum'];
            $http = Base_Network_Http::instance()->url($url);
            $http->timeout(1);
            $http->exec();
        }
        return $ret; 
    }
    
    /**
     * 根据$_updateData更新景点信息
     * @param integer $sightId
     * @param array $_updateData: array('describe' =>'xxx','name' => 'xxx');
     * @return integer:更新影响的行数，返回非零值正确
     */
    public function editSight($sightId,$arrInfo){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' =>$sightId));
        foreach ($arrInfo as $key => $val){
            $key = $this->getprop($key);
            if(in_array($key,$this->_fileds)){
                $objSight->$key = $val;
            }           
        }
        $listSightTag = new Sight_List_Tag();
        $listSightTag->setFilter(array('sight_id' => $sightId));
        $listSightTag->setPagesize(PHP_INT_MAX);
        $arrSightTag = $listSightTag->toArray();
        foreach ($arrSightTag['list'] as $val){
            $objSightTag = new Sight_Object_Tag();
            $objSightTag->fetch(array('id' => $val['id']));
            $objSightTag->remove();
        }
        if(isset($arrInfo['tags'])){
            $arrTags      = $arrInfo['tags'];
            foreach ($arrTags as $id){
                 $objSightTag = new Sight_Object_Tag();
                 $objSightTag->sightId = $objSight->id;
                 $objSightTag->tagId   = $id;
                 $objSightTag->save();
            }
        }
        $ret = $objSight->save();
        if($ret && isset($arrInfo['status']) && ($arrInfo['status'] == Sight_Type_Status::PUBLISHED)){
            $data = $this->modelSight->query(array('id' => $sightId), 1, 1);
            $conf = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
            $url  = $_SERVER["HTTP_HOST"]."/InitData?sightId=".$data[0]['id']."&type=All&num=".$conf['thirddata'] ['initnum'];
            $http = Base_Network_Http::instance()->url($url);
            $http->timeout(1);
            $http->exec();
        }
        return $ret;
    }
    
    
    public function publishSight($sightId,$bDoPublish){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' =>$sightId));
        if($bDoPublish){
            $objSight->status = Sight_Type_Status::PUBLISHED;
        }else{
            $objSight->status = Sight_Type_Status::NOTPUBLISHED;
        }
        $ret = $objSight->save();
        if($ret && $bDoPublish){
            $data = $this->modelSight->query(array('id' => $sightId), 1, 1);
            $conf = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
            $url  = $_SERVER["HTTP_HOST"]."/InitData?sightId=".$data[0]['id']."&type=All&num=".$conf['thirddata'] ['initnum'];
            $http = Base_Network_Http::instance()->url($url);
            $http->timeout(1);
            $http->exec();
        }
        return $ret;
    }
    
    /**
     * 获取景点的话题数
     * @param integer $sightId
     * @return integer
     */
    public function getTopicNum($sightId='',$arrConf = array()){
        if(array('status' => Topic_Type_Status::PUBLISHED) == $arrConf && (!empty($sightId))){
            $redis  = Base_Redis::getInstance();
            $ret    = $redis->hGet(Sight_Keys::getSightTongjiKey($sightId),Sight_Keys::TOPIC);
            if(!empty($ret)){
                return $ret;
            }
        }
        $count = 0;
        $listSightTopic = new Sight_List_Topic();
        $objTopic = new Topic_Object_Topic();
        if(!empty($sightId)){
            $listSightTopic->setFilter(array('sight_id' => $sightId));
        }
        $listSightTopic->setFields(array('topic_id'));
        $listSightTopic->setPagesize(PHP_INT_MAX);
        $arr = $listSightTopic->toArray();
        foreach ($arr['list'] as $topicId){
            $arrFilter = array_merge(array('id' => $topicId['topic_id']),$arrConf);
            $objTopic->fetch($arrFilter);
            if(isset($objTopic->id)){
                $count += 1;
            }
        }
        if(array('status' => Topic_Type_Status::PUBLISHED) == $arrConf && (!empty($sightId))){
            $redis  = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sightId),Sight_Keys::TOPIC,$count);
        }
        return $count;
    }
    
    /**
     * 获取景点词条数
     * @param integer  $sightId
     * @return integer
     */
    public function getKeywordNum($sightId){
        $listKeyword = new Keyword_List_Keyword();
        $listKeyword->setFilter(array('sight_id' => $sightId));
        return intval($listKeyword->getTotal());
    }
    
    /**
     * 根据条件获取景点数量
     * @param array $arrInfo
     * @return integer
     */
    public function getSightsNum($arrInfo,$cityId = ''){
        $listSight = new Sight_List_Sight();
        if(!empty($cityId)){
            $arrInfo = array_merge($arrInfo,array('city_id' => $cityId));
        }
        if(!empty($arrInfo)){
            $listSight->setFilter($arrInfo);
        }
        $listSight->setPagesize(PHP_INT_MAX);
        $arrRet    = $listSight->toArray();        
        return $arrRet['total'];
    }
    
    /**
     * 检查所给的景点名是否存在
     * @param string $name
     */
    public function checkSightName($name){
        $ret = $this->querySights(array('name'=>$name), 1, 1);
        if(empty($ret['list'])){
            return false;
        }
        return true;
    }
}