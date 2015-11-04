<?php
/**
 * 搜索逻辑层
 * @author huwei
 *
 */
class Search_Logic_Search{
    
    const SEARCH_LABEL_NUM = 8;
    
    protected $logicCity;
    
    protected $logicSight;
    
    protected $logicTopic;
    
    protected $logicComment;
    
    protected $logicCollect;
    
    protected $logicHotWord;
    
    
    public function __construct(){
        $this->logicCity    = new City_Logic_City();
        $this->logicSight   = new Sight_Logic_Sight();
        $this->logicTopic   = new Topic_Logic_Topic();
        $this->logicCollect = new Collect_Logic_Collect();
        $this->logicComment = new Comment_Logic_Comment();
        $this->logicHotWord = new Search_Logic_Word();
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
                //内容信息
                $arrRet = Base_Search::Search('content', $query, $page, $pageSize, array('id','unique_id','search_type','title','content'));
                foreach ($arrRet['data'] as $key => $val){
                    if($val['search_type'] == 'topic'){
                        $topic = Topic_Api::getTopicById($val['id']);
                        $arrRet['data'][$key]['image'] = isset($topic['image'])?Base_Image::getUrlByName($topic['image']):'';
                    }elseif($val['search_type'] == 'book'){                       
                        $book = Book_Api::getBookInfo($val['id']);
                        $arrRet['data'][$key]['image'] = isset($book['image'])?Base_Image::getUrlByName($book['image']):'';
                    }elseif($val['search_type'] == 'video'){
                        $video = Video_Api::getVideoInfo($val['id']);
                        $arrRet['data'][$key]['url']   = isset($video['url'])?$video['url']:'';
                        $arrRet['data'][$key]['image'] = isset($video['image'])?Base_Image::getUrlByName($video['image']):'';
                    }else{
                        $keyword = Keyword_Api::queryById($val['id']);
                        $arrRet['data'][$key]['url'] = isset($keyword['url'])?$keyword['url']:'';
                        $arrRet['data'][$key]['image'] = isset($keyword['image'])?Base_Image::getUrlByName($keyword['image']):'';
                    }
                }
                break;
            default :
                $arrCity     = $this->logicCity->search($query, $page, $pageSize);
                $arrSight    = $this->logicSight->search($query, $page, $pageSize);
                $arrContent = Base_Search::Search('content', $query, $page, $pageSize, array('id','unique_id','search_type','title','content'));
                foreach ($arrContent['data'] as $key => $val){
                    if($val['search_type'] == 'topic'){
                        $topic = Topic_Api::getTopicById($val['id']);
                        $arrContent['data'][$key]['image'] = isset($topic['image'])?Base_Image::getUrlByName($topic['image']):'';
                    }elseif($val['search_type'] == 'book'){                       
                        $book = Book_Api::getBookInfo($val['id']);
                        $arrContent['data'][$key]['image'] = isset($book['image'])?Base_Image::getUrlByName($book['image']):'';
                    }elseif($val['search_type'] == 'video'){
                        $video = Video_Api::getVideoInfo($val['id']);
                        $arrContent['data'][$key]['url']   = isset($video['url'])?$video['url']:'';
                        $arrContent['data'][$key]['image'] = isset($video['image'])?Base_Image::getUrlByName($video['image']):'';
                    }else{
                        $keyword = Keyword_Api::queryById($val['id']);
                        $arrContent['data'][$key]['url']   = isset($keyword['url'])?$keyword['url']:'';
                        $arrContent['data'][$key]['image'] = isset($keyword['image'])?Base_Image::getUrlByName($keyword['image']):'';
                    }
                    $arrContent['data'][$key]['title']   = Base_Util_String::trimall(Base_Util_String::getHtmlEntity($val['title']));
                    $arrContent['data'][$key]['content'] = Base_Util_String::trimall(Base_Util_String::getHtmlEntity($val['content']));
                }
                $arrRet = array(
                    'city'        => $arrCity['data'],
                    'city_num'    => $arrCity['num'],
                    'sight'       => $arrSight['data'],
                    'sight_num'   => $arrSight['num'],
                    'content'     => $arrContent['data'],
                    'content_num' => $arrContent['num'],
                );
                break;
        }
        $this->logicHotWord->addSearchWord($query);
        return $arrRet;
    }
    
    /**
     * 获取搜索标签类型
     */
    public function label($labelId, $page, $pageSize){
        $arrRet    = array();
        $arrData   = array();        
        $arrRet['image']     = Base_Image::getUrlByName(Base_Config::getConfig('searchlabel')->image);
        if($page == 1){
            $listTag = new Tag_List_Tag();
            $listTag->setFilter(array('type' => Tag_Type_Tag::SEARCH));
            $listTag->setOrder('`weight` asc');
            $listTag->setPagesize(self::SEARCH_LABEL_NUM);
            $arrTag           = $listTag->toArray();
            $logicSearchLabel = new Search_Logic_Label();
            $arrTemp          = array();
            foreach ($arrTag['list'] as $key => $val){
                $arrTemp[$key]['id']   = trim($val['id']);
                $arrTemp[$key]['name'] = trim($val['name']);
                $arrTemp[$key]['num']  = $logicSearchLabel->getLabeledNum($val['id']);
            }
            $arrRet['label'] = $arrTemp;
            if(empty($labelId)){
                $labelId     = $arrTemp[0]['id'];
            }
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
                    $topic_num     = $this->logicCity->getTopicNum($cityId);
                    $collect       = $this->logicCollect->getTotalCollectNum(Collect_Type::CITY, $cityId);
                    $temp['param1']  =  sprintf("%d个景点",$sight_num);
                    $temp['param2']  =  sprintf("%d个话题",$topic_num);
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
                        $count    += $this->logicComment->getTotalCommentNum($sightId);
                    }
                    $topic_num     = $this->logicSight->getTopicNum($sightId,array('status' => Topic_Type_Status::PUBLISHED));
                    $collect       = $this->logicCollect->getTotalCollectNum(Collect_Type::SIGHT, $sightId);
                    $temp['param1']  =  sprintf("%d个话题",$topic_num);
                    $temp['param2']  =  sprintf("%d个评论",$count);
                    $temp['param3']  =  sprintf("%d人收藏",$collect);                                        
                    $arrData[] = $temp;
                }
            }
        }
        $arrRet['content']   = $arrData;         
        return $arrRet;
    }
}