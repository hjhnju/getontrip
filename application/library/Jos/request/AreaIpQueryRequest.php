<?php

class AreaIpQueryRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.area.ip.query';
    }

    public function setIp($v)
    {
        $this->apiParas['ip'] = $v;
        return $this;
    }
}