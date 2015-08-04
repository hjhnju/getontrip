<?php

class WareBookbigfieldGetRequest extends JosRequest
{

    public function getApiMethod ()
    {
        return 'jingdong.ware.bookbigfield.get';
    }

    public function setSkuId($fields){
        $this->apiParas['sku_id'] = $fields;
        return $this;
    }
    
}