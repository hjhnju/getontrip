<?php
class City_Logic_City{
    
    const HOTPERIOD = 30;
    
    const DEFAULT_SIZE = 4;
    
    const DEFAULT_CITY = 2;
    
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
     * @param integer $cityId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCityDetail($cityId,$deviceId,$page,$pageSize){
        $arrHot     = array();
        $logicTopic = new Topic_Logic_Topic();
        $redis      = Base_Redis::getInstance();
        $ret        = City_Api::getCityById($cityId);
        $arrSight   = $this->_modeSight->getSightByCity($page, $pageSize, $cityId);
        $logicCollect = new Collect_Logic_Collect();
        foreach ($arrSight as $key => $val){
            $ret    = $redis->sMembers(Sight_Keys::getSightTopicKey($val['id']));
            $hot    = 0;
            foreach ($ret as $topicId){
                $hot += $logicTopic->getTopicHotDegree($topicId, self::HOTPERIOD);
            }
            $arrHot[] = $hot;     
            $arrSight[$key]['image']  = Base_Image::getUrlByName($val['image']);
            $arrSight[$key]['topics'] = sprintf("共%s个话题",count($redis->sMembers(Sight_Keys::getSightTopicKey($val['id']))));
            $arrSight[$key]['collect']= strval($logicCollect->checkCollect(Collect_Type::SIGHT, $deviceId, $val['id']));
        }
        array_multisort($arrHot, SORT_DESC , $arrSight);
        return $arrSight;
    }
    
    /**
     * 获取城市信息
     * @return array
     */
    public function getCityInfo(){
        $arrRet        = array();
        $arrRet['hot'] = $this->getHotCity();
        $arrLeters     = range('A','Z');
        foreach($arrLeters as $char){
            $strFilter = "`cityid` = 0 and `provinceid` != 0";
            $listCity = new City_List_Meta();
            $strFilter .=" and `pinyin` like '".strtolower($char)."%'";
            $listCity->setFilterString($strFilter);
            $listCity->setFields(array('name','pinyin'));
            $listCity->setPageSize(PHP_INT_MAX);
            $arrCity = $listCity->toArray();
            foreach ($arrCity['list'] as $key => $val){
                $arrCity['list'][$key]['pinYinHead'] = strtolower(Base_Util_String::pinyin_first($val['name']));
            }
            $arrRet[$char] = $arrCity['list'];
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
        $objCity->fetch(array('id' => $cityId));
        $ret = $objCity->toArray();
        if(!empty($ret)){
            $objCity->fetch(array('id' => $ret['pid']));
            $ret['pidname'] = $objCity->name;
            
            $objCityInfo = new City_Object_City();
            $objCityInfo->fetch(array('id' => $ret['id']));
            $arrRet      = $objCityInfo->toArray();
            $ret         = array_merge($ret, $arrRet);
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
        if(empty($objCity->id)){
            return false;
        }
        foreach ($arrInfo as $key => $val){
            $objCity->$key = $val;
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
      $modelCity       = new CityModel();
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
        $strFileter = "`provinceid` = 0 and name like '".$str."%'";
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
            $count += $redis->sSize(Sight_Keys::getSightTopicKey($val['id']));
        }
        return $count;
    }
    
    /**
     * 获取热门城市信息
     * @return array
     */
    public function getHotCity(){
        $arrHotCity = array(
            array('id' =>2,   'name'=>'北京','pinyin' => 'beijing', 'pinYinHead' => 'bj'),
            array('id' =>41,  'name'=>'上海','pinyin' => 'shanghai', 'pinYinHead' => 'sh'),
            array('id' =>2185,'name'=>'广州','pinyin' => 'guangzhou', 'pinYinHead' => 'gz'),
            array('id' =>2211,'name'=>'深圳','pinyin' => 'shenzhen', 'pinYinHead' => 'sz'),
            array('id' =>925, 'name'=>'南京','pinyin' => 'nanjing', 'pinYinHead' => 'nj'),
            array('id' =>1058,'name'=>'杭州','pinyin' => 'hangzhou', 'pinYinHead' => 'hz'),
            array('id' =>972, 'name'=>'苏州','pinyin' => 'suzhou', 'pinYinHead' => 'sz'),
        );
        return $arrHotCity;
    }
    
    /**
     * 根据城市名称前缀获取城市ID
     * @param string $strName
     * @return integer
     */
    public function getCityIdByName($strName){
        $cityId     = 2;
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
    public function getHotTopic($cityId,$page = 1,$pageSize = self::DEFAULT_SIZE){
        $arrRet     = $this->_modelTopic->getHotTopicIdsByCity($cityId,$page,$pageSize);
        $logicTopic = new Topic_Logic_Topic();
        foreach($arrRet as $key => $val){
            $topicDetail = $this->_modelTopic->getTopicDetail($val['id'],$page);
            $arrRet[$key]['title']     = trim($topicDetail['title']);
            $arrRet[$key]['subtitle']  = trim($topicDetail['subtitle']);
            $arrRet[$key]['desc']      = trim($topicDetail['desc']);
            //话题访问人数
            $arrRet[$key]['visit']     = strval($logicTopic->getTotalTopicVistUv($val['id']));
    
            //话题收藏数
            $logicCollect            = new Collect_Logic_Collect();
            $arrRet[$key]['collect'] = strval($logicCollect->getTotalCollectNum(Collect_Type::TOPIC, $val['id']));
    
            //话题来源
            $logicSource = new Source_Logic_Source();
            $arrRet[$key]['from']    = $logicSource->getSourceName($topicDetail['from']);
    
            $arrRet[$key]['image']  = Base_Image::getUrlByName($topicDetail['image']);
            
            $logicSight             = new Sight_Logic_Sight();
            $sightIds = $logicSight->getSightByTopic($val['id']);
            $sightId  = $sightIds['list'][0]['sight_id'];
            $sight    = $logicSight->getSightById($sightId);
            $arrRet[$key]['sight']  = trim($sight['name']);
            
            $logicTag  = new Tag_Logic_Tag();
            $arrTags   = $logicTag->getTopicTags($val['id']);
            $arrRet[$key]['tag']    = isset($arrTags[0])?trim($arrTags[0]):'';
        }
        return $arrRet;
    }
    
    /*
     * 城市的搜索接口
     */
    public function search($query, $page, $pageSize){
        $logicSight = new Sight_Logic_Sight();
        $arrCity  = Base_Search::Search('city', $query, $page, $pageSize, array('id'));
        foreach ($arrCity as $key => $val){
            $city = $this->getCityById($val['id']);
            $arrCity[$key]['name']  = trim($city['name']);
            $arrCity[$key]['image'] = isset($city['image'])?Base_Image::getUrlByName($city['image']):'';
            
            $sight_num     = $logicSight->getSightsNum(array(),$val['id']);
            $topic_num     = $this->getTopicNum($val['id']);
            $arrCity[$key]['desc'] = sprintf("景点数:%d，话题数:%d",$sight_num,$topic_num);
        }
        return  $arrCity;
    }
    
    /**
     * 根据城市名获取城市信息
     * @param string $strName
     * @return array
     */
    public function getCityFromName($strName){
        if (preg_match("/^[\x7f-\xff]+$/", $strName)) {
            $strName   = str_replace("市", "", $strName);
            $tmpCity   = $this->queryCityPrefix($strName, 1, 1);            
        }else{
            $strName   = str_replace("shi", "", strtolower($strName));
            $listCity  = new City_List_Meta();
            $filter    = "`pinyin` like '".$strName."%'";
            $listCity->setFilterString($filter);
            $tmpCity   = $listCity->toArray();
            foreach ($tmpCity['list'] as $key => $val){
                $objCity = new City_Object_City();
                $objCity->fetch(array('id' => $val['id']));
                $tmpCity['list'][$key]['image'] = $objCity->image;
            }
        }
        if(!isset($tmpCity['list'][0]['id'])){
            $tmpCity   = City_Api::queryCityPrefix('北京', 1, 1);
        }
        return $tmpCity;
    }
}