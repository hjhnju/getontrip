<?php
class City_Logic_City{
    
    const HOTPERIOD = 30;
    
    const DEFAULT_SIZE = 4;
    
    const DEFAULT_CITY = 2;
    
    const CHINA_ID     = 10001;
    
    protected $_modeSight;
    
    public function __construct(){
        $this->_modeSight  = new SightModel();
        $this->_modelTopic = new TopicModel();
    }   
    
    /**
     * 根据城市ID获取其经纬度
     * @param integer $cityId
     * @return array
     */
    public function getCityLoc($cityId){
        $objCity = new City_Object_City();
        $objCity->fetch(array('id' => $cityId));
        if(!empty($objCity->id)){
            return array(
                'x' => $objCity->x,
                'y' => $objCity->y,
            );
        }
        return array();
    }
    
    /**
     * 根据城市ID获取城市信息，包含景点及话题信息，景点按话题热度排序
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCityDetail($cityId,$page,$pageSize){
        $logicTopic = new Topic_Logic_Topic();
        $logicSight = new Sight_Logic_Sight();
        $redis      = Base_Redis::getInstance();
        $ret        = City_Api::getCityById($cityId);
        $listSight  = new Sight_List_Sight();
        $listSight->setFields(array('id','name','image'));
        $listSight->setFilter(array('city_id' => $cityId,'status' => Sight_Type_Status::PUBLISHED));
        $listSight->setOrder('`hot2` desc');
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $arrSight     = $listSight->toArray();
        $arrSight     = $arrSight['list'];
        $logicCollect = new Collect_Logic_Collect();
        foreach ($arrSight as $key => $val){       
            $topic_num     = $logicSight->getTopicNum($val['id'],array('status' => Topic_Type_Status::PUBLISHED));
            $wiki_num      = Keyword_Api::getKeywordNum($val['id']);
            $book_num      = Book_Api::getBookNum($val['id']);
            $video_num     = Video_Api::getVideoNum($val['id']);
            $arrSight[$key]['id']        = strval($val['id']);
            $arrSight[$key]['image']     = Base_Image::getUrlByName($val['image']);
            $arrSight[$key]['topics']    = sprintf("%s个内容",$topic_num + $wiki_num + $book_num + $video_num);
            $arrSight[$key]['collected'] = strval($logicCollect->checkCollect(Collect_Type::SIGHT, $val['id']));
        }
        return $arrSight;
    }
    
    public function getCityLandscape($cityId,$x, $y, $page,$pageSize){
        $arrInfo    = array();
        $modelGis   = new GisModel();
        $logicSight = new Sight_Logic_Sight();
        $logicCollect = new Collect_Logic_Collect();
        $arrRet   = $modelGis->getCityNearSight($cityId, $x, $y, $page, $pageSize);
        foreach($arrRet as $key => $val){
            $sight         = Sight_Api::getSightById($val['id']);
            $topic_num     = $logicSight->getTopicNum($val['id'],array('status' => Topic_Type_Status::PUBLISHED));
            $wiki_num      = Keyword_Api::getKeywordNum($val['id']);
            $book_num      = Book_Api::getBookNum($val['id']);
            $video_num     = Video_Api::getVideoNum($val['id']);
            $arrRet[$key]['id']          = strval($val['id']);
            $arrRet[$key]['name']        = strval($sight['name']);
            $arrRet[$key]['x']           = strval($sight['x']);
            $arrRet[$key]['y']           = strval($sight['y']);
            $arrRet[$key]['image']       = Base_Image::getUrlByName($sight['image']);
            $arrRet[$key]['contentNum']  = strval($topic_num + $wiki_num + $book_num + $video_num);
            $arrRet[$key]['collectNum']  = strval($logicCollect->getTotalCollectNum(Collect_Type::SIGHT, $val['id']));
            if(!empty($x) && !empty($y)){
                if($val['dis'] < 1000){
                    $arrRet[$key]['dis']      = strval(ceil($val['dis']));
                    $arrRet[$key]['dis_unit'] = "m";
                }else{
                    $arrRet[$key]['dis']      = strval(ceil($val['dis']/1000));
                    $arrRet[$key]['dis_unit'] = "km";
                }
            }else{
                $arrRet[$key]['dis']          = '';
                $arrRet[$key]['dis_unit']     = '';
            }
            $arrRet[$key]['landscape_id']     = '';
            $arrRet[$key]['url']              = '';
            $arrRet[$key]['content']          = '';
            $arrRet[$key]['audio']            = '';
            $arrRet[$key]['audio_len']        = '';
            $objKeyword = new Keyword_Object_Keyword();
            $objKeyword->fetch(array('sight_id' => $val['id'],'level' => Keyword_Type_Level::SIGHT));
            //$objKeyword->fetch(array('sight_id' => $val['id'],'name' => $sight['name']));
            if(!empty($objKeyword->id)){
                $arrRet[$key]['url']          = trim($objKeyword->url);
                $arrRet[$key]['landscape_id'] = strval($objKeyword->id);
                $arrRet[$key]['content']      = strval($objKeyword->content);
                $arrRet[$key]['audio']        = empty($objKeyword->audio)?'':"/audio/".trim($objKeyword->audio);
                $arrRet[$key]['audio_len']    = strval($objKeyword->audioLen);
            }
        }
        $city                        = City_Api::getCityById($cityId);
        $arrInfo['id']               = strval($cityId);
        $arrInfo['name']             = strval($city['name']);
        $arrInfo['image']            = isset($city['image'])?Base_Image::getUrlByName($city['image']):'';
        $arrInfo['des']              = '';
        $arrInfo['audio']            = '';
        $arrInfo['audio_len']        = '';
        $arrInfo['url']              = '';
        $arrInfo['isfav']            = strval($logicCollect->checkCollect(Collect_Type::CITY, $cityId));
        $objKeyword   = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('sight_id' => $cityId,'level' => Keyword_Type_Level::CITY,'status' => Keyword_Type_Status::PUBLISHED));
        if(!empty($objKeyword->id)){
            $arrInfo['landscape_id'] = strval($objKeyword->id);
            $arrInfo['des']          = strval($objKeyword->content);
            $arrInfo['audio']        = empty($objKeyword->audio)?'':"/audio/".trim($objKeyword->audio);
            $arrInfo['audio_len']    = strval($objKeyword->audioLen);
            $arrInfo['url']          = strval($objKeyword->url);
        }
        $arrInfo['sight'] = $arrRet;
        return $arrInfo;
    }
    
    /**
     * 获取城市信息，供前端使用
     * @return array
     */
    public function getCityInfo($type){
        $arrRet        = array();
        $arrLeters     = range('A','Z');
        $objCity       = new City_Object_City();
        $logicSight    = new Sight_Logic_Sight();
        $modelCity     = new CityModel();
        foreach($arrLeters as $char){
            $arrCity  = $modelCity->queryPushCityByPinyin($char,$type);
            $tempCity = array();
            foreach ($arrCity as $key => $val){
                $objCity = new City_Object_City();
                $objCity->fetch(array('id' => $val['id']));
                $val['id']         = strval($val['id']);
                $val['name']       = strval($objCity->name);
                $sightNum          = $logicSight->getSightsNum(array('status' => Sight_Type_Status::PUBLISHED),$val['id']);
                $topic_num         = $modelCity->getCityTopicNum($val['id']);
                $wiki_num          = $modelCity->getCityWikiNum($val['id']);
                $video_num         = $modelCity->getCityVidoNum($val['id']);
                $book_num          = $modelCity->getCityBookNum($val['id']);
                $val['sight']      = strval($sightNum);
                $val['topic']      = strval($topic_num + $wiki_num + $video_num + $book_num);
                $tempCity[] = $val;
            }
            if(!empty($tempCity)){
                $arrRet[$char] = $tempCity;
            }
        }       
        return $arrRet;
    }
    
