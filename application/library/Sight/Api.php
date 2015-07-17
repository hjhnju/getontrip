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
        $model = new SightModel();
        $arr = $model->getSightList($page,$pageSize);
        $num = $model->getSightNum();
        $arrRet['page']     = $page;
        $arrRet['pagesize'] = $pageSize;
        $arrRet['pageall']  = ceil($num/$pageSize);
        $arrRet['total']    = $num;
        $arrRet['list']     = $arr;
        return $arrRet;
    }
    
    /**
     * 接口2：Sight_Api::getSightById($sightId)
     * 根据景点ID获取景点详情
     * @param integer $sightId
     * @return array
     */
    public static function getSightById($sightId){
        $model = new SightModel();
        $arr = $model->getSightById($sightId);
        return $arr;        
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
        $model = new SightModel();
        $arr = $model->getSightByCity($page,$pageSize,$cityId);
        $num = $model->getSightNum($cityId);
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
        $model = new SightModel();
        $ret = $model->eddSight($sightId, $_updateData);
        return $ret;
    }
    
    /**
     * 接口5：Sight_Api::addSight($arrInfo)
     * 根据$arrInfo添加景点
     * @param array $arrInfo:array('name' => 'xxx','level' => 'xxx');
     * @return integer:更新影响的行数，返回非零值正确
     */
    public static function addSight($arrInfo){
        $model = new SightModel();
        $ret = $model->addNewSight($arrInfo);
        return $ret;
    }
    
    /**
     * 接口6：Sight_Api::delSight($id)
     * 根据ID删除景点信息
     * @param integer $id
     * @return boolean
     */
    public static function delSight($id){
        $model = new SightModel();
        $ret = $model->delSight($id);
        return $ret;
    }
}