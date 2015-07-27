<?php

class KuaicheZnPlanSearchKeywordDeleteRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.kuaiche.zn.plan.search.keyword.delete';
    }

    public function setPlanId($value)
    {
        $this->apiParas['plan_id'] = $value;
    }

    public function setKeywordIds($value)
    {
        $this->apiParas['keyword_ids'] = $value;
    }

}