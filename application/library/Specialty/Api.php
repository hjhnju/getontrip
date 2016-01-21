<?php
class Specialty_Api{
    
    /**
     * 接口1：Specialty_Api::getSpecialtys($page,$pageSize,$arrParam = array())
     * 获取特产信息
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public static function getSpecialtys($page,$pageSize,$arrParam = array()){
        $logicSpecialty = new Specialty_Logic_Specialty();
        return    $logicSpecialty->getSpecialtys($page,$pageSize,$arrParam);
    }
    
    /**
     * 接口2：Specialty_Api::getSpecialtyNum($sighId)
     * 根据景点ID获取特产数量
     * @param integer $sighId
     * @param integer $status
     * @return number
     */
    public static function getSpecialtyNum($sighId, $type = Destination_Type_Type::SIGHT, $status = Specialty_Type_Status::PUBLISHED){
        $logicSpecialty = new Specialty_Logic_Specialty();
        return $logicSpecialty->getSpecialtyNum($sighId, $type, $status);
    }
    
    /**
     * 接口3:Specialty_Api::editSpecialty($id, $arrParam)
     * 修改特产信息
     * @param integer $id
     * @param array $arrParam
     */
    public static function editSpecialty($id, $arrParam){
        $logicSpecialty = new Specialty_Logic_Specialty();
        return $logicSpecialty->editSpecialty($id, $arrParam);
    }
    
    /**
     * 接口4:Specialty_Api::delSpecialty($id)
     * 删除特产
     * @param integer $id
     */
    public static function delSpecialty($id){
        $logicSpecialty = new Specialty_Logic_Specialty();
        return $logicSpecialty->delSpecialty($id);
    }
    
    /**
     * 接口5:Specialty_Api::addSpecialty($arrParam)
     * 添加特产
     * @param array $arrParam,array('title'=>'xxx','sight_id'=>1,...)
     */
    public static function addSpecialty($arrParam){
        $logicSpecialty = new Specialty_Logic_Specialty();
        return $logicSpecialty->addSpecialty($arrParam);
    }
    
    /**
     * 接口6:Specialty_Api::getSpecialtyInfo($id)
     * 根据ID获取特产信息
     * @param string $id
     * @return array
     */
    public static function getSpecialtyInfo($id){
        $logicSpecialty = new Specialty_Logic_Specialty();
        return $logicSpecialty->getSpecialtyByInfo($id);
    }
    
    /**
     * 接口7：Specialty_Api::changeWeight($sightId,$id,$to)
     * 修改某景点下的特产的权重
     * @param integer $id ID
     * @param integer $to 需要排的位置
     * @return boolean
     */
    public static function changeWeight($sightId,$type,$id,$to){
        $logicSpecialty = new Specialty_Logic_Specialty();
        return $logicSpecialty->changeWeight($sightId,$type,$id,$to);
    }
    
    public static function getProductList($page,$pageSize,$arrParam = array()){
        $logicProduct = new Specialty_Logic_Product();
        return $logicProduct->getProductList($page, $pageSize, $arrParam);
    }
    
    public static function getProductById($id){
        $logicProduct = new Specialty_Logic_Product();
        return $logicProduct->getProductById($id);
    }
    
    public static function addProduct($arrInfo){
        $logicProduct = new Specialty_Logic_Product();
        return $logicProduct->addProduct($arrInfo);
    }
    
    public static function editProduct($id,$arrInfo){
        $logicProduct = new Specialty_Logic_Product();
        return $logicProduct->editProduct($id, $arrInfo);
    }
}