<?php

class PopVenderCenerVenderBrandQueryRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.pop.vender.cener.venderBrand.query';
    }

    public function setName($value)
    {
        $this->apiParas['name'] = $value;
        return $this;
    }
}