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
    
    const STR_LANDSCAPE = 'landscape';
    
    const STR_VIDEO = 'video';
    
    const STR_BOOK = 'book';
    
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
        
        if(!isset($arrSight['name'])){
            $objSight = new Sight_Object_Meta();
            $objSight->fetch(array('id' => $sightId));
            $arrSight =  $objSight->toArray();
        }
        
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
        if($strTags == self::STR_LANDSCAPE){
            $logic      = new Keyword_Logic_Keyword();
            $arrRet     = $logic->getKeywordList($sightId,$page,$pageSize);
        }elseif($strTags == self::STR_BOOK){
            $logic      = new Book_Logic_Book();
            $arrRet     = $logic->getBookList($sightId,$page,$pageSize,array('status' => Book_Type_Status::PUBLISHED));
        }elseif($strTags == self::STR_VIDEO){
            $logic      = new Video_Logic_Video();
            $arrRet     = $logic->getVideoList($sightId,$page,$pageSize);
        }else{
            $redis       = Base_Redis::getInstance();
            if(self::ORDER_NEW == $order){
                $arrRet =  $this->logicTopic->getNewTopic($sightId,self::DEFAULT_HOT_PERIOD,$page,$pageSize,$strTags);
            }else{
                $arrRet =  $this->logicTopic->getHotTopic($sightId,self::DEFAULT_HOT_PERIOD,$page,$pageSize,$strTags);
            }
            $logicTag = new Tag_Logic_Tag();
            foreach ($arrRet as $key => $val){
                 $arrTags = array();
                 $tags = $logicTag->getTopicTags($val['id']);
                 if(!empty($tags)){
                     $arrTags[] = str_replace("其他", "", $tags[0]);
                 }
                 $arrRet[$key]['tags'] = $arrTags;
            }
        }
        $sight = Sight_Api::getSightById($sightId);
        return $arrRet;
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
        //$listSight->setOrder('`id` asc');
        $listSight->setOrder('`hot2` desc');
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
        $model = new SightModel();
        $ret = $model->getSightByTopic($topicId, $page, $pageSize);
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
            $arrRet['list'][$key]['topic_num'] = intval(Topic_Api::getTopicNum(array('sightId'=>$val['id'],'status'=>Topic_Type_Status::PUBLISHED)));
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
        $num          = $arrSight['num'];
        $arrSight     = $arrSight['data'];
        foreach ($arrSight as $key => $val){
            $sight = $this->getSightById($val['id']);
            $arrSight[$key]['name']  = empty($val['name'])?trim($sight['name']):$val['name'];
            $arrSight[$key]['image'] = isset($sight['image'])?Base_Image::getUrlByName($sight['image']):'';
        
            $arrSight[$key]['title']  = $arrSight[$key]['name'];
            $strTopicIds   = $logicTopic->getTopicIdBySight($val['id']);
            $arrTopicIds   = explode(",",$strTopicIds);
            //类别数
            $logic  = new Sight_Logic_Tag();
            $count  = count($logic->getTagsBySight($val['id'],"1.1"));
            
            //话题数
            $topic_num     = $this->getTopicNum($val['id'],array('status' => Topic_Type_Status::PUBLISHED));
            $wiki_num      = Keyword_Api::getKeywordNum($val['id']);
            $book_num      = Book_Api::getBookNum($val['id']);
            $video_num     = Video_Api::getVideoNum($val['id']);            
            
            $arrSight[$key]['desc']     = sprintf("%d个类别，%d个内容",$count,$topic_num + $video_num + $wiki_num + $book_num);
            $arrSight[$key]['content']  = $arrSight[$key]['desc'];
            
            $arrSight[$key]['search_type']  = 'sight';
        }
        return array('data' => $arrSight, 'num' => $num);
    }
    
    public function querySightByPrefix($query,$page,$pageSize){
        $arrRet = array();
        $filter = "`name` like '$query"."%'";
        $listSight = new Sight_List_Meta();
        $listSight->setFilterString($filter);
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $ret = $listSight->toArray();
        foreach ($ret['list'] as $val){
            $arrRet[] = array(
                'id'   => $val['id'],
                'name' => $val['name']."  ".$val['city'],
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
        //删除postgresql中数据
        $model = new GisModel();
        $ret   = $model->delSight($id);
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
            $model = new GisModel();
            $model->insertSight($objSight->id);
            
            $data = $this->modelSight->query(array('name' => $arrInfo['name']), 1, 1);
            $url  = "http://123.57.67.165:8301/InitData?sightId=".$data[0]['id']."&type=All&num=".Base_Config::getConfig('thirddata')->initnum;
            $http = Base_Network_Http::instance()->url($url);
            $http->timeout(2);
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
            if(in_array($key,$this->_fileds)){
                $key = $this->getprop($key);
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
            $model = new GisModel();
            $model->insertSight($sightId);
            
            $data = $this->modelSight->query(array('id' => $sightId), 1, 1);
            $url  = "http://123.57.67.165:8301/InitData?sightId=".$data[0]['id']."&type=All&num=".Base_Config::getConfig('thirddata')->initnum;
            $http = Base_Network_Http::instance()->url($url);
            $http->timeout(2);
            $http->exec();
        }
        if($ret && isset($arrInfo['status']) && ($arrInfo['status'] == Sight_Type_Status::NOTPUBLISHED)){
            $model = new GisModel();
            $model->delSight($sightId);        
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
            $model = new GisModel();
            $model->delSight($sightId);
        }
        $ret = $objSight->save();
        if($ret && $bDoPublish){
            $model = new GisModel();
            $model->insertSight($sightId);
            
            $data = $this->modelSight->query(array('id' => $sightId), 1, 1);
            $url  = "http://123.57.67.165:8301/InitData?sightId=".$data[0]['id']."&type=All&num=".Base_Config::getConfig('thirddata')->initnum;
            $http = Base_Network_Http::instance()->url($url);
            $http->timeout(2);
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
        if(!empty($sightId)){
            $listSightTopic->setFilter(array('sight_id' => $sightId));
        }
        $listSightTopic->setFields(array('topic_id'));
        $listSightTopic->setPagesize(PHP_INT_MAX);
        $arr = $listSightTopic->toArray();
        foreach ($arr['list'] as $topicId){
            $objTopic = new Topic_Object_Topic();
            $arrFilter = array_merge(array('id' => $topicId['topic_id']),$arrConf);
            $objTopic->fetch($arrFilter);
            if(!empty($objTopic->id)){
                $count += 1;
            }
        }
        
        $listSightTag = new Sight_List_Tag();
        $listSightTag->setFilter(array('sight_id' => $sightId));
        $listSightTag->setPagesize(PHP_INT_MAX);
        $arrSightTag  = $listSightTag->toArray(); 
        foreach ($arrSightTag['list'] as $val){
            $logicTopic = new Topic_Logic_Topic();
            $count     += $logicTopic->getTopicNumByTag($val['tag_id'], $sightId);
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
    
    /**
     * 根据景点ID判断是否保存到sight表里面
     * @param integer $sightId
     * @return array
     */
    public function isExistById($sightId){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' => $sightId));
        $arrSight =  $objSight->toArray();
         
        return $arrSight;
    }
    
}