    /**
     * 根据城市ID获取城市信息
     * @param integer $cityId
     * @return array
     */
    public function getCityById($cityId){
        $objCity = new City_Object_Meta();
        $logicDestTags = new Destination_Logic_Tag();
        $objCity->fetch(array('id' => $cityId));
        $ret = $objCity->toArray();
        if(!empty($ret)){
            $objCity->fetch(array('id' => $ret['pid']));
            $ret['pidname'] = $objCity->name;

            $objCity->fetch(array('id' => $ret['continentid']));
            $ret['continentname'] = $objCity->name;

            $objCity->fetch(array('id' => $ret['countryid']));
            $ret['countryname'] = $objCity->name;

            $objCityInfo = new City_Object_City();
            $objCityInfo->fetch(array('id' => $ret['id']));
            $arrRet      = $objCityInfo->toArray();
            $ret         = array_merge($ret, $arrRet);
            $ret['tags'] = $logicDestTags->getDestinationTags($cityId, Destination_Type_Type::CITY, 1, PHP_INT_MAX);
        }
        return $ret;
    }
    
    /**
     * 修改城市信息
     * @param integer $cityId
     * @param array $arrInfo: array('pinyin' => 'xxx','x' => xxxx)
     * @return boolean
     */
    public function editCity($cityId,$arrInfo){
        $objCity = new City_Object_City();
        $objCity->fetch(array('id' => $cityId));
        $arrTag  = array();
        if(isset($arrInfo['tags'])){
            $arrTag = $arrInfo['tags'];
            unset($arrInfo['tags']);
        }
        if(empty($objCity->id)){
            return false;
        }
        foreach ($arrInfo as $key => $val){
            $objCity->$key = $val;
        }
        
        $listDestinationTag = new Destination_List_Tag();
        $listDestinationTag->setFilter(array('destination_id' => $cityId,'destination_type' => Destination_Type_Type::CITY));
        $listDestinationTag->setPagesize(PHP_INT_MAX);
        $arrDestinationTag = $listDestinationTag->toArray();
        foreach ($arrDestinationTag['list'] as $val){
            $objDestinationTag = new Destination_Object_Tag();
            $objDestinationTag->fetch(array('id' => $val['id']));
            $objDestinationTag->remove();
        }
        
        foreach ($arrTag as $id){
            $objDestinationTag = new Destination_Object_Tag();
            $objDestinationTag->destinationId   = $cityId;
            $objDestinationTag->destinationType = Destination_Type_Type::CITY;
            $objDestinationTag->tagId   = $id;
            $objDestinationTag->save();
        }
        return $objCity->save();
    }
    
