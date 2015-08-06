<?php
/**
 * 主题信息接口
 * @author huwei
 *
 */
class Theme_Api{
    /**
     * 接口1：Theme_Api::addTheme($arrInfo)
     * 添加主题信息
     * @param array $arrInfo，:array('name' => xxx,'landscape' => array(1,2));
     * @return number|'',成功返回主题ID，失败返回空
     */
    public static function addTheme($arrInfo){
        $logic = new Theme_Logic_Theme();
        return $logic->addTheme($arrInfo);
    }
    
    /**
     * 接口2：Theme_Api::editTheme($id,$arrInfo)
     * 编辑主题信息接口
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public static function editTheme($id,$arrInfo){
        $logic = new Theme_Logic_Theme();
        return $logic->editTheme($id, $arrInfo);
    }
    
    /**
     * 接口3：Theme_Api::delTheme($id)
     * 删除主题接口
     * @param integer $id
     * @return boolean
     */
    public static function  delTheme($id){
        $logic = new Theme_Logic_Theme();
        return $logic->delTheme($id);
    }
    
    /**
     * 接口4：Theme_Api::queryThemeById($id)
     * 根据ID查询主题接口
     * @param integer $id
     * @return array
     */
    public static function queryThemeById($id){
        $logic = new Theme_Logic_Theme();
        return $logic->queryThemeById($id);
    }
    
    /**
     * 接口5：Theme_Api::queryTheme($arrInfo,$page,$pageSize)
     * 根据条件查询主题信息
     * @param array $arrInfo
     * @return array
     */
    public static function queryTheme($arrInfo,$page,$pageSize){
        $logic = new Theme_Logic_Theme();
        return $logic->queryTheme($arrInfo, $page, $pageSize);
    }
    
    /**
     * 接口6：Theme_Api::searchTheme($arrInfo,$page,$pageSize)
     * 根据名称模糊查询
     * @param array $arrInfo
     * @return array
     */
    public static function searchTheme($arrInfo,$page,$pageSize){
        $logic = new Theme_Logic_Theme();
        return $logic->searchTheme($arrInfo, $page, $pageSize);
    }
    
    /**
     * 接口7：Theme_Api::addLandscapeToTheme($themeId,$arrLandIds)
     * 为主题添加景观
     * @param integer $themeId
     * @param array $arrLandIds
     * @return boolean
     */
    public function addLandscapeToTheme($themeId,$arrLandIds){
        $logic = new Theme_Logic_Theme();
        return $logic->addLandscapeToTheme($themeId, $arrLandIds);
    }
}