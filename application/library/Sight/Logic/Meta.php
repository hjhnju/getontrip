<?php
class Sight_Logic_Meta extends Base_Logic{
    protected $_fileds;

    public function __construct(){   
        $this->_fields = array('id', 'name', 'level', 'image', 'describe', 'impression', 'address', 'type', 'continent', 'country', 'province', 'city', 'region', 'is_china', 'x', 'y', 'url', 'status', 'sight_id', 'create_time', 'update_time');
    }

 
    /**
     * 根据MetaId获取景点元数据
     * @param  integer $metaId 
     * @return array
     */
    public function getSightByMetaId($metaId){
        $objSightMeta = new Sight_Object_Meta();
        $objSightMeta->fetch(array('id' => $metaId));
        $ret = $objSightMeta->toArray();
        if(!empty($ret)){
             
        }
        return $ret;
    }

    /**
     * 根据metaName获取景点元数据
     * @param  integer $metaName 
     * @return array
     */
    public function getSightByMetaName($metaName){
        $objSightMeta = new Sight_Object_Meta();
        $objSightMeta->fetch(array('name' => $metaName));
        $ret = $objSightMeta->toArray();
        if(!empty($ret)){
             
        }
        return $ret;
    }

    /**
     * 根据景点名称模糊查询景点元数据
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function querySightMetaByPrefix($query,$page,$pageSize){
        $arrRet = array();
        $filter = "`name` like '$query"."%'";
        $listSight = new Sight_List_Sight();
        $listSight->setFilterString($filter);
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $ret = $listSight->toArray();
        foreach ($ret['list'] as $val){
            $arrRet[] = array(
                'id'   => $val['id'],
                'name' => $val['name'],
            );
        }
        return $arrRet;
    }

     /**
     * 对景点进行搜索
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function searchMeta($arrInfo,$page,$pageSize){ 
        $list  = new Sight_List_Meta();
        $arrParam   = array();
        $arrParam = array_merge($arrParam,$arrInfo);
        $filter = '';
        
        if(!empty($arrParam)){
            if(isset($arrParam['name'])){
                $filter = "`name` like '%".$arrParam['name']."%'  and "; 
                unset($arrParam['name']);
            }
            if(isset($arrParam['type'])){
                $filter = "`type` like '%".$arrParam['type']."%'  and "; 
                unset($arrParam['type']);
            }
            foreach ($arrParam as $key => $val){
                $filter .= "`".$key."` = '".$val."' and ";
            }
            if(!empty($filter)){
                $filter  = substr($filter,0,-4);
                $list->setFilterString($filter);
            }
            if(!empty($filter)){
                $list->setFilterString($filter);
            }
        }
               
        $list->setPage($page);
        $list->setPagesize($pageSize);
        return $list->toArray();
    }


    /**
     * 根据条件获取国家列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCountryList($arrInfo,$page,$pageSize){ 
        $list  = new Sight_List_Meta();
        $arrParam   = array();
        $arrParam = array_merge($arrParam,$arrInfo);  

        $list->setFields(array('continent','country'));
        $list->setFilter($arrParam);      
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $list->setGroup('`country`');
        return $list->toArray();
    }

    /**
     * 根据条件获取省份列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getProvinceList($arrInfo,$page,$pageSize){ 
        $list  = new Sight_List_Meta();
        $arrParam   = array();
        $arrParam = array_merge($arrParam,$arrInfo);  

        $list->setFields(array('province','city','is_china'));
        $list->setFilter($arrParam);      
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $list->setGroup('`province`');
        return $list->toArray();
    
    }

        /**
     * 根据条件获取城市列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCityList($arrInfo,$page,$pageSize){ 
        $list  = new Sight_List_Meta();
        $arrParam   = array();
        $arrParam = array_merge($arrParam,$arrInfo);  

        $list->setFields(array('city'));
        $list->setFilter($arrParam);      
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $list->setGroup('`city`');
        return $list->toArray();
    }

    /**
     * 根据条件获取地区列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getRegionList($arrInfo,$page,$pageSize){ 
        $list  = new Sight_List_Meta();
        $arrParam   = array();
        $arrParam = array_merge($arrParam,$arrInfo);  

        $list->setFields(array('region'));
        $list->setFilter($arrParam);      
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $list->setGroup('`region`');
        return $list->toArray();
    }



    /**
     * 根据条件获取大洲-国家-省份-城市-地区列表
     * @param string $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCityObjList($arrInfo,$page,$pageSize){  
    
        $logicSightMeta = new Sight_Logic_Meta();
        $logicCityMeta = new City_Logic_City();
        $countryArray = array();
        $continentArray = array('亚洲','欧洲','北美洲','大洋洲','南美洲','非洲','南极洲');
        $cityObj_id = 9999;
        $continentid = 10000;
        $countryid = 0;
        $provinceid = 0;
        
         //1、查询所有大洲列表
        for ($i=0; $i < count($continentArray); $i++) {
           $continentName = $continentArray[$i];
           $cityObj_id++;
           //查询大洲是否存在
           $continent_meta = $logicCityMeta->getCityMeta(array('name'=>$continentName)); 
           if (empty($continent_meta)) { 
               //插入一条大洲信息
               $obj = array(
                          'id'=>$cityObj_id,
                          'name'=>$continentName 
                        );
               $continentid = $logicCityMeta->addCityMeta($obj);  
           }else{ 
               $continentid = $continent_meta['id'];
           } 
           //根据大洲查询所有国家列表
           $countryList = Sight_Api::getCountryList(array('continent'=>$continentName),1,PHP_INT_MAX);
           $countryArray=array_reverse($countryList['list'],false);
           for ($j=0; $j < count($countryArray); $j++) { 
                $countryItem = $countryArray[$j]; 
                $countryName = $countryItem['country'];
                $cityObj_id++;
                //查询国家是否存在
                $country_meta = $logicCityMeta->getCityMeta(array('name'=>$countryName)); 
                if (empty($country_meta)) { 
                   //插入一条国家信息
                   $obj = array(
                      'id'=>$cityObj_id,
                      'name'=>$countryName,
                      'pid'=>$continentid,
                      'continentid'=>$continentid,
                    );
                   $countryid = $logicCityMeta->addCityMeta($obj);
                   
                }else{ 
                   $countryid = $country_meta['id'];
                } 
                if ($countryItem['country']=='中国') {
                    continue;
                } 
                //根据国家名称 查询省份列表
                $provinceList = Sight_Api::getProvinceList(array('country'=>$countryName),1,PHP_INT_MAX);
                $unknownProvinceList = array();
                for ($k=0; $k < count($provinceList['list']); $k++) { 
                    $provinceItem = $provinceList['list'][$k]; 
                    if ($provinceItem['province']==$provinceItem['city']&&$provinceItem['is_china']==0) {
                        # 国外省份 ,城市==省份名称的默认归属到未知省份
                        $provinceName = '';
                        $cityName = $provinceItem['city'];
                        array_push($unknownProvinceList,$cityName);
                        continue;
                    }else{ 
                       $provinceName = $provinceItem['province'];
                    } 
                    $cityObj_id++;
                    //查询省份是否存在
                    $province_meta = $logicCityMeta->getCityMeta(array('name'=>$provinceName,'countryid'=>$countryid)); 
                    if (empty($province_meta)) { 
                       //插入一条省份信息
                       $obj = array(
                          'id'=>$cityObj_id,
                          'name'=>$provinceName,
                          'pid'=>$countryid,
                          'continentid'=>$continentid,
                          'countryid'=>$countryid,
                        );
                       $provinceid = $logicCityMeta->addCityMeta($obj); 
                    }else{ 
                       $provinceid = $province_meta['id']; 
                    }  
                    //根据省份查询城市列表
                    $cityList = Sight_Api::getCityList(array('continent'=>$continentName,'country'=>$countryName,'province'=>$provinceName),1,PHP_INT_MAX);

                    for ($m=0; $m < count($cityList['list']); $m++) {  
                        $cityItem = $cityList['list'][$m]; 
                        $cityName = $cityItem['city'];
                        $city_meta = $logicCityMeta->getCityMeta(array('name'=>$cityName,'provinceid'=>$provinceid)); 
                        $cityObj_id++;
                        if (empty($city_meta)) { 
                           //插入一条城市信息
                           $obj = array(
                              'id'=>$cityObj_id,
                              'name'=>$cityName,
                              'pid'=>$provinceid,
                              'continentid'=>$continentid,
                              'countryid'=>$countryid,
                              'provinceid'=>$provinceid,
                            );
                           $cityid = $logicCityMeta->addCityMeta($obj); 
                        }else{ 
                           $cityid = $province_meta['id'];
                        }
                    }
                    
                } 
                //未知省份  城市级别操作
                if (count($unknownProvinceList)>0) { 
                    //插入一条未知省份信息
                    $cityObj_id++;  
                    $obj = array(
                      'id'=>$cityObj_id,
                      'name'=>'未知省份',
                      'pid'=>$countryid,
                      'continentid'=>$continentid,
                      'countryid'=>$countryid,
                    );
                    $provinceid = $logicCityMeta->addCityMeta($obj); 
                    //如果是未知省份，则当前省份即为城市直接添加即可
                    for ($l=0; $l < count($unknownProvinceList); $l++) { 
                         //如果是未知省份，则当前省份即为城市直接添加即可
                        $cityObj_id++;  
                        //插入一条城市信息
                        $obj = array(
                          'id'=>$cityObj_id,
                          'name'=>$unknownProvinceList[$l],
                          'pid'=>$provinceid,
                          'continentid'=>$continentid,
                          'countryid'=>$countryid,
                          'provinceid'=>$provinceid,
                        );
                       $cityid = $logicCityMeta->addCityMeta($obj);
                    } 
                   
                }
           }
        }
        return '成功！';
    }

 
}