    /**
     * 添加新的城市
     * @param array $arrInfo : array('name' => 'xxx','cityid' => 'xxx')
     * @return boolean
     */
    public function addCity($arrInfo){
        $objCity = new City_Object_City();
        foreach ($arrInfo as $key => $val){
            $objCity->$key = $val;
        }
        return $objCity->save();
    }
    
    /**
     * 查询城市
     * @param array $arrInfo:过滤条件，如:array("status"=>1);
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryCity($arrInfo,$page,$pageSize){ 
      $modelCity         = new CityModel();
      $logicDestTag      = new Destination_Logic_Tag();
        $arrRet          = array();
        $arrRet['total'] = $modelCity->getCityNum($arrInfo);
        $arrRet['pageall'] = ceil($arrRet['total'] / $pageSize);
        $arrRet['page']  = $page;
        $arrRet['pagesize'] = $pageSize;
        
        $arrRet['list']  = $modelCity->queryCity($arrInfo, $page, $pageSize); 
        foreach ($arrRet['list'] as $key => $val){
            $ret  = $this->getCityById($val['id']);
            $arrRet['list'][$key]['x']       = isset($ret['x'])?$ret['x']:'';
            $arrRet['list'][$key]['y']       = isset($ret['y'])?$ret['y']:'';
            $arrRet['list'][$key]['image']   = isset($ret['image'])?$ret['image']:'';
            $arrRet['list'][$key]['status']  = isset($ret['status'])?$ret['status']:'';
            $arrRet['list'][$key]['pidname'] = $ret['pidname'];
            $arrRet['list'][$key]['countryname'] = $ret['countryname'];
            $arrRet['list'][$key]['continentname'] = $ret['continentname'];
            $arrRet['list'][$key]['tags']    = $logicDestTag->getDestinationTags($val['id'], Destination_Type_Type::CITY);
        }
        return $arrRet;
    }
    
    /**
     * 获取省的信息列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getProvinceList($page,$pageSize){
        $listCity = new City_List_Meta();
        $listCity->setFilter(array('provinceid' => 0));
        $listCity->setPage($page);
        $listCity->setPagesize($pageSize);
        $arrCity = $listCity->toArray();
        return $arrCity;
    }
    
    /**
     * 城市名前缀模糊查询
     * @param string $str
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryCityPrefix($str,$page,$pageSize,$arrParms = array()){
        $model   = new CityModel();
        $arrRet  = array();
        $logicCollect = new Collect_Logic_Collect();
        
        $arrRet['total'] = $model->getCityNum($arrParms,$str);
        $arrRet['pageall'] = ceil($arrRet['total'] / $pageSize);
        $arrRet['page']  = $page;
        $arrRet['pagesize'] = $pageSize;
        $arrRet['list']  = $model->queryCityPrefix($str, $page, $pageSize, $arrParms);
   
        foreach ($arrRet['list'] as $key => $val){
            $city = City_Api::getCityById($val['pid']);
            $arrRet['list'][$key]['pidname'] = $city['name'];
            $arrRet['list'][$key]['sight_num'] = $this->_modeSight->getSightNum($val['id']);
            $arrRet['list'][$key]['topic_num'] = $this->getTopicNum($val['id']);
            $arrRet['list'][$key]['collect']   = $logicCollect->getTotalCollectNum(Collect_Type::CITY, $val['id']);
        }
        return $arrRet;
    }
    
    /**
     * 省份名前缀模糊查询
     * @param string $str
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryProvincePrefix($str,$page,$pageSize){
        $listCity = new City_List_Meta();
        $strFileter = "`provinceid` = 0 and `continentid` != 0 and `countryid` != 0 and  name like '".$str."%'";
        $listCity->setFilterString("$strFileter");
        $listCity->setPage($page);
        $listCity->setPagesize($pageSize);
        $arrCity = $listCity->toArray();
        foreach ($arrCity['list'] as $key => $val){
            $city = City_Api::getCityById($val['id']);
            $arrCity['list'][$key]['pidname'] = $city['name'];
        }
        return $arrCity;
    }

    /**
     * 国家名前缀模糊查询
     * @param string $str
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryCountryPrefix($str,$page,$pageSize){
        $listCity = new City_List_Meta();
        $strFileter = "`countryid` = 0 and `continentid` != 0 and  name like '".$str."%'";
        $listCity->setFilterString("$strFileter");
        $listCity->setPage($page);
        $listCity->setPagesize($pageSize);
        $arrCity = $listCity->toArray();
        foreach ($arrCity['list'] as $key => $val){
            $city = City_Api::getCityById($val['id']);
            $arrCity['list'][$key]['pidname'] = $city['name'];
        }
        return $arrCity;
    }
    
    /**
     * 获取一个城市的话题总数
     * @param integer $cityId
     * @return integer
     */
    public function getTopicNum($cityId){
        $count = 0;
        $redis = Base_Redis::getInstance();
        $ret   = $this->_modeSight->getSightByCity(1,PHP_INT_MAX,$cityId);
        foreach ($ret as $val){
            $logicTopic = new Topic_Logic_Topic();
            $count += $logicTopic->getTopicNumBySight($val['id'], Topic_Type_Status::PUBLISHED);
        }
        return $count;
    }
    
