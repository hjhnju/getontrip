<?php
/**
 * 主题列表信息接口
 * @author huwei
 *
 */
class ListController extends Base_Controller_Page {
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Theme_Logic_Theme();   
    }
    
    /**
     * 接口1：/theme/list
     * 获取主题信息
     * @return json
     */
    public function indexAction(){
        $ret = $this->logic->getThemeList();
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
}
