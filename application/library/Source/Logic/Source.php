<?php
class Source_Logic_Source extends Base_Logic{
    
    protected $_fields ;
    
    public function __construct(){   
        $this->_fields = array('id','name','url','create_time','update_time');  
    }
    
    /**
     * 获取来源列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function listSource($page,$pageSize){
        $list = new Source_List_Source();
        $list->setPage($page);
        $list->setPagesize($pageSize);
        return $list->toArray();
    }
    
    /**
     * 添加一个来源
     * @param array $arrInfo
     * @return boolean
     */
    public function addSource($arrInfo){
        $obj    = new Source_Object_Source();
        $bCheck = false;
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){                
                $key = $this->getprop($key);
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if($bCheck){
            return $obj->save();
        }
        return false;
    }
    
    /**
     * 获取来源的名称
     * @param integer $id
     * @return string
     */
    public function getSourceName($id){
        $obj = new Source_Object_Source();
        $obj->fetch(array('id' => $id));
        return $obj->name;
    }
    
    /**
     * 获取来源信息
     * @param integer $id
     * @return array
     */
    public function getSourceInfo($id){
        $obj = new Source_Object_Source();
        $obj->fetch(array('id' => $id));
        return $obj->toArray();
    }
    
    /**
     * 模糊查询来源
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function searchSource($query,$page,$pageSize){
        $list = new Source_List_Source();
        $list->setFilterString("`name` like '%".$query."%'");
        $list->setPage($page);
        $list->setPagesize($pageSize);
        return $list->toArray();
    }
    
    /**
     * 根据名称获取源信息
     * @param string $name
     * @return array
     */
    public function getSourceByName($name){
        $obj = new Source_Object_Source();
        $obj->fetch(array('name' => $name));
        return $obj->toArray();
    }
}