<?php
class Sight_Api{
    
    /**
     * 接口1：Sight_Api::getSightList($page,$pageSize)
     * 获取景点列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getSightList($page,$pageSize){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->getSightList($page, $pageSize);
    }
    
    /**
     * 接口2：Sight_Api::getSightById($sightId)
     * 根据景点ID获取景点详情
     * @param integer $sightId
     * @return array
     */
    public static function getSightById($sightId){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->getSightById($sightId);       
    }
    
    /**
     * 接口3：Sight_Api::getSightByCity($cityId,$page,$pageSize)
     * 根据城市ID获取景点详情
     * @param integer $cityId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getSightByCity($cityId,$page,$pageSize){
        $logicSight = new Sight_Logic_Sight();
        $arr        = $logicSight->getSightListByCity($page, $pageSize, $cityId);
        $num        = count($arr);
        
        $arrRet['page']     = $page;
        $arrRet['pagesize'] = $pageSize;
        $arrRet['pageall']  = ceil($num/$pageSize);
        $arrRet['total']    = $num;
        $arrRet['list']     = $arr;
        return $arrRet;
    }
    
    /**
     * 接口4：Sight_Api::editSight($sightId,$_updateData)
     * 根据$_updateData更新景点信息
     * @param integer $sightId
     * @param array $_updateData: array('describe' =>'xxx','name' => 'xxx');
     * @return integer:更新影响的行数，返回非零值正确
     */
    public static function editSight($sightId,$_updateData){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->editSight($sightId, $_updateData);
    }
    
    /**
     * 接口5：Sight_Api::addSight($arrInfo)
     * 根据$arrInfo添加景点
     * @param array $arrInfo:array('name' => 'xxx','level' => 'xxx');
     * @return integer:更新影响的行数，返回非零值正确
     */
    public static function addSight($arrInfo){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->addSight($arrInfo);
    }
    
    /**
     * 接口6：Sight_Api::delSight($id)
     * 根据ID删除景点信息
     * @param integer $id
     * @return boolean
     */
    public static function delSight($id){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->delSight($id);
    }
    
    /**
     * 接口7：Sight_Api::search($query,$page,$pageSize)
     * 对景点中的标题内容进行模糊查询
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function search($query,$page,$pageSize){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->search($query, $page, $pageSize);
    }
    
    /**
     * 接口：8 Sight_Api::querySights($arrInfo,$page,$pageSize)
     * 根据条件数组筛选景点
     * @param array $arrInfo，条件数组，如:array('id'=1);
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function querySights($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->querySights($arrInfo, $page, $pageSize);
    }
}