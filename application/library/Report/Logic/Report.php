<?php
class Report_Logic_Report extends Base_Logic{
    
    public function add($objId,$userId,$type = Report_Type_Type::COMMENT){
        $objReport = new Report_Object_Report();
        $objReport->objid  = $objId;
        $objReport->userid = $userId;
        $objReport->type   = $type;
        $objReport->status = Report_Type_Status::AUDITING;
        return $objReport->save();
    }
}