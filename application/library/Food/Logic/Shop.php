<?php
class Food_Logic_Shop extends Base_Logic{
        
    protected $fields = array('id', 'food_id','title','addr','phone','score', 'price','x','y','image', 'url','status', 'create_time', 'update_time', 'create_user', 'update_user');
    
    public function getShops($page, $pageSize, $arrParam){
        $listShop = new Food_List_Shop();
        $listShop->setFilter($arrParam);
        $listShop->setPage($page);
        $listShop->setPageSize($pageSize);
        return $listShop->toArray();
    }
    
    public function addShop($arrInfo){
        $objShop = new Food_Object_Shop();
        foreach($arrInfo as $key => $val){
            if(in_array($key,$this->fields)){
                $key              = $this->getprop($key);
                $objShop->$key = $val;
            }
        }
        $ret = $objShop->save();
        if($ret){
            return $objShop->id;
        }
        return '';
    }
    
    public function editShop($id,$arrInfo){
        $objShop = new Food_Object_Shop();
        $objShop->fetch(array('id' => $id));
        foreach($arrInfo as $key => $val){
            if(in_array($key,$this->fields)){
                $key              = $this->getprop($key);
                $objShop->$key = $val;
            }
        }
        return $objShop->save();
    }
    
    public function getShopById($id){
        $objShop = new Food_Object_Shop();
        $objShop->fetch(array('id' => $id));
        return $objShop->toArray();
    }
    
    public function getShopNum($arrPram){
        $listShop = new Food_List_Shop();
        $listShop->setFilter($arrPram);
        return $listShop->getTotal();
    }
}