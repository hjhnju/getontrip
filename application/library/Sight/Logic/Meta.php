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
}