<?php
/**
 * PostgreSql数据层Model基类,使用PDO方式连接数据库
 */
class PgBaseModel {

    /**
     */
    protected $db;

    public function __construct() {
        $this->db  = Base_Pg::getPDOInstance("getontrip");
    }
}
