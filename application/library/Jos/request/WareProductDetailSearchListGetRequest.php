<?php
class WareProductDetailSearchListGetRequest extends JosRequest
{

    public function getApiMethod (){
        return "jingdong.ware.product.detail.search.list.get";
    }
    
    public function setSkuId($fields){
        $this->apiParas['skuId'] = $fields;
        return $this;
    }
    
    public function setClient($fields){
        $this->apiParas['client'] = $fields;
        return $this;
    }
    
    public function setIsLoadWareScore($fields){
        $this->apiParas['isLoadWareScore'] = $fields;
        return $this;
    }
}