    /**
     * 获取热门城市信息
     * @return array
     */
    public function getHotCity($type){
        $arrRet        = array();
        $logicSight    = new Sight_Logic_Sight();
        $modelCity     = new CityModel();
        $arrHotCity    = City_Api::getHotCityIds();
        if($type == City_Type_Type::INLAND){
            $arrHotCity = isset($arrHotCity['inland'])?$arrHotCity['inland']:array();
        }else{
            $arrHotCity = isset($arrHotCity['outer'])?$arrHotCity['outer']:array();
        }    
        foreach ($arrHotCity as $key => $val){
            $sightNum          = $logicSight->getSightsNum(array('status' => Sight_Type_Status::PUBLISHED),$val);
            $topic_num         = $modelCity->getCityTopicNum($val);
            $wiki_num          = $modelCity->getCityWikiNum($val);
            $video_num         = $modelCity->getCityVidoNum($val);
            $book_num          = $modelCity->getCityBookNum($val);
            
            $objCity           = new City_Object_City();
            $objCity->fetch(array('id' => $val));
            $arrRet[$key]['id']          = strval($val);
            $arrRet[$key]['name']        = strval($objCity->name);
            $arrRet[$key]['image']       = Base_Image::getUrlByName($objCity->image);
            $arrRet[$key]['sight']       = strval($sightNum);
            $arrRet[$key]['topic']       = strval($topic_num + $wiki_num + $video_num + $book_num);
        }
        return array('hot' => $arrRet);
    }
    
