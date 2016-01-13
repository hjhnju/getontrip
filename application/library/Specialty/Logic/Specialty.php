<?php
class Specialty_Logic_Specialty extends Base_Logic{
    
    /**
     * 获取特产信息
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public  function getSpecialtys($page,$pageSize,$arrParam = array()){
        $arrSpecialty['list']   = array();
        $arrRet['list']     = array();
        $title    = '';
        if(isset($arrParam['sight_id'])){
            $sightId    = $arrParam['sight_id'];
            $arrSpecialtyIds = array();
            $listSightSpecialty = new Sight_List_Specialty();
            $listSightSpecialty->setFilter(array('sight_id' => $sightId));
            if(isset($arrParam['order'])){
                $listSightSpecialty->setOrder($arrParam['order']);
                unset($arrParam['order']);
            }
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $ret = $listSightSpecialty->toArray();
            if(isset($arrParam['title'])){
                $title  = $arrParam['title'];
                unset($arrParam['title']);
            }
            foreach ($ret['list'] as $val){
                $arrParam = array_merge($arrParam,array('id' => $val['Specialty_id']));
                $objSpecialty = new Specialty_Object_Specialty();
                $objSpecialty->fetch($arrParam);
                $arrTmpSpecialty = $objSpecialty->toArray();
                if(!empty($title) && isset($arrTmpSpecialty['title'])){
                    if( false ==  strstr($arrTmpSpecialty['title'],$title)){
                        continue;
                    }
                }
                if(!empty($arrTmpSpecialty)){
                    $arrSpecialty['list'][] = $arrTmpSpecialty['id'];
                }
            }
            $arrSpecialty['page'] = $page;
            $arrSpecialty['pagesize'] = $pageSize;
            $arrSpecialty['pageall'] = ceil(count($arrSpecialty['list'])/$pageSize);
            $arrSpecialty['total'] = count($arrSpecialty['list']);
            $arrSpecialty['list'] = array_slice($arrSpecialty['list'], ($page-1)*$pageSize,$pageSize);
        }else{
            $listSpecialty = new Specialty_List_Specialty();
            if(!empty($arrParam)){
                $filter = "1";
                if(isset($arrParam['title'])){
                    $filter .= " and `title` like '".$arrParam['title']."%'";
                    unset($arrParam['title']);
                }
                foreach ($arrParam as $key => $val){
                    $filter .= " and `".$key."` =".$val;
                }
                $listSpecialty->setFilterString($filter);
            }
            $listSpecialty->setPage($page);
            $listSpecialty->setPagesize($pageSize);
            $arrSpecialty = $listSpecialty->toArray();
            foreach ($arrSpecialty['list'] as $key => $val){
                $arrSpecialty['list'][$key] = $val['id'];
            }
        }
        $arrRet['page'] = $arrSpecialty['page'];
        $arrRet['pagesize'] = $arrSpecialty['pagesize'];
        $arrRet['pageall'] = $arrSpecialty['pageall'];
        $arrRet['total'] = $arrSpecialty['total'];
        foreach($arrSpecialty['list'] as $key => $val){
            $temp = array();
            $arrSpecialty['list'][$key] = Specialty_Api::getSpecialtyInfo($val);
            $arrSpecialty['list'][$key]['sights'] = array();
            $listSightSpecialty = new Sight_List_Specialty();
            $listSightSpecialty->setFilter(array('Specialty_id' => $val));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $data){
                $sight          = Sight_Api::getSightById($data['sight_id']);
                $temp['id']     = $data['sight_id'];
                $temp['name']   = $sight['name'];
                $temp['weight'] = $data['weight'];
            }
            if(!empty($temp)){
                $arrSpecialty['list'][$key]['sights'][] = $temp;
            }
        
        }
        return $arrSpecialty;
    }
    
    /**
     * 根据景点ID获取特产数量
     * @param integer $sighId
     * @param integer $status
     * @return number
     */
    public function getSpecialtyNum($sighId, $status = Specialty_Type_Status::PUBLISHED){
    }
    
    /**
     * 修改特产信息
     * @param integer $id
     * @param array $arrParam
     */
    public function editSpecialty($id, $arrParam){
        
    }
    
    /**
     * 删除特产
     * @param integer $id
     */
    public function delSpecialty($id){
    }
    
    /**
     * 添加特产
     * @param array $arrParam,array('title'=>'xxx','sight_id'=>1,...)
     */
    public function addSpecialty($arrParam){
    }
    
    /**
     * 根据ID获取特产信息
     * @param string $id
     * @return array
     */
    public function getSpecialtyInfo($id){
    }
    
    /**
     * 修改某景点下的特产的权重
     * @param integer $id ID
     * @param integer $to 需要排的位置
     * @return boolean
     */
    public function changeWeight($sightId,$id,$to){
        $strSpecialtyids = '';
        $model = new SpecialtyModel();
        $ret   = $model->getSightSpecialty($sightId, Specialty_Type_Status::PUBLISHED);
        $strSpecialtyids = implode(",",$ret);
        
        $objSightSpecialty = new Sight_Object_Specialty();
        $objSightSpecialty->fetch(array('sight_id' => $sightId,'Specialty_id' => $id));
        $from       = $objSightSpecialty->weight;
        $objSightSpecialty->weight = $to;
        
        $listSightSpecialty = new Sight_List_Specialty();
        $filter ="`sight_id` =".$sightId." and `Specialty_id` in (".$strSpecialtyids.") and `weight` >= $to and `weight` != $from";
        $listSightSpecialty->setFilterString($filter);
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $key => $val){
            $objTmpSightSpecialty = new Sight_Object_Specialty();
            $objTmpSightSpecialty->fetch(array('id' => $val['id']));
            $objTmpSightSpecialty->weight += 1;
            $objTmpSightSpecialty->save();
        }
        $ret = $objSightSpecialty->save();
        return $ret;
    }
}