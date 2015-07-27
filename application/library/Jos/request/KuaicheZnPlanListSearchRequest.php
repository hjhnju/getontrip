<?php

class KuaicheZnPlanListSearchRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.kuaiche.zn.plan.list.search';
    }

    public function setPlanName($value)
    {
        $this->apiParas['plan_name'] = $value;
        return $this;
    }

    public function setStatus($value)
    {
        $this->apiParas['status'] = $value;
        return $this;
    }

    public function setIsQueryByStatus($value)
    {
        $this->apiParas['is_query_by_status'] = $value;
        return $this;
    }

    public function setBegin($time)
    {
        $this->apiParas['begin'] = $time;
        return $this;
    }

    public function setEnd($time)
    {
        $this->apiParas['end'] = $time;
        return $this;
    }

    public function setMode($mode)
    {
        $this->apiParas['mode'] = $mode;
        return $this;
    }

    public function setPageSize($size)
    {
        $this->apiParas['page_size'] = $size;
        return $this;
    }

    public function setPageIndex($index)
    {
        $this->apiParas['page_index'] = $index;
        return $this;
    }
}