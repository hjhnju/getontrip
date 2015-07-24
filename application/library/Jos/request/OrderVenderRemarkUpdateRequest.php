<?php

class OrderVenderRemarkUpdateRequest extends JosRequest
{

    public function getApiMethod()
    {
        return '360buy.order.vender.remark.update';
    }

    public function setRemark($val)
    {
        $this->apiParas['remark'] = $val;
        return $this;
    }

    public function setOrderId($orderId)
    {
        $this->apiParas['order_id'] = $orderId;
        return $this;
    }

    public function setTradeNo($val)
    {
        $this->apiParas['trade_no'] = $val;
        return $this;
    }

    public function setFlag($val)
    {
        $this->apiParas['flag'] = $val;
        return $this;
    }
}