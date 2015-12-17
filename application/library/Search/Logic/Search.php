<?php
/**
 * 搜索逻辑层
 * @author huwei
 *
 */
class Search_Logic_Search{
    
    const SEARCH_LABEL_NUM = 8;
    
    const HOT_TOPIC_NUM    = 30;
    
    protected $logicCity;
    
    protected $logicSight;
    
    protected $logicTopic;
    
    protected $logicComment;
    
    protected $logicPraise;
    
    protected $logicCollect;
    
    protected $logicBook;
    
    protected $logicVideo;
    
    protected $logicKeyword;
    
    protected $logicHotWord;
    
    
    public function __construct(){
        $this->logicCity    = new City_Logic_City();
        $this->logicSight   = new Sight_Logic_Sight();
        $this->logicTopic   = new Topic_Logic_Topic();
        $this->logicCollect = new Collect_Logic_Collect();
        $this->logicBook    = new Book_Logic_Book();
        $this->logicVideo   = new Video_Logic_Video();
        $this->logicKeyword = new Keyword_Logic_Keyword();
        $this->logicComment = new Comment_Logic_Comment();
        $this->logicHotWord = new Search_Logic_Word();
        $this->logicPraise  = new Praise_Logic_Praise();
    }
    
    /**
     * 搜索接口
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @param double $x
     * @param double $y
     * @return array
  */
    public function search($query, $page, $pageSize, $type){
        $arrRet = array();
        switch($type){
            case Search_Type_Search::CITY:
                //城市信息
                $arrRet  = $this->logicCity->search($query, $page, $pageSize);
                break;
            case Search_Type_Search::SIGHT:
                //景点信息
                $arrRet = $this->logicSight->search($query, $page, $pageSize);
                break;
            case Search_Type_Search::CONTENT:
            case Search_Type_Search::TOPIC:
                $arrRet    = $this->logicTopic->searchTopic($query, $page, $pageSize);
                break;
            case Search_Type_Search::BOOK:
                $arrRet     = $this->logicBook->search($query, $page, $pageSize);                
                break;
            case Search_Type_Search::VIDEO:
                $arrRet    = $this->logicVideo->search($query, $page, $pageSize);
                break;
            case Search_Type_Search::WIKI:
                $arrRet     = $this->logicKeyword->search($query, $page, $pageSize);
                break;
            default :
                $arrCity     = $this->logicCity->search($query, $page, $pageSize);
                $arrSight    = $this->logicSight->search($query, $page, $pageSize);
                $arrWiki     = $this->logicKeyword->search($query, $page, $pageSize);                
                $arrTopic    = $this->logicTopic->searchTopic($query, $page, $pageSize);
                $arrVideo    = $this->logicVideo->search($query, $page, $pageSize);
                $arrBook     = $this->logicBook->search($query, $page, $pageSize);               
                $arrRet = array(
                    'city'        => $arrCity['data'],
                    'city_num'    => $arrCity['num'],
                    'sight'       => $arrSight['data'],
                    'sight_num'   => $arrSight['num'],
                    'content'     => $arrTopic['data'],
                    'content_num' => $arrTopic['num'],
                    'landscape'   => $arrWiki['data'],
                    'landscape_num' => $arrWiki['num'],
                    'book'        => $arrBook['data'],
                    'book_num'    => $arrBook['num'],
                    'video'       => $arrVideo['data'],
                    'video_num'   => $arrVideo['num'],
                );
                break;
        }
        //记一条业务日志
        $arrLog = array(
            'type'  => 'search',
            'uid'   => User_Api::getCurrentUser(),
            'query' => $query,
        );
        Base_Log::NOTICE($arrLog);
        
        $this->logicHotWord->addSearchWord($query);
        if(!empty($type)){
            return $arrRet['data'];
        }
        return $arrRet;
    }
    
