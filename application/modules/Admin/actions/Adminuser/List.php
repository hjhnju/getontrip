<?php
/**
 * 列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() { 
        $userInfo = Admin_Api::getLoggedUser(); 
    	$issuper = Admin_Type_Role::SUPER == $userInfo['role']?1:0; 
    	$this->getView()->assign('issuper', $issuper); 

    	$roleArray=Admin_Type_Role::$names;  
        $this->getView()->assign('roleArray', $roleArray); 
 
    }
}
