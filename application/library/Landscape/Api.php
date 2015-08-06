<?php
/**
 * 景观接口api
 * @author huwei
 *
 */
class Landscape_Api{
    
    /**
     * 接口1：Landscape_Api::addLandscape($arrInfo)
     * 添加景观
     * @param array $arrInfo
     * @return number|''，成功返回ID，失败返回空
     */
    public static function addLandscape($arrInfo){
        $logic = new Landscape_Logic_Landscape();
        return $logic->addLandscape($arrInfo);
    }
    
    /**
     * 接口2：Landscape_Api::editLandscape($id,$arrInfo)
     * 编辑景观
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public static function editLandscape($id,$arrInfo){
        $logic = new Landscape_Logic_Landscape();
        return $logic->editLandscape($id, $arrInfo);
    }
    
    /**
     * 接口3：Landscape_Api::delLandscape($id)
     * 删除景观
     * @param integer $id
     * @return boolean
     */
    public static function delLandscape($id){
        $logic = new Landscape_Logic_Landscape();
        return $logic->delLandscape($id);
    }
    
    /**
     * 接口4：Landscape_Api::queryLandscape($arrInfo,$page,$pageSize)
     * 根据条件查询景观信息
     * @param array $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function queryLandscape($arrInfo,$page,$pageSize){
        $logic = new Landscape_Logic_Landscape();
        return $logic->queryLandscape($arrInfo, $page, $pageSize);
    }
    
    /**
     * 接口5：Landscape_Api::queryLandscapeById($id)
     * 根据ID查询景观
     * @param integer $id
     * @return array
     */
    public static function queryLandscapeById($id){
        $logic = new Landscape_Logic_Landscape();
        return $logic->queryLandscapeById($id);
    }
}