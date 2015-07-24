<?php

class CrmMemberSearch extends JosRequest
{

    public function getApiMethod()
    {
        return 'jingdong.crm.member.search';
    }

    public function setCurrentPage($value)
    {
        $this->apiParas['current_page'] = $value;
        return $this;
    }

    public function setPageSize($value)
    {
        $this->apiParas['page_size'] = $value;
        return $this;
    }

    public function setCustomerPin($value)
    {
        $this->apiParas['customer_pin'] = $value;
        return $this;
    }

    public function setGrade($value)
    {
        $this->apiParas['grade'] = $value;
        return $this;
    }

    public function setMinLastTradeTime($value)
    {
        $this->apiParas['min_last_trade_time'] = $value;
        return $this;
    }

    public function setMaxLastTradeTime($value)
    {
        $this->apiParas['max_last_trade_time'] = $value;
        return $this;
    }

    public function setMinTradeCount($value)
    {
        $this->apiParas['min_trade_count'] = $value;
        return $this;
    }

    public function setMaxTradeCount($value)
    {
        $this->apiParas['max_trade_count'] = $value;
        return $this;
    }

    public function setAvgPrice($value)
    {
        $this->apiParas['avg_price'] = $value;
        return $this;
    }

    public function setMinTradeAmount($value)
    {
        $this->apiParas['min_trade_amount'] = $value;
        return $this;
    }
}