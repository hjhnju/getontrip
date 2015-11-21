<?php
class Report_Logic_Report extends Base_Logic{
    
    public function add($objId,$userId,$type = Report_Type_Type::COMMENT){
        $objReport = new Report_Object_Report();
        $objReport->fetch(array('objid' => $objId,'userid' => $userId,'type' => $type));
        if(!empty($objReport->id)){
            return Advise_RetCode::REPORT_EXSIT;
        }
        $objReport->objid  = $objId;
        $objReport->userid = $userId;
        $objReport->type   = $type;
        $objReport->status = Report_Type_Status::AUDITING;
        $ret =  $objReport->save();
        if($ret){
            return Advise_RetCode::SUCCESS;
        }
        return Advise_RetCode::UNKNOWN_ERROR;
    }
}