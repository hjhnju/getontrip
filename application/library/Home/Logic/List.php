<?php
class Home_Logic_List{
    
    private $_model;
    
    protected $_logicTopic;
    
    public function __construct(){
        $this->_model      = new GisModel();
        $this->_logicTopic = new Topic_Logic_Topic();
    }
    
    /**
     * 根据给定点，找附近的点并拼装话题信息
     * @param float $x
     * @param float $y
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
}