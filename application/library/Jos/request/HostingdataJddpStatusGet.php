<?php

class HostingdataJddpStatusGet extends JosRequest
{

    protected $parameters = [];

    public function getApiMethod()
    {
        return 'jingdong.hostingdata.jddp.status.get';
    }

    public function setParameter($v)
    {
        $this->apiParas['parameter'] = $v;
        return $this;
    }

    public function addParameter($k, $v)
    {
        $this->parameters[$k] = $v;
    }

    public function getAppJsonParams()
    {
        if (isset($this->apiParas['parameter'])) {
            $p = explode(',', $this->apiParas['parameter']);
            foreach ($p as $ps) {
                $pp = explode($ps, '=');
                $this->addParameter($pp[0], $pp[1]);
            }
        }
        $p = '';
        foreach ($this->parameters as $k => $v) {
            $p .= $k . '=' . $v . ',';
        }
        $this->setParameter(substr($p, 0, - 1));
        return parent::getAppJsonParams();
    }
}