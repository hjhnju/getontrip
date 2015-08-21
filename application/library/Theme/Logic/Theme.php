<?php
class Theme_Logic_Theme extends Base_Logic{
    
    protected $_fields;
    
    public function __construct(){
        $this->_fields = array('id','name','title','image','content','author','status','create_time','update_time');
    }
    
    /**
     * 添加主题信息
     * @param array $arrInfo，:array('name' => xxx,'landscape' => array(1,2));
     * @return number|'',成功返回主题ID，失败返回空
     */
    public function addTheme($arrInfo){
        $objTheme     = new Theme_Object_Theme();
        $bCheck       = false;
        $arrLandscape = array();
  
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){
                $key                = $this->getprop($key);
                $objTheme->$key = $val;
                $bCheck             = true;
            }
        }
        if($bCheck){
            $ret = $objTheme->save();
        }
        if($ret){
            if(isset($arrInfo['landscape'])){
                foreach ($arrInfo['landscape'] as $val){
                    $objRelation              = new Theme_Object_Landscape();
                    $objRelation->themeId     = $objTheme->id;
                    $objRelation->landscapeId = $val;
                    $objRelation->save();
                }
            }
            return $objTheme->id;
        }
        return '';
    }
    
    /**
     * 编辑主题信息接口
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public function editTheme($id,$arrInfo){
        $bCheck = false;
        $obj    = new Theme_Object_Theme();
        $obj->fetch(array('id' => $id));
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){
                $key = $this->getprop($key);
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if($bCheck){
            $ret1 =  $obj->save();
        }
        if($ret1){
            if(isset($arrInfo['landscape'])){
                $listRelation = new Theme_List_Landscape();
                $listRelation->setFilter(array('theme_id' => $obj->id));
                $listRelation->setPagesize(PHP_INT_MAX);
                $ret = $listRelation->toArray();
                foreach ($ret['list'] as $key => $val){
                    if(!in_array($val['landscape_id'],$arrInfo['landscape'])){
                        $objRelation = new Theme_Object_Landscape();
                        $objRelation->fetch(array('id' => $val['id']));
                        $objRelation->remove();
                        unset($arrInfo['landscape']);
                    }
                }
                if(!empty($arrInfo['landscape'])){
                    foreach ($arrInfo['landscape'] as $landscape_id){
                        $objRelation = new Theme_Object_Landscape();
                        $objRelation->landscapeId = $landscape_id;
                        $objRelation->themeId = $id;
                        $objRelation->save();
                    }
                }
            }
        }
        return $ret1;
    }
    
    /**
     * 删除主题接口
     * @param integer $id
     * @return boolean
     */
    public function  delTheme($id){
        $listRelation = new Theme_List_Landscape();
        $listRelation->setFilter(array('theme_id' => $id));
        $listRelation->setPagesize(PHP_INT_MAX);
        $ret = $listRelation->toArray();
        foreach ($ret['list'] as $val){
            $objRelation = new Theme_Object_Landscape();
            $objRelation->fetch(array('id' => $val['id']));
            $objRelation->remove();
        }
        $obj = new Theme_Object_Theme();
        $obj->fetch(array('id' => $id));
        return $obj->remove();
    }
    
    /**
     * 根据ID查询主题接口
     * @param integer $id
     * @return array
     */
    public function queryThemeById($id){
        $obj          = new Theme_Object_Theme();
        $arrLandscape = array();
        $obj->fetch(array('id' => $id));
        $arrRet = $obj->toArray();
        
        $list = new Theme_List_Landscape();
        $list->setFilter(array('theme_id' => $id));
        $list->setPagesize(PHP_INT_MAX);
        $ret = $list->toArray();
        foreach ($ret['list'] as $val){
            $logicLandscape = new Landscape_Logic_Landscape();
            $objLandscape   = $logicLandscape->queryLandscapeById($val['landscape_id']);
            if($objLandscape['status'] == Landscape_Type_Status::PUBLISHED){
                $arrLandscape[] = $objLandscape;
            }
        }
        $arrRet['landscape'] = $arrLandscape;
        return $arrRet;
    }
    
    /**
     * 根据条件查询主题信息
     * @param array $arrInfo
     * @return array
     */
    public function queryTheme($arrInfo,$page,$pageSize){
        $list = new Theme_List_Theme();
        foreach ($arrInfo as $key => $val){
            if(!in_array($key,$this->_fields)){
                unset($arrInfo[$key]);
            }
            if(isset($arrInfo['status'])&&($arrInfo['status'] == Theme_Type_Status::ALL)){
                unset($arrInfo['status']);
            }
        }
        if(!empty($arrInfo)){
            $list->setFilter($arrInfo);
        }
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $arrRet = $list->toArray();
        
        foreach ($arrRet['list'] as $index => $data){
            $listRelation = new Theme_List_Landscape();
            $listRelation->setFilter(array('theme_id' => $data['id']));
            $listRelation->setPagesize(PHP_INT_MAX);
            $ret = $listRelation->toArray();
            foreach ($ret['list'] as $val){
                $objLandscape = new Landscape_Object_Landscape();
                $objLandscape->fetch(array('id' => $val['landscape_id'],'status' => Landscape_Type_Status::PUBLISHED));
                if(!empty($objLandscape->id)){
                    $arrRet['list'][$index]['landscape'][] = $objLandscape->toArray();
                }
            }
        }
        return $arrRet;
    }
    
    /**
     * 根据名称模糊查询
     * @param array $arrInfo
     * @return array
     */
    public function searchTheme($arrInfo,$page,$pageSize){
        $list = new Theme_List_Theme();
        foreach ($arrInfo as $key => $val){
            if(!in_array($key,$this->_fields)){
                unset($arrInfo[$key]);
            }
            if(isset($arrInfo['status'])&&($arrInfo['status'] == Theme_Type_Status::ALL)){
                unset($arrInfo['status']);
            }
        }
        if(!empty($arrInfo)){
            $filter = '';
            $name = $arrInfo['name'];
            unset($arrInfo['name']);
            foreach ($arrInfo as $key => $val){
                $filter .= " $key = '".$val."' and";
            }
            $filter .= "name like '".$name."%'";
            $list->setFilterString($filter);
        }
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $arrRet = $list->toArray();
        
        foreach ($arrRet['list'] as $index => $data){
            $listRelation = new Theme_List_Landscape();
            $listRelation->setFilter(array('theme_id' => $data['id']));
            $listRelation->setPagesize(PHP_INT_MAX);
            $ret = $listRelation->toArray();
            foreach ($ret['list'] as $val){
                $objLandscape = new Landscape_Object_Landscape();
                $objLandscape->fetch(array('id' => $val['landscape_id'],'status' => Landscape_Type_Status::PUBLISHED));
                if(!empty($objLandscape->id)){
                    $arrRet['list'][$index]['landscape'][] = $objLandscape->toArray();
                }
            }
        }
        return $arrRet;
    }
    
    /**
     * 为主题添加景观
     * @param integer $themeId
     * @param array $arrLandIds
     * @return boolean
     */
    public function addLandscapeToTheme($themeId,$arrLandIds){
        $ret = true;
        foreach ($arrLandIds as $id){
            $obj = new Theme_Object_Landscape();
            $obj->themeId      = $themeId;
            $obj->landscapeId  = $id;
            $ret = $obj->save();
            if(!ret){
                return $ret;
            }
        }
        return $ret;
    }
    
    /**
     * 获取主题列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getThemeList($page=1,$pageSize=PHP_INT_MAX){
        $list          = new Theme_List_Theme();
        $logicCollect  = new Collect_Logic_Collect();
        $list->setFields(array('id','name','image','period'));
        $list->setFilter(array('status' => Theme_Type_Status::PUBLISHED));
        $list->setPage($page);
        $list->setPagesize($pageSize);
        $arrRet = $list->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $arrRet['list'][$key]['collect'] = $logicCollect->getTotalCollectNum(Collect_Keys::THEME,$val['id']);
        }
        return $arrRet['list'];
    }
}