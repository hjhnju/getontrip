<?php
/**
 * 标签接口
 * @author huwei
 */
class Tag_Api{
    
    /**
     * 接口1: Tag_Api::getTagList($page, $pageSize)
     * 获取标签信息
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getTagList($page, $pageSize){
       $logicTag = new Tag_Logic_Tag();
       return $logicTag->getTagList($page, $pageSize);
    }
    
    /**
     * 接口2: Tag_Api::editTag($id, $name)
     * 编辑标签信息
     * @param integer $id
     * @param string $name
     * @return boolean
     */
    public static function editTag($id, $name){
        $logicTag = new Tag_Logic_Tag();
        return $logicTag->editTag($id, $name);
    }
    
    /**
     * 接口3：Tag_Api::saveTag($name)
     * 添加标签信息
     * @param string $name
     * @return boolean
     */
    public static function saveTag($name){
        $logicTag = new Tag_Logic_Tag();
        return $logicTag->saveTag($name);
    }
    
    /**
     * 接口4：Tag_Api::delTag($id)
     * 标签删除接口
     * @param integer $id
     * @return boolean
     */
    public static function delTag($id){
        $logicTag = new Tag_Logic_Tag();
        return $logicTag->delTag($id);
    }
}