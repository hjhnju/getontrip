<?php
/**
 */
class ListController extends Base_Controller_Page {
    
    const PAGE_SIZE = 10;
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Tag_Logic_Tag();   
    }
    
    /**
     * 获取标签信息
     */
    public function indexAction(){
        $page     = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize = isset($_POST['pagesize'])?intval($_POST['pagesize']):self::PAGE_SIZE;
        $ret = $this->logic->getTagList($page, $pageSize);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
    
    /**
     * 获取热门标签接口
     */
    public function hotAction(){
        $size = isset($_POST['size'])?intval($_POST['size']):self::PAGE_SIZE;
    }
}
