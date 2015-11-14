<?php
/**
 * 首页接口数据逻辑层
 * @author huwei
 *
 */
class Home_Logic_List{
    
    protected $_logicTopic;
    
    protected $_logicSight;
    
    protected $_logicCollect;
    
    const REDIS_TIMEOUT = 3600;
    
    const ORDER_HOT = 1;
    
    const ORDER_NEW = 2;
    
    const PAGE_SIZE = 4;
    
    const DEFAULT_CITY_ID = 2; //默认城市北京
    
    public function __construct(){       
        $this->_logicTopic   = new Topic_Logic_Topic();
        $this->_logicSight   = new Sight_Logic_Sight();
        $this->_logicCollect = new Collect_Logic_Collect();
    }
    
    /**
     * 根据给定点，找附近的景点并拼装话题信息
     * @param double $x
     * @param double $y
     * @param integer $page
     * @param integer $pageSize
     * @return array $arr
     */
    public function getNearSight($x,$y,$page,$pageSize,$deviceId = ''){
        $arr   = array();
        $redis = Base_Redis::getInstance();
        $model = new TopicModel();
        $modelGis  = new GisModel();
        Yaf_Session::getInstance()->set(Home_Keys::SESSION_USER_X_NAME,$x);
        Yaf_Session::getInstance()->set(Home_Keys::SESSION_USER_Y_NAME,$y);
        //找出所有由近到远的景点
        $arr = $modelGis->getNearSight(array(
            'x'=>$x,
            'y'=>$y,            
        ),$page,$pageSize);
        //通过这些景点，取出其它的如城市、话题、答案等信息
        foreach ($arr as $index => $val){
            $objCity = new City_Object_City();
            $objCity->fetch(array('id' => $val['city_id']));
            $arr[$index]['city']  = $objCity->name;       

            //是否收藏过
            $logicCollect = new Collect_Logic_Collect();
            if(!empty($deviceId)){
                $arr[$index]['collected'] = strval($logicCollect->checkCollect(Collect_Type::SIGHT,$val['id']));
            }else{
                $arr[$index]['collected'] = '';
            }
            
            $arr[$index]['topic'] = $this->_logicTopic->getHotTopic($val['id']);                                
            //图片用全路径               
            $arr[$index]['image']  = Base_Image::getUrlByName($val['image']);
                     
            //距离转换成字符串
            $arr[$index]['dis'] = Base_Util_Number::getDis($val['dis']);             
        }        
        return $arr; 
    }
    
    /**
     * 根据给定位置及过滤条件，获取周边景点信息
     * @param integer $page
     * @param integer $pageSize
     * @param integer $order
     * @param integer $sight
     * @param string $strTags
     */
    public function getFilterSight($page,$pageSize,$order,$sight,$strTags){
        $arr   = array();
        $arr   = $this->_logicSight->getSightDetail($sight,$page,$pageSize,$order,$strTags);           
        return $arr;
    }
    
    /**
     * 城市中间页入口一
     * @param string $city
     */
    public function getHomeData($cityId){
        //城市信息
        $tmpCity      = City_Api::getCityById($cityId);
        $cityTopicNum = 0;
        $collected = $this->_logicCollect->checkCollect(Collect_Type::CITY, $cityId);
        $arrCity = array(
            'id'        => isset($tmpCity['id'])?strval($tmpCity['id']):'',
            'name'      => isset($tmpCity['name'])?trim($tmpCity['name']):'',
            'image'     => isset($tmpCity['image'])?Base_Image::getUrlByName($tmpCity['image']):'', 
            'collected' => strval($collected),         
        );
        $arrCity['name']  = str_replace("市","",$arrCity['name']);
        //景点信息
        $arrSight = array();
        $sight    = Sight_Api::getSightByCity($cityId, 1, PHP_INT_MAX);
        foreach ($sight['list'] as $key => $val){
            $temp = array();
            if($val['status'] == Sight_Type_Status::NOTPUBLISHED){
               continue;
            }            
            $temp['id']    = strval($val['id']);
            $temp['name']  = trim($val['name']);
            $temp['image'] = isset($val['image'])?Base_Image::getUrlByName($val['image']):'';
                      
            $topic_num     = $this->_logicSight->getTopicNum($val['id'],array('status' => Topic_Type_Status::PUBLISHED));
            $cityTopicNum += $topic_num;
            $collect       = $this->_logicCollect->getTotalCollectNum(Collect_Type::SIGHT, $val['id']);
            $temp['content']  = sprintf("%d个内容",$topic_num);
            $temp['collect']  = sprintf("%d人收藏",$collect);
            
            $arrSight[] = $temp;
        }
        
        //话题信息
        $cityTopic = new City_Logic_City();
        $arrTopic  = $cityTopic->getHotTopic($cityId);
        
        //获取城市话题页数
        $pageNum   = ceil($cityTopicNum/self::PAGE_SIZE);
        
        $arrRet = array(
            'city'  => $arrCity,
            'sight' => $arrSight,
            'topic' => $arrTopic,
            'page_num' => strval($pageNum),
        );
        return $arrRet;
    }
}