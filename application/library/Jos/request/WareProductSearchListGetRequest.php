<?php

class WareProductSearchListGetRequest extends JosRequest
{

    public function getApiMethod ()
    {
        return 'jingdong.ware.product.search.list.get';
    }

    public function setIsLoadAverageScore ($v)
    {
        $this->apiParas['isLoadAverageScore'] = $v;
        return $this;
    }

    public function setIsLoadPromotion ($v)
    {
        $this->apiParas['isLoadPromotion'] = $v;
        return $this;
    }

    public function setSort ($v)
    {
        $this->apiParas['sort'] = $v;
        return $this;
    }

    public function setClient ($fields)
    {
        $this->apiParas['client'] = $fields;
        return $this;
    }
    
    public function setKeyword($fields){
        $this->apiParas['keyword'] = $fields;
        return $this;
    }
    
    public function setPage($fields){
        $this->apiParas['page'] = $fields;
        return $this;
    }
    
    public function setPagesize($fields){
        $this->apiParas['pageSize'] = $fields;
        return $this;
    }
}