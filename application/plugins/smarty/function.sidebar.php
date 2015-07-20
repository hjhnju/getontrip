<?php
/**
 * 通过配置载入侧边栏
 * @param array $params
 * @param Smarty $smarty
 */
function smarty_function_sidebar($params, &$smarty) {
    $filename = MODULE_CONF_PATH . '/sidebar.ini';
    $value = Base_Config::getConfig('sidebar', $filename);
    
    $uri = $smarty->getVariable('uri'); 
    $current = 0;
    foreach ($value as $key => $section) {
        foreach ($section['items'] as $id => $item) { 
            if ($item['url'] != $uri) { 
                if(!isset($value[$key]['current'])||$value[$key]['current']!=1){ 
                   $value[$key]['current'] = 0; 
                }
               $value[$key]['items'][$id]['current'] = 0;
               // break;
            }else{ 
                $value[$key]['current'] = 1;
                $value[$key]['items'][$id]['current'] = 1;
                $current = 1;
            }
        }
    } 
    if ($current == 0) {
        
    }
    
    $smarty->assign('sidebar', $value);
}