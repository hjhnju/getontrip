<?php
/**
 * 搜索逻辑层
 * @author huwei
 *
 */
class Search_Logic_Search{
    
    protected $logicCity;
    
    protected $logicSight;
    
    protected $logicTopic;
    
    protected $logicTheme;
    
    public function __construct(){
        $this->logicCity  = new City_Logic_City();
        $this->logicSight = new Sight_Logic_Sight();
        $this->logicTopic = new Topic_Logic_Topic();
        $this->logicTheme = new Theme_Logic_Theme();
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
    public function search($query, $page, $pageSize,$x,$y){
        $arrCity  = $this->logicCity->queryCityPrefix($query, $page, $pageSize);
        $arrSight = $this->logicSight->search($query, $page, $pageSize,$x,$y);
        $arrTopic = $this->logicTopic->searchTopic($query, $page, $pageSize);
        $arrTheme = $this->logicTheme->searchTheme(array('name' => $query), $page, $pageSize);
        return array(
            'city'  => $arrCity['list'],
            'sight' => $arrSight,
            'topic' => $arrTopic['list'],
            'theme' => $arrTheme,
        );
    }
}