<?php
/**
 * 主题详情信息接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Theme_Logic_Theme();   
    }
    
    /**
     * 接口1：/api/theme/detail
     * 获取主题详情信息
     * @param integer id，主题 ID
     * @return json
     */
    public function detailAction(){
        $id  = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        if(empty($id)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        } 
        $ret = $this->logic->queryThemeById($id);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
    
    /**
     * 接口2：/api/theme/list
     * 获取主题列表信息
     * @return json
     */
    public function listAction(){
        $ret = $this->logic->getThemeList();
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }
}
