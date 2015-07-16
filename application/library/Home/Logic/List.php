<?php
class Home_Logic_List{
    
    private $_model;
    
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
     * 根据给定点，找附近的点并拼装话题信息
     * @param double $x
     * @param double $y
     * @param integer $page
     * @param integer $pageSize
     * @return array $arr
     */
    public function getNearSight($x,$y,$page,$pageSize){
        $arr   = array();
        $total = 0;
        $ret = $this->_model->getNearSight(array(
            'x'=>$x,
            'y'=>$y,            
        ));        
        foreach ($ret as $val){
            $num = $this->_logicTopic->getHotTopicNum($val['id']);
            $num = 0;
            $total += $num;
            if($pageSize <= 0){
                break;
            }
            if( $total < ($page-1)*$pageSize){
                continue;
            }else{
                $arr[] = $val;
                $pageSize -= $num; 
            }
        }
        foreach ($arr as $index => $val){
            $arr[$index]['topic'] = $this->_logicTopic->getHotTopic($val['id']);
        }        
        return $arr; 
    }
    
    /**
     * 根据给定位置及过滤条件，获取周边景点信息
     * @param double $x
     * @param double $y
     * @param integer $page
     * @param integer $pageSize
     * @param integer $order
     * @param integer $city
     * @param integer $sight
     * @param string $strTags
     */
    public function getFilterSight($x,$y,$page,$pageSize,$order,$city,$sight,$strTags){
        if(-1 !== $sight){
            $ret = $this->_logicSight->getSightDetail($sight,$page,$pageSize,$strTags);
        }else{
            $arr   = array();
            $total = 0;
            $ret = $this->_model->getNearSight(array(
                'x'=>$x,
                'y'=>$y,            
            ));        
            foreach ($ret as $val){
                $num = $this->_logicTopic->getHotTopicNum($val['id']);
                $num = 0;
                $total += $num;
                if($pageSize <= 0){
                    break;
                }
                if( $total < ($page-1)*$pageSize){
                    continue;
                }else{
                    $arr[] = $val;
                    $pageSize -= $num; 
                }
            }
            if($order == self::ORDER_HOT){
                foreach ($arr as $index => $val){
                    $arr[$index]['topic'] = $this->_logicTopic->getHotTopic($val['id'],self::PAGE_SIZE,$strTags);
                } 
            }else{
                foreach ($arr as $index => $val){
                    $arr[$index]['topic'] = $this->_logicTopic->getNewTopic($val['id'],self::PAGE_SIZE,$strTags);
                }
            }
                   
            return $arr; 
            }
        return $ret;
    }
}