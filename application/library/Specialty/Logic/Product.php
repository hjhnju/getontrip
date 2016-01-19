<?php
class Specialty_Logic_Product extends Base_Logic{
    
    protected $fields = array('id', 'specialty_id','title', 'content','image', 'url', 'price','status', 'create_time', 'update_time', 'create_user', 'update_user');
    
    public function getProductById($id){
        $objProduct = new Specialty_Object_Product();
        $objProduct->fetch(array('id' => $id));
        return $objProduct->toArray();
    }
    
    public function getProductList($page, $pageSize,$arrParam = array()){
        $listProduct = new Specialty_List_Product();
        if(!empty($arrParam)){
            $listProduct->setFilter($arrParam);
        }
        $listProduct->setPage($page);
        $listProduct->setPagesize($pageSize);
        return $listProduct->toArray();
    }
    
    public function addProduct($arrInfo){
        $objProduct = new Specialty_Object_Product();
        foreach($arrInfo as $key => $val){
            if(in_array($key,$this->fields)){
                $key              = $this->getprop($key);
                $objProduct->$key = $val;
            }
        }
        $ret = $objProduct->save();
        if($ret){
            return $objProduct->id;
        }
        return '';
    }
    
    public function editProduct($id,$arrInfo){
        $objProduct = new Specialty_Object_Product();
        $objProduct->fetch(array('id' => $id));
        foreach($arrInfo as $key => $val){
            if(in_array($key,$this->fields)){
                $key              = $this->getprop($key);
                $objProduct->$key = $val;
            }
        }
        return $objProduct->save();
    }
    
    public function getProductNum($arrPram){
        $listProduct = new Specialty_List_Product();
        $listProduct->setFilter($arrPram);
        return $listProduct->getTotal();
    }
}