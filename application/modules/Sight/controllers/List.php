<?php
/**
 * 景点列表接口
 * @author xingjiaodai-ux
 *
 */
class ListController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：获取景点列表 /sight/list
     * @param integer $page
     * @param integer $pageSize
     * @param integer $cityId
     * @return array
     */
    public function indexAction() {
        //$page       = $_POST['page'];
        //$pageSize   = $_POST['pageSize'];
        //$cityId      = isset($_POST['cityId'])?$_POST['cityId']:'';
        $page = 1;
        $pageSize = 10;
        $cityId = 1;
        $logic      = new Sight_Logic_Sight();
        if(!empty($cityId)){
            $ret  =  Sight_Api::getSightList($page,$pageSize);
        }else{
            $ret  =  Sight_Api::getSightByCity($cityId,$page,$pageSize);
        }
        
        $this->ajax($ret);
    }
    
}