    /**
     * 获取搜索标签类型
     */
    public function label($labelId, $page, $pageSize){
        $arrRet    = array();
        $arrData   = array(); 
        $oss       = Oss_Adapter::getInstance();
        $arrRet['image']     = Base_Image::getUrlByName(Base_Config::getConfig('searchlabel')->image);
        $sign                = $oss->getMetaLen(Base_Config::getConfig('searchlabel')->image);
        $arrRet['image']    .= sprintf("?%s",md5($sign));
        if($page == 1){
            $listTag = new Tag_List_Tag();
            $listTag->setFilter(array('type' => Tag_Type_Tag::SEARCH));
            $listTag->setOrder('`weight` asc');
            $listTag->setPagesize(self::SEARCH_LABEL_NUM);
            $arrTag           = $listTag->toArray();
            $logicSearchLabel = new Search_Logic_Label();
            $arrTemp          = array();
            foreach ($arrTag['list'] as $key => $val){
                $arrTemp[$key]['id']    = trim($val['id']);
                $arrTemp[$key]['order'] = trim($val['weight']);
                $arrTemp[$key]['name']  = trim($val['name']);
                if($arrTemp[$key]['name'] == '热门内容'){
                    $arrTemp[$key]['num']  = strval(self::HOT_TOPIC_NUM);
                }else{
                    $arrTemp[$key]['num']  = $logicSearchLabel->getLabeledNum($val['id']);
                }               
            }
            $arrRet['label'] = $arrTemp;
            if(empty($labelId)){
                $labelId     = 1;
            }
        }
        $objLabel = new Tag_Object_Tag();
        $objLabel->fetch(array('type' => Tag_Type_Tag::SEARCH,'weight' => $labelId));
        $label    = Tag_Api::getTagInfo($objLabel->id);  
        $labelId  = $objLabel->id;
        if(isset($label['name']) && ($label['name'] == '热门内容')){
            if($page == 1){
                $modelTopic      = new TopicModel();
                $arrData['list'] = $modelTopic->getRandTopicIds($pageSize);
            }elseif($page <= 15){
                $listTopic = new Topic_List_Topic();
                $listTopic->setPage($page-1);
                $listTopic->setFilter(array('status' => Topic_Type_Status::PUBLISHED));
                $listTopic->setPagesize($pageSize);
                $listTopic->setOrder('`hot2` desc, `create_time` desc');
                $arrData = $listTopic->toArray();
            }else{
                $arrData['list'] = array();
            }
            if(empty($arrData['list'])){
                return $arrData['list'];
            }
            foreach ($arrData['list'] as $key => $val){
                $topicId       = $val['id'];
                $arrTopic      = $this->logicTopic->getTopicById($topicId);
                $temp['id']    = strval($topicId);
                $temp['type']  = strval(Search_Type_Label::TOPIC);
                $temp['name'] = $arrTopic['title'];
                $temp['image'] = isset($arrTopic['image'])?Base_Image::getUrlByName($arrTopic['image']):'';
                $logicTag      = new Tag_Logic_Tag();
                $tags          = $logicTag->getTopicTags($topicId);
                $sight         = '';
                $arrSight = $this->logicSight->getSightByTopic($topicId);
                if(!empty($arrSight['list'])){
                    $sightId   = $arrSight['list'][0]['sight_id'];
                    $arrSight  = Sight_Api::getSightById($sightId);
                    $sight     = $arrSight['name'];
                }
                if(isset($tags[0])){
                    $tags[0] = str_replace("其他", "", $tags[0]);
                }
                if(empty($sight)){
                    $temp['param1'] = isset($tags[0])?trim($tags[0]):'';
                }elseif(isset($tags[0])){       
                    $temp['param1'] = $sight.'·'.trim($tags[0]);
                }else{
                    $temp['param1'] = $sight;
                }
        
                $visit_num       = $this->logicTopic->getTotalTopicVistUv($topicId);
                //$collect         = $this->logicCollect->getTotalCollectNum(Collect_Type::TOPIC, $topicId);
                
                $praiseNum       = $this->logicPraise->getPraiseNum($topicId);
                
                $temp['param2']  =  strval($praiseNum);
                $temp['param3']  =  strval($visit_num);
                $ret[] = $temp;
            }
            $arrRet['content']   = $ret;
            return $arrRet;
        }
        
        $listLabel = new Search_List_Label();
        $listLabel->setFilter(array('label_id' => $labelId));
        $listLabel->setPage($page);
        $listLabel->setPagesize($pageSize);
        $arrLabel = $listLabel->toArray();
        if(!empty($arrLabel['list'])){
            $type  = intval($arrLabel['list'][0]['type']);
            if($type == Search_Type_Label::CITY){
                $logicCity = new City_Logic_City();
                foreach ($arrLabel['list'] as $key => $val){
                    $cityId        = $val['obj_id'];
                    $arrCity       = $logicCity->getCityById($cityId);
                    $temp['id']    = strval($cityId);
                    $temp['type']  =  strval(Search_Type_Label::CITY);
                    $temp['name']  = $arrCity['name'];
                    $temp['name']  = str_replace("市", "", $temp['name']);
                    $temp['image'] = isset($arrCity['image'])?Base_Image::getUrlByName($arrCity['image']):'';
                    $sight_num     = $this->logicSight->getSightsNum(array('status' => Sight_Type_Status::PUBLISHED),$cityId);
                    
                    $modelTopic    = new TopicModel();
                    $topic_num     = $modelTopic->getCityTopicNum($cityId);
                    $collect       = $this->logicCollect->getTotalCollectNum(Collect_Type::CITY, $cityId);
                    $temp['param1']  =  sprintf("%d个景点",$sight_num);
                    $temp['param2']  =  sprintf("%d个内容",$topic_num);
                    $temp['param3']  =  sprintf("%d人收藏",$collect);                                     
                    $arrData[] = $temp;
                }
            }else{
                $logicSight    = new Sight_Logic_Sight();
                foreach ($arrLabel['list'] as $key => $val){
                    $sightId       = $val['obj_id'];
                    $arrSight      = $logicSight->getSightById($sightId);
                    $temp['id']    = strval($sightId);
                    $temp['type']  = strval(Search_Type_Label::SIGHT);
                    $temp['name']  = $arrSight['name'];
                    $temp['image'] = isset($arrSight['image'])?Base_Image::getUrlByName($arrSight['image']):'';
                    $strTopicIds   = $this->logicTopic->getTopicIdBySight($sightId);
                    $arrTopicIds   = explode(",",$strTopicIds);
                    $count         = 0;
                    foreach ($arrTopicIds as $id){
                        if(empty($id)){
                            continue;
                        }
                        $count    += $this->logicComment->getTotalCommentNum($id);
                    }
                    $topic_num     = $this->logicSight->getTopicNum($sightId,array('status' => Topic_Type_Status::PUBLISHED));
                    $collect       = $this->logicCollect->getTotalCollectNum(Collect_Type::SIGHT, $sightId);
                    $temp['param1']  =  sprintf("%d个内容",$topic_num);
                    $temp['param2']  =  sprintf("%d条评论",$count);
                    $temp['param3']  =  sprintf("%d人收藏",$collect);                                        
                    $arrData[] = $temp;
                }
            }
        }
        $arrRet['content']   = $arrData;         
        return $arrRet;
    }
}