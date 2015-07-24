<?php

class KuaicheZnKeywordgroupListSearchRequest extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.kuaiche.zn.keywordgroup.list.search';
    }

    public function setThirdCategoryId($value)
    {
        $this->apiParas['third_category_id'] = $value;
        return $this;
    }

    public function setSortField($value)
    {
        $this->apiParas['sort_field'] = $value;
        return $this;
    }

    public function setSortType($value)
    {
        $this->apiParas['sort_type'] = $value;
        return $this;
    }

    public function setPageSize($value)
    {
        $this->apiParas['page_size'] = $value;
        return $this;
    }

    public function setPageIndex($value)
    {
        $this->apiParas['page_index'] = $value;
        return $this;
    }
}