<?php
/**
 * 标签信息接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    const PAGE_SIZE = 10;
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Tag_Logic_Tag();   
    }
    
    /**
     * 接口1：/api/tag/list
     * 获取所有标签信息
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function listAction(){
        $page     = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize = isset($_POST['pageSize'])?intval($_POST['pageSize']):self::PAGE_SIZE;
        $ret = $this->logic->getTagList($page, $pageSize);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
    
    /**
     * 接口2：/api/tag/hot
     * 获取热门标签接口
     * @param integer size，热门标签TOP 几
     * @return json
     */
    public function hotAction(){
        $size     = isset($_POST['size'])?intval($_POST['size']):self::PAGE_SIZE;
        $ret = $this->logic->getHotTags($size);
        return $this->ajax($ret);
    }
}
