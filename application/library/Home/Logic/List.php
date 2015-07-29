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
        $total = 0;
        //找出所有由近到远的景点
        $ret = $this->_model->getNearSight(array(
            'x'=>$x,
            'y'=>$y,            
        ));    
        //从景点中找出包含了所要求的话题范围的景点    
        foreach ($ret as $val){
            $num = $this->_logicTopic->getHotTopicNum($val['id'],"1 month ago");
            if(empty($num)){
                continue;
            }
            $total += $num;
            if($pageSize <= 0){
                if($pageSize < 0){  
                   array_pop($arr); 
                }
                break;
            }
            if( $total < ($page-1)*$pageSize){
                continue;
            }else{
                $arr[] = $val;
                $pageSize -= $num; 
            }
        }
        //通过这些景点，取出其它的如城市、话题、答案等信息
        foreach ($arr as $index => $val){
            $objCity = new City_Object_City();
            $objCity->fetch(array('id' => $val['city_id']));
            $arr[$index]['city']  = $objCity->name;
            $arr[$index]['topic'] = $this->_logicTopic->getHotTopic($val['id']);
            
            //图片用全路径
            if(!empty($arr[$index]['image'])){
                $arr[$index]['image']  = "http://".$_SERVER['HTTP_HOST']."/Pic/".$arr[$index]['image'].".jpg";
            }
            
            //距离转换成KM
            $arr[$index]['dis'] = strval(floor($arr[$index]['dis']/1000));
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