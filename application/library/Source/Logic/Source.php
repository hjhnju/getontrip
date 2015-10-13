<?php
class Source_Logic_Source extends Base_Logic{
    
    protected $_fields ;
    
    protected $_type_fields;
    
    public function __construct(){   
        $this->_fields = array('id','name','url','type','create_time','update_time','group'); 
        $this->_type_fields = array('id','name','create_time','update_time','creat_user','update_user');
    }
    
    /**
     * 获取来源列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function listSource($page,$pageSize,$arrInfo = array()){
        $list = new Source_List_Source();
        foreach ($arrInfo as $key => $val){
            if(!in_array($key,$this->_fields)){
                unset($arrInfo[$key]);
            }
        }
        if(!empty($arrInfo)){
            $list->setFilter($arrInfo);
        }
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
     * 编辑一个来源
     * @param array $arrInfo
     * @return boolean
     */
    public function editSource($sourceId,$arrInfo){
        $obj    = new Source_Object_Source();
        $obj->fetch(array('id' => $sourceId));
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
     * @param array   $arrConf
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function searchSource($arrConf,$page,$pageSize){
        $list   = new Source_List_Source();
        $filter = "";
        if(isset($arrConf['name'])){
            $filter = "`name` like '%".$arrConf['name']."%' and ";
            unset($arrConf['name']);
        }
               
        foreach ($arrConf as $key => $val){
            $filter .= "`$key` = $val and ";
        }
        if(!empty($filter)){
            $filter  = substr($filter,0,-4);
            $list->setFilterString($filter);
        }        
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
    
    /**
     * 获取热门来源
     * @return array
     */
    public function getHotSource(){
        //2是其他来源，故放在最后
        $arrHotSourceIds = array(1,6,14,48,2);
        $arrHotSource    = array();       
        foreach ($arrHotSourceIds as $id){
            $objSource = new Source_Object_Source();
            $objSource->fetch(array('id' => $id));
            $arrHotSource[] = $objSource->toArray();
        }      
        return $arrHotSource;       
    }
    
    /**
     * 删除来源
     * @param integer $sourceId
     * @return boolean
     */
    public function delSource($sourceId){
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilter(array('from' => $sourceId));
        $listTopic->setPagesize(PHP_INT_MAX);
        $arrTopics = $listTopic->toArray();
        if(!empty($arrTopics['list'])){
            return false;
        }
        $obj = new Source_Object_Source();        
        $obj->fetch(array('id' => $sourceId));
        return $obj->remove();
    }
    
    public function addType($arrInfo){
        $objSourceType = new Source_Object_Type();
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_type_fields)){
                $key  = $this->getprop($key);
                $objSourceType->$key = $val;
            }
        }
        return $objSourceType->save();
    }
    
    public function delType($typeId){
        $objSourceType = new Source_Object_Type();
        $objSourceType->fetch(array('id' => $typeId));
        return $objSourceType->remove();
    }
    
    public function listType($page,$pageSize,$arrInfo = array()){
        $listSourceType = new Source_List_Type();
        $listSourceType->setPage($page);
        $listSourceType->setPagesize($pageSize);
        $listSourceType->setFilter($arrInfo);
        return $listSourceType->toArray();
    }    
}