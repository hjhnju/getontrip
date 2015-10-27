<?php
/**
 * 数据层Model基类
 */
class BaseModel {

    /**
     */
    public $db;

    public function __construct() {
        $this->db  = Base_Db::getInstance('getontrip');
    }
}
