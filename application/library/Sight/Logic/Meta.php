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
     * 添加新的景点元数据
     * @param array $arrInfo : array('name' => 'xxx','cityid' => 'xxx')
     * @return boolean
     */
    public function addSightMeta($arrInfo){
        $objSightMeta = new Sight_Object_Meta();
        foreach ($arrInfo as $key => $val){
            $key = $this->getprop($key);
            $objSightMeta->$key = $val;
        }
        $objSightMeta->save();
        return $objSightMeta->id;
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
            if (isset($arrParam['city'])) {
                $filter = "`city` like '%".$arrParam['city']."%'  and "; 
                unset($arrParam['city']);
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
     * 修改景点元数据
     * @param integer $id, 元数据ID
     * @param array $arrInfo
     * @return boolean
     */
    public function editMeta($id,$arrInfo){
        $objSightMeta = new Sight_Object_Meta();
        $objSightMeta->fetch(array('id' => $id));
        if(empty($objSightMeta->id)){
            return false;
        }
        foreach ($arrInfo as $key => $val){
            $objSightMeta->$key = $val;
        }
        return $objSightMeta->save();
        return $ret;
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
        //$list->setGroup('`province`');
        $List = $list->toArray();
        //去重
        $List['list'] = $this->array_unique_fb($List['list']);
        return $List;
        for ($i=0; $i < count($List['list']); $i++) { 
           /* if (condition) {
                # code...
            }*/
        }
    
    }

    //二维数组去掉重复值,并保留键值
    function array_unique_fb($array2D){
        foreach ($array2D as $k=>$v){
            $v=join(',',$v);  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[$k]=$v;
        }
        $temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组    
        foreach ($temp as $k => $v){
            $array=explode(',',$v); //再将拆开的数组重新组装
            //下面的索引根据自己的情况进行修改即可
            $temp2[$k]['province'] =$array[0];
            $temp2[$k]['city'] =$array[1];
            $temp2[$k]['is_china'] =$array[2]; 
        }
        return $temp2;
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
     * 根据ID删除景点信息
     * @param integer $id
     * @return boolean
     */
    public function delMeta($id){
        $objSight = new Sight_Object_Meta();
        $objSight->fetch(array('id' => $id));
        $ret = $objSight->remove(); 
        return $ret;
    }
}