    /**
     * 根据城市名称前缀获取城市ID
     * @param string $strName
     * @return integer
     */
    public function getCityIdByName($strName){
        $city       = $this->queryCityPrefix($strName, 1, 1);
        if(!empty($city['list'])){
            $cityId     = $city['list'][0]['id'];
        }
        return $cityId;
    }
    
    /**
     * 获取最热门的话题，带景点ID、时间范围、大小、标签过滤，并加上答案等信息
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getHotTopic($cityId,$page = 1,$pageSize = self::DEFAULT_SIZE,$tag= ''){
        $arrRet     = $this->_modelTopic->getHotTopicIdsByCity($cityId,$page,$pageSize,$tag);
        $logicTopic = new Topic_Logic_Topic();
        foreach($arrRet as $key => $val){
            $topicDetail = $this->_modelTopic->getTopicDetail($val['id'],$page);
            $arrRet[$key]['title']     = trim($topicDetail['title']);
            $arrRet[$key]['subtitle']  = trim($topicDetail['subtitle']);
            //$arrRet[$key]['desc']      = trim($topicDetail['desc']);
            //话题访问人数
            $arrRet[$key]['visit']     = strval($logicTopic->getTotalTopicVistUv($val['id']));
            
            //话题点赞数
            $logicPraise               = new Praise_Logic_Praise();
            $arrRet[$key]['praise']    = $logicPraise->getPraiseNum($val['id']);
    
            //话题收藏数
            //$logicCollect            = new Collect_Logic_Collect();
            //$arrRet[$key]['collect'] = strval($logicCollect->getTotalCollectNum(Collect_Type::TOPIC, $val['id']));
    
            //话题来源
            //$logicSource = new Source_Logic_Source();
            //$arrRet[$key]['from']    = $logicSource->getSourceName($topicDetail['from']);
    
            $arrRet[$key]['image']  = Base_Image::getUrlByName($topicDetail['image']);
            
            $arrRet[$key]['tagid']     = strval($tag);
            
            $arrRet[$key]['url']    = Base_Config::getConfig('web')->root.'/topic/detail/'.Base_Util_Secure::encryptForUuap(Base_Util_Secure::PASSWD_KEY,$topicDetail['id']);
            
            $logicSight             = new Sight_Logic_Sight();
            $sightIds = $logicSight->getSightByTopic($val['id']);
            if(isset($sightIds['list'][0])){
                $sightId  = $sightIds['list'][0]['sight_id'];
                $sight    = $logicSight->getSightById($sightId);
                $arrRet[$key]['sight']  = trim($sight['name']);
            }else{
                $arrRet[$key]['sight']  = "";
            }
            
            $logicTag  = new Tag_Logic_Tag();
            $arrTags   = $logicTag->getTopicTags($val['id']);
            foreach ($arrTags as $index => $data){
                $arrTags[$index] = str_replace("其他", "", $data);
            }
            $arrRet[$key]['tags'] = $arrTags;
        }
        return $arrRet;
    }
    
    /*
     * 城市的搜索接口
     */
    public function search($query, $page, $pageSize){
        $logicSight = new Sight_Logic_Sight();
        $modelCity  = new CityModel();
        $arrCity  = Base_Search::Search('city', $query, $page, $pageSize, array('id'));
        $num      = $arrCity['num'];
        $arrCity  = $arrCity['data'];
        foreach ($arrCity as $key => $val){
            $city = $this->getCityById($val['id']);
            $arrCity[$key]['name']  = empty($val['name'])?trim($city['name']):$val['name'];
            $arrCity[$key]['name']  = str_replace("市","",$arrCity[$key]['name']);
            
            $arrCity[$key]['title']  = $arrCity[$key]['name'];
            $arrCity[$key]['image'] = isset($city['image'])?Base_Image::getUrlByName($city['image']):'';
            
            $sight_num     = $logicSight->getSightsNum(array('status' => Sight_Type_Status::PUBLISHED),$val['id']);
            
            $topic_num     = $modelCity->getCityTopicNum($val['id']);
            $wiki_num      = $modelCity->getCityWikiNum($val['id']);
            $video_num     = $modelCity->getCityVidoNum($val['id']);
            $book_num      = $modelCity->getCityBookNum($val['id']);
            
            $arrCity[$key]['desc'] = sprintf("%d个景点，%d个内容",$sight_num,$topic_num + $wiki_num + $video_num + $book_num);
            $arrCity[$key]['content'] = $arrCity[$key]['desc'];
        }
        return  array('data' => $arrCity,'num' => $num);
    }
    
    /**
     * 根据城市名获取城市信息
     * @param string $strName
     * @return array
     */
    public function getCityFromName($strName){
        $ret = '';
        if (preg_match("/^[\x7f-\xff]+$/", $strName)) {
            $strName   = str_replace("市", "", $strName);
            $tmpCity   = $this->queryCityPrefix($strName, 1, 1);            
        }else{
            $strName   = str_replace("shi", "", strtolower($strName));
            $listCity  = new City_List_Meta();
            $filter    = "`pinyin` like '".$strName."%'";
            $listCity->setFilterString($filter);
            $listCity->setFields(array('id','name'));
            $tmpCity   = $listCity->toArray();
        }
        foreach ($tmpCity['list'] as $key => $val){
            $objCity = new City_Object_City();
            $objCity->fetch(array('id' => $val['id'],'status' => City_Type_Status::PUBLISHED));
            $id      = strval($objCity->id);
            if(!empty($id)){
                return $id;
            }            
        }
        return $ret;
    }
    
    public function provice(){
        $listProvince = new City_List_Meta();
        $listProvince->setFilter(array('provinceid' => 0));
        $listProvince->setFields(array('id','name'));
        $listProvince->setOrder('`id` asc');
        $listProvince->setPagesize(PHP_INT_MAX);
        $arrRet   =  $listProvince->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $listCity = new City_List_Meta();
            $listCity->setFilter(array('provinceid' => $val['id'],'cityid' => 0));
            $listCity->setFields(array('name'));
            $listCity->setPagesize(PHP_INT_MAX);
            $arrCity = $listCity->toArray();
            $arrRet['list'][$key]['city'] = $arrCity['list'];
            unset($arrRet['list'][$key]['id']);
        }
        return $arrRet['list'];
    }
    
    public function getCityNum($arrInfo){
        $listCity = new City_List_City();
        if(!empty($arrInfo)){
            $listCity->setFilter($arrInfo);
        }
        return $listCity->getTotal();
    }


    /**
     * 根据条件获取city_meta对象
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCityMeta($arrInfo){  
        $objMeta = new City_Object_Meta();
        $arrParam   = array();
        $arrParam = array_merge($arrParam,$arrInfo);  
        $objMeta->fetch($arrParam);
        $ret = $objMeta->toArray();
        if(!empty($ret)){
             
        }
        return $ret;
    }


    /**
     * 添加新的城市
     * @param array $arrInfo : array('name' => 'xxx','cityid' => 'xxx')
     * @return boolean
     */
    public function addCityMeta($arrInfo){
        $objCity = new City_Object_Meta();
        foreach ($arrInfo as $key => $val){
            $objCity->$key = $val;
        }
        $ret = $objCity->save();
        return $objCity->id;
    }
    
    public function getHotCityIds(){
        $arrRet  = array();
        $listHot = new Hot_List_Hot();
        $listHot->setFields(array('obj_id'));
        $listHot->setFilter(array('obj_type' => Hot_Type_Obj::CITY,'type' => Hot_Type_Hot::HOT));
        $listHot->setPagesize(PHP_INT_MAX);
        $arrHot  = $listHot->toArray();
        foreach($arrHot['list'] as $val){
            $objCity = new City_Object_City();
            $objCity->fetch(array('id' => $val['obj_id']));
            if($objCity->isChina == City_Type_Type::INLAND){
                $arrRet['inland'][] = $objCity->id;
            }else{
                $arrRet['outer'][]  = $objCity->id;
            }
        }
        return $arrRet;
    }
    
    public function setHotCity($id){
        $objHot = new Hot_Object_Hot();
        $objHot->fetch(array('obj_id' => $id, 'obj_type' => Hot_Type_Obj::CITY, 'type' => Hot_Type_Hot::HOT));
        if(empty($objHot->id)){
            $objHot->objId   = $id;
            $objHot->objType = Hot_Type_Obj::CITY;
            $objHot->type    = Hot_Type_Hot::HOT;
            return $objHot->save();
        }
        return true;
    }
    
    public function delHotCity($id){
        $objHot = new Hot_Object_Hot();
        $objHot->fetch(array('obj_id' => $id, 'obj_type' => Hot_Type_Obj::CITY, 'type' => Hot_Type_Hot::HOT));
        return $objHot->remove();
    }

    
    /*用于增加与国家同名城市*/
    public function countryList(){
        $list = new City_List_Meta();
        $objProvice = new City_Object_Meta();
        $objCity = new City_Object_Meta();
        $filter =  'countryid =0 and continentid!=0'; 
        $list->setFilterString($filter); 
        $list->setOrder('`id` asc');
        $list->setPagesize(PHP_INT_MAX);
        $arrRet   =  $list->toArray(); 

        $array = array(); 
        $city_id = 13000;
        //查询当前国家下的未知省份
        for ($i=0; $i < count($arrRet['list']); $i++) { 
            $item = $arrRet['list'][$i];
            if ($item['name']=='中国') {
                continue;
            }
            $objProvice->fetch(array('countryid' => $item['id'],'name'=>'未知省份')); 
            $provice = $objProvice->toArray();
            $item['provices'] = $provice;
           
            $objCity = new City_Object_Meta();
            //查询当前国家下的同名城市
            $objCity->fetch(array('countryid' => $item['id'],'provinceid'=>$provice['id'],'name'=>$item['name'])); 
            //$objCity->fetch(array('countryid' => 11540,'name'=>'加拿大')); 
            $city = $objCity->toArray();

            
            //return $city; 
            if (empty($city['id'])) {   
                //没有未知省份的增加一个未知省份 
                $city_id ++; 
                $city['id'] = $city_id;
                $city['name'] = $item['name'];
                $city['continentid'] = $item['continentid']; 
                $city['countryid'] = $item['id']; 
                $city['provinceid'] = $provice['id']; 
                $city['pid'] = $provice['id'];
                $city['pinyin'] = $item['pinyin']; 
                $this->addCityMeta($city);  
            } 
            else {
                $item['citys'] = $city; 
                array_push($array,$item);
             } 
            $arrRet['list'][$i] = $item;
        }
        return array($array,count($array));
        return $arrRet;
    }
}