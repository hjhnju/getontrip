<?php

class KuaicheZnPlanSearchKeywordUpdateRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.kuaiche.zn.plan.search.keyword.update';
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