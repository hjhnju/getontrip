<?php
class WareBaseproductGetRequest extends JosRequest
{
    public function getApiMethod ()
    {
        return 'jingdong.ware.baseproduct.get';
    }
    
    public function setSkuId($fields){
        $this->apiParas['ids'] = $fields;
        return $this;
    }
    
    public function setBase($fields){
        $this->apiParas['base'] = $fields;
        return $this;
    }
}