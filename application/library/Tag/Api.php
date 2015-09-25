<?php
/**
 * 标签接口
 * @author huwei
 */
class Tag_Api{
    
    /**
     * 接口1: Tag_Api::getTagList($page, $pageSize,$arrParam = array())
     * 获取标签信息
     * @param integer $page
     * @param integer $pageSize
     * @param array   $arrParam
     * @return array
     */
    public static function getTagList($page, $pageSize, $arrParam = array()){
       $logicTag = new Tag_Logic_Tag();
       return $logicTag->getTagList($page, $pageSize, $arrParam);
    }
    
    /**
     * 接口2: Tag_Api::editTag($id, $arrInfo)
     * 编辑标签信息
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public static function editTag($id, $arrInfo){
        $logicTag = new Tag_Logic_Tag();
        return $logicTag->editTag($id, $arrInfo);
    }
    
    /**
     * 接口3：Tag_Api::saveTag($arrInfo)
     * 添加标签信息
     * @param array $arrInfo
     * @return boolean
     */
    public static function saveTag($arrInfo){
        $logicTag = new Tag_Logic_Tag();
        return $logicTag->saveTag($arrInfo);
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
    
    /**
     * 接口5：Tag_Api::getTagByName($name)
     * 根据名称获取标签信息
     * @param string $name
     * @return array
     */
    public static function getTagByName($name){
        $logicTag = new Tag_Logic_Tag();
        return $logicTag->getTagByName($name);
    }
}