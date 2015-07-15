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
        $listTag = new Tag_List_Tag();
        $listTag->setPage($page);
        $listTag->setPagesize($pageSize);
        return $listTag->toArray();
    }
    
    /**
     * 接口2: Tag_Api::editTag($id, $name)
     * 编辑标签信息
     * @param integer $id
     * @param string $name
     * @return boolean
     */
    public static function editTag($id, $name){
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $id));
        $objTag->name = $name;
        return $objTag->save();
    }
    
    /**
     * 接口3：Tag_Api::saveTag($name)
     * 添加标签信息
     * @param string $name
     * @return boolean
     */
    public static function saveTag($name){
        $objTag = new Tag_Object_Tag();
        $objTag->name = $name;
        return $objTag->save();
    }
}