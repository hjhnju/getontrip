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
        
        //2015-10-18 范莹莹  增加
        $retList  = $list->toArray();
        $templist = $retList['list'];
        for($i=0; $i<count($templist); $i++){ 
            //类型名称
            $templist[$i]['typename'] =Source_Type_Type::getTypeName($templist[$i]['type']); 
            //分组名称
            $objGroup = new Source_Object_Type();
            $objGroup->fetch(array('id' => $templist[$i]['group']));
            $templist[$i]['groupname']  = $objGroup->name?$objGroup->name:'暂无分组';
        }
        $retList['list'] = $templist;
        //2015-10-18 范莹莹  增加
         
    
        return $retList;
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
        $ret = $objSourceType->save();
        if(isset($arrInfo['source'])){
            foreach ($arrInfo['source'] as $id){
                $objSource = new Source_Object_Source();
                $objSource->fetch(array('id' => $id));
                $objSource->group = $objSourceType->id;
                $objSource->save();
            }
        }
        return $ret;
    }
    
    public function editType($typeId, $arrInfo){
        $objSourceType = new Source_Object_Type();
        $objSourceType->fetch(array('id' => $typeId));
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_type_fields)){
                $key  = $this->getprop($key);
                $objSourceType->$key = $val;
            }
        }
        $ret = $objSourceType->save();
        return $ret;//下面的代码用处是？分组修改是不是只修改名称？fyy 10-18
        $listSource = new Source_List_Source();
        $listSource->setFilter(array('group' => $typeId));
        $listSource->setPagesize(PHP_INT_MAX);
        $arrSource  = $listSource->toArray();
        foreach ($arrSource['list'] as $val){
            $objSource = new Source_Object_Source();
            $objSource->fetch(array('id' => $val['id']));
            $objSource->group = '';
            $objSource->save();
        }
        
        foreach ($arrInfo['source'] as $id){
            $objSource = new Source_Object_Source();
            $objSource->fetch(array('id' => $id));
            $objSource->group = $objSourceType->id;
            $objSource->save();
        }
        return $ret;
    }
    
    public function delType($typeId){
        $objSourceType = new Source_Object_Type();
        $objSourceType->fetch(array('id' => $typeId));
        $ret =  $objSourceType->remove();
        
        $listSource = new Source_List_Source();
        $listSource->setFilter(array('group' => $typeId));
        $listSource->setPagesize(PHP_INT_MAX);
        $arrSource  = $listSource->toArray();
        foreach($arrSource['list'] as $val){
            $objSource = new Source_Object_Source();
            $objSource->fetch(array('id' => $val['id']));
            $objSource->group = '';
            $objSource->save();
        }
        return $ret;
    }
    
    public function listType($page,$pageSize,$arrInfo = array()){
        $listSourceType = new Source_List_Type();
        $listSourceType->setPage($page);
        $listSourceType->setPagesize($pageSize);
        $listSourceType->setFilter($arrInfo);
        $arrType  = $listSourceType->toArray();
        foreach ($arrType['list'] as $key => $val){
            $listSource = new Source_List_Source();
            $listSource->setFilter(array('group' => $val['id']));
            $listSource->setPagesize(PHP_INT_MAX);
            $arrSource  = $listSource->toArray();
            $arrType['list'][$key]['sources'] = $arrSource['list'];
        }
        return $arrType;
    }    
}