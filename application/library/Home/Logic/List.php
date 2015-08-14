<?php
/**
 * 首页接口数据逻辑层
 * @author huwei
 *
 */
class Home_Logic_List{
    
    protected $_model;
    
    protected $_logicTopic;
    
    protected $_logicSight;
    
    const REDIS_TIMEOUT = 3600;
    
    const ORDER_HOT = 1;
    
    const ORDER_NEW = 2;
    
    const PAGE_SIZE = 2;
    
    public function __construct(){
        $this->_model      = new GisModel();
        $this->_logicTopic = new Topic_Logic_Topic();
        $this->_logicSight = new Sight_Logic_Sight();
    }
    
    /**
     * 根据给定点，找附近的景点并拼装话题信息
     * @param double $x
     * @param double $y
     * @param integer $page
     * @param integer $pageSize
     * @return array $arr
     */
    public function getNearSight($x,$y,$page,$pageSize){
        $arr   = array();
        $redis = Base_Redis::getInstance();
        Yaf_Session::getInstance()->set(Home_Keys::SESSION_USER_X_NAME,$x);
        Yaf_Session::getInstance()->set(Home_Keys::SESSION_USER_Y_NAME,$y);
        //找出所有由近到远的景点
        $arr = $this->_model->getNearSight(array(
            'x'=>$x,
            'y'=>$y,            
        ),$page,$pageSize);
        //通过这些景点，取出其它的如城市、话题、答案等信息
        foreach ($arr as $index => $val){
            $objCity = new City_Object_City();
            $objCity->fetch(array('id' => $val['city_id']));
            $arr[$index]['city']  = $objCity->name;            
            $ret   = $redis->get(Sight_Keys::getIndexTopicKey($val['id']));
            if(!empty($ret)){
                $arr[$index]['topic'] = json_decode($ret,true);
            }else{
                $arr[$index]['topic'] = $this->_logicTopic->getHotTopic($val['id']);
                $data = json_encode($arr[$index]['topic']);
                $redis->setex(Sight_Keys::getIndexTopicKey($val['id']),self::REDIS_TIMEOUT,$data);
            }            
            
            //图片用全路径
            if(!empty($val['image'])){                
                $arr[$index]['image']  = Base_Image::getUrlByName($val['image']);
            }else{
                $arr[$index]['image']  = '';
            }
            
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
}