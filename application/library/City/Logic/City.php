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
            $arrSight[$key]['id']        = strval($val['id']);
            $arrSight[$key]['image']     = Base_Image::getUrlByName($val['image']);
            $arrSight[$key]['topics']    = sprintf("%s个内容",$topic_num);
            $arrSight[$key]['collected'] = strval($logicCollect->checkCollect(Collect_Type::SIGHT, $val['id']));
        }
        return $arrSight;
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
        $modelTopic    = new TopicModel();
        foreach($arrLeters as $char){
            $strFilter  = "`cityid` = 0 and `provinceid` != 0";
            $listCity   = new City_List_Meta();
            $strFilter .=" and `pinyin` like '".strtolower($char)."%'";
            $listCity->setFilterString($strFilter);
            $listCity->setFields(array('id','name'));
            $listCity->setPageSize(PHP_INT_MAX);
            $arrCity = $listCity->toArray();
            $tempCity = array();
            foreach ($arrCity['list'] as $key => $val){
                $objCity = new City_Object_City();
                $objCity->fetch(array('id' => $val['id']));
                if($objCity->status == City_Type_Status::PUBLISHED){
                    if($type == City_Type_Type::INLAND){
                        if($objCity->isChina !== City_Type_Type::INLAND){
                            continue;
                        }
                    }else{
                        if($objCity->isChina == City_Type_Type::INLAND){
                            continue;
                        }
                    }
                    $val['id']         = strval($val['id']);
                    $val['name']       = strval($val['name']);
                    $sightNum          = $logicSight->getSightsNum(array('status' => Sight_Type_Status::PUBLISHED),$val['id']);
                    $topicNum          = $modelTopic->getCityTopicNum($val['id']);
                    $val['sight']       = strval($sightNum);
                    $val['topic']       = strval($topicNum);
                    $tempCity[] = $val;
                }
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
            $arrRet['list'][$key]['countryname'] = $ret['countryname'];
            $arrRet['list'][$key]['continentname'] = $ret['continentname'];
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
        $modelTopic    = new TopicModel();
        $arrHotCity    = City_Api::getHotCityIds();
        if($type == City_Type_Type::INLAND){
            $arrHotCity = isset($arrHotCity['inland'])?$arrHotCity['inland']:array();
        }else{
            $arrHotCity = isset($arrHotCity['outer'])?$arrHotCity['outer']:array();
        }    
        foreach ($arrHotCity as $key => $val){
            $sightNum          = $logicSight->getSightsNum(array('status' => Sight_Type_Status::PUBLISHED),$val);
            $topicNum          = $modelTopic->getCityTopicNum($val);
            
            $objCity           = new City_Object_City();
            $objCity->fetch(array('id' => $val));
            $arrRet[$key]['id']          = strval($val);
            $arrRet[$key]['name']        = strval($objCity->name);
            $arrRet[$key]['image']       = Base_Image::getUrlByName($objCity->image);
            $arrRet[$key]['sight']       = strval($sightNum);
            $arrRet[$key]['topic']       = strval($topicNum);
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
    public function getHotTopic($cityId,$page = 1,$pageSize = self::DEFAULT_SIZE){
        $arrRet     = $this->_modelTopic->getHotTopicIdsByCity($cityId,$page,$pageSize);
        $logicTopic = new Topic_Logic_Topic();
        foreach($arrRet as $key => $val){
            $topicDetail = $this->_modelTopic->getTopicDetail($val['id'],$page);
            $arrRet[$key]['title']     = trim($topicDetail['title']);
            $arrRet[$key]['subtitle']  = trim($topicDetail['subtitle']);
            //$arrRet[$key]['desc']      = trim($topicDetail['desc']);
            //话题访问人数
            $arrRet[$key]['visit']     = strval($logicTopic->getTotalTopicVistUv($val['id']));
    
            //话题收藏数
            //$logicCollect            = new Collect_Logic_Collect();
            //$arrRet[$key]['collect'] = strval($logicCollect->getTotalCollectNum(Collect_Type::TOPIC, $val['id']));
    
            //话题来源
            //$logicSource = new Source_Logic_Source();
            //$arrRet[$key]['from']    = $logicSource->getSourceName($topicDetail['from']);
    
            $arrRet[$key]['image']  = Base_Image::getUrlByName($topicDetail['image']);
            
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
        $modelTopic  = new TopicModel();
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
            $topic_num     = $modelTopic->getCityTopicNum($val['id']);
            $arrCity[$key]['desc'] = sprintf("%d个景点，%d篇内容",$sight_num,$topic_num);
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
        $objHot->objId   = $id;
        $objHot->objType = Hot_Type_Obj::CITY;
        $objHot->type    = Hot_Type_Hot::HOT;
        return $objHot->save();
    }
}