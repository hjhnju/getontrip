<?php

class AreaTownGetRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.area.town.get';
    }

    public function setParentId($v)
    {
        $this->apiParas['parent_id'] = $v;
        return $this;
    }
}