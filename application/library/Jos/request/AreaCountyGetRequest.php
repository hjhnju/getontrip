<?php

class AreaCountyGetRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.area.county.get';
    }

    public function setParentId($v)
    {
        $this->apiParas['parent_id'] = $v;
        return $this;
    }
}