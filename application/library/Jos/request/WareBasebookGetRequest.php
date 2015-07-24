<?php

class WareBasebookGetRequest extends JosRequest
{

    public function getApiMethod ()
    {
        return 'jingdong.ware.basebook.get';
    }
    
    public function setSkuId($fields){
        $this->apiParas['sku_id'] = $fields;
        return $this;
    }
    
}