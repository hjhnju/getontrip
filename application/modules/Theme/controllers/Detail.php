<?php
/**
 * 主题详情信息接口
 * @author huwei
 *
 */
class DetailController extends Base_Controller_Page {
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Theme_Logic_Theme();   
    }
    
    /**
     * 接口1：/theme/detail
     * 获取主题信息
     * @param integer id，主题 ID
     * @return json
     */
    public function indexAction(){
        $id  = isset($_POST['id'])?intval($_POST['id']):'';
        if(empty($id)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        } 
        $ret = $this->logic->queryThemeById($id);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
}
