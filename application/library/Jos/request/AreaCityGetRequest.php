<?php

class AreaCityGetRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.area.city.get';
    }

    public function setParentId($v)
    {
        $this->apiParas['parent_id'] = $v;
        return $this;
    }
}