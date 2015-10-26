<?php
/**
 * 标签关系管理
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $arrIds = array();
        $id     = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
      	$arrRet = Tag_Api::getTagList(1, PHP_INT_MAX,array('type' => Tag_Type_Tag::TOP_CLASS));
      	foreach ($arrRet['list'] as $index => $data){
      	    if($data['id'] == $id){
      	        $arrRet['list'][$index]['checked'] = true;
      	    }else{
      	        $arrRet['list'][$index]['checked'] = false;
      	    }
      	}
      	if(empty($id)){
      	    $arrRet['list'][0]['checked'] = true;
      	}
        $this->_view->assign('data_first', $arrRet['list']);  
        
        $id = empty($id)?$arrRet['list'][0]['id']:$id;
        $arrSub = Tag_Api::getTagRelation($id, 1, 10);
        foreach ($arrSub['list'] as $key => $val){
            $arrIds[] = $val['classifytag_id'];
        }
        
        $arrRet = Tag_Api::getTagList(1, PHP_INT_MAX,array('type' => Tag_Type_Tag::CLASSIFY));
        foreach ($arrRet['list'] as $key => $val){
            if(in_array($val['id'],$arrIds)){
                $arrRet['list'][$key]['checked'] = true;
            }else{
                $arrRet['list'][$key]['checked'] = false;
            }
        }
        $this->_view->assign('data_second', $arrRet['list']);
    }
}
