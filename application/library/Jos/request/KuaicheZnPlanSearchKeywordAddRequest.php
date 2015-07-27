<?php

class KuaicheZnPlanSearchKeywordAddRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.kuaiche.zn.plan.search.keyword.add';
    }

    public function setPlanId($value)
    {
        $this->apiParas['plan_id'] = $value;
    }

    public function setKeywordPrice($value)
    {
        $this->apiParas['keyword_price'] = $value;
    }
}