<?php
/**
 * 反馈意见接口
 * @author huwei
 *
 */
class Advise_Api{
    
    /**
     * 接口1：Advise_Api::listAdvise($type,$page,$pageSize)
     * 根据类型查询反馈意见
     * @param integer $type，意见类型：0 未解决，1 已经解决，2 所有
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function listAdvise($type,$page,$pageSize){
        $listAdvise = new Advise_List_Advise();
        $strFileter = '';
        switch($type){
            case Advise_Type::UNTREATED:
                $strFileter = "status = $type";
                break;
            case Advise_Type::SETTLED:
                $strFileter = "status = $type";
                break;
            default:
                break;
        }
        $listAdvise->setFilterString($strFileter);
        $listAdvise->setPage($page);
        $listAdvise->setPagesize($pageSize);
        return $listAdvise->toArray();
    }
    
    /**
     * 接口2：Advise_Api::dealAdvise($id,$type)
     * 标记某个意见的处理状态
     * @param integer $id
     * @return boolean
     */
    public static function dealAdvise($id,$type){
        $objAdvise = new Advise_Object_Advise();
        $objAdvise->fetch(array('id' => $id));
        $objAdvise->status = $type;
        return $objAdvise->save();
    }
}