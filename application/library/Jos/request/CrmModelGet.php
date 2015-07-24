<?php

class CrmModelGet extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.crm.model.get';
    }

    public function setModelId($value)
    {
        $this->apiParas['model_id'] = $value;
        return $this;
    }

}