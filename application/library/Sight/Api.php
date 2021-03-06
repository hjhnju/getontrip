<?php
class Sight_Api{
    
    /**
     * 接口1：Sight_Api::getSightList($page,$pageSize)
     * 获取景点列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getSightList($page,$pageSize,$status=Sight_Type_Status::ALL){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->getSightList($page, $pageSize,$status);
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
     * 根据城市ID获取景点列表
     * @param integer $cityId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getSightByCity($cityId,$page,$pageSize){
        $logicSight = new Sight_Logic_Sight();
        return  $logicSight->getSightListByCity($page, $pageSize, $cityId);
    }
    
    /**
     * 接口4：Sight_Api::editSight($sightId,$_updateData)
     * 根据$_updateData更新景点信息
     * @param integer $sightId
     * @param array $_updateData: array('describe' =>'xxx','name' => 'xxx','tags' => array(1,2));
     * @return integer:更新影响的行数，返回非零值正确
     */
    public static function editSight($sightId,$_updateData){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->editSight($sightId, $_updateData);
    }
    
    /**
     * 接口5：Sight_Api::addSight($arrInfo)
     * 根据$arrInfo添加景点
     * @param array $arrInfo:array('name' => 'xxx','level' => 'xxx','tags' => array(1,2));
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
    public static function search($query,$page,$pageSize){
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
    public static function querySights($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->querySights($arrInfo, $page, $pageSize);
    }
    
    /**
     * 接口9：Sight_Api::getTopicNum($sightId,$arrInfo=array())
     * 获取景点的话题数,如果不传景点ID，则表示查询所有话题数
     * @param integer $sightId
     * @param array $arrInfo,过滤条件，话题的一些属性
     * @return integer 
     */
    public static function getTopicNum($sightId='',$arrInfo= array('status' => Topic_Type_Status::PUBLISHED)){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->getTopicNum($sightId,$arrInfo);
    }
    
    /**
     * 接口10：Sight_Api::getKeywordNum($sightId)
     * 获取景点词条数
     * @param integer  $sightId
     * @return integer
     */
    public static function getKeywordNum($sightId,$arrInfo=array()){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->getKeywordNum($sightId);
    }
    
    /**
     * 接口11：Sight_Api::getSightNum($arrInfo)
     * 根据条件获取景点数量
     * @param array $arrInfo
     * @return integer
     */
    public static function getSightNum($arrInfo){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->getSightsNum($arrInfo);
    }
    
    /**
     * 接口12：Sight_Api::checkSightName($name)
     * 检验所给景点名称是否存在
     * @param string $name
     * @return boolean 存在：true,不存在：false
     */
    public static function checkSightName($name){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->checkSightName($name);
    }
    
    /**
     * 接口13：querySightByPrefix($query,$page,$pageSize)
     * 根据景点名称模糊查询景点ID
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function querySightByPrefix($query,$page,$pageSize){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->querySightByPrefix($query, $page, $pageSize);
    }
    
    /**
     * 接口14：Sight_Api::publishSight($sightId,$bDoPublish)
     * 景点发布与取消发布接口
     * @param integer $sightId
     * @param boolean,$bDoPublish:true发布，false取消发布
     * @return boolean
     */
    public static function publishSight($sightId,$bDoPublish){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->publishSight($sightId, $bDoPublish);
    }

    /**
     * 接口15：Sight_Api::getSightByMetaId($metaId)
     * 根据MetaId获取景点元数据
     * @param integer $metaId 
     * @return array
     */
    public static function getSightByMetaId($metaId){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->getSightByMetaId($metaId);
    }


    /**
     * 接口16：querySightMetaByPrefix($query,$page,$pageSize)
     * 根据景点名称模糊查询景点元数据
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function querySightMetaByPrefix($query,$page,$pageSize){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->querySightMetaByPrefix($query, $page, $pageSize);
    }

    /**
     * 接口17：Sight_Api::searchMeta($arrInfo,$page,$pageSize)
     * 对景点中的标题内容进行模糊查询
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function searchMeta($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->searchMeta($arrInfo, $page, $pageSize);
    }

    /**
     * 接口18：Sight_Api::getSightByMetaName($metaName)
     * 根据MetaId获取景点元数据
     * @param integer $metaName 
     * @return array
     */
    public static function getSightByMetaName($metaName){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->getSightByMetaName($metaName);
    }

    /**
     * 接口19：Sight_Api::getCountryList($arrInfo,$page,$pageSize)
     * 根据条件获取国家列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getCountryList($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->getCountryList($arrInfo, $page, $pageSize);
    }

    /**
     * 接口20：Sight_Api::getProvinceList($arrInfo,$page,$pageSize)
     * 根据条件获取国家列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getProvinceList($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->getProvinceList($arrInfo, $page, $pageSize);
    }

    /**
     * 接口21：Sight_Api::getCityList($arrInfo,$page,$pageSize)
     * 根据条件获取城市列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getCityList($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->getCityList($arrInfo, $page, $pageSize);
    }

     /**
     * 接口22：Sight_Api::getRegionList($arrInfo,$page,$pageSize)
     * 根据条件获取地区列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getRegionList($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->getRegionList($arrInfo, $page, $pageSize);
    }

    /**
     * 接口23：Sight_Api::getCityObjList($arrInfo,$page,$pageSize)
     * 根据条件获取大洲-国家-省份-城市-地区列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getCityObjList($arrInfo,$page,$pageSize){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->getCityObjList($arrInfo, $page, $pageSize);
    }
    
    /**
     * 接口24：Sight_Api::editMeta($id, $arrInfo)
     * 修改景点元数据信息
     * @param integer $id
     * @param array $arrInfo
     * @return boolen
     */
    public static function editMeta($id, $arrInfo){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->editMeta($id, $arrInfo);
    }
    
    /**
     * 接口25：Sight_Api::isExistById($id)
     * 根据景点ID判断是否保存到sight表里面
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function isExistById($id){
        $logicSight = new Sight_Logic_Sight();
        return $logicSight->isExistById($id);
    }
    
    /**
     * 接口26：Sight_Api::addSightMeta($arrInfo)
     * 添加景点元数据信息
     * @param array $arrInfo
     * @return boolen
     */
    public static function addSightMeta($arrInfo){
        $logicSight = new Sight_Logic_Meta();
        return $logicSight->addSightMeta($arrInfo);
    }  
    
}