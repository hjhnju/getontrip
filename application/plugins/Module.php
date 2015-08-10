<?php
/**
 * 模块插件
 * @author jiangsongfang
 *
 */
class ModulePlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //对模块的url进行处理
        if ($request->getModuleName() != MODULE) {
            $request->setModuleName(MODULE);
            $request->setControllerName(ucfirst($request->getActionName()));
            $request->setActionName('index');
        }
        
        if (strtolower($request->getControllerName()) == 'api') {
            $key = key($request->getParams());
            $request->setModuleName(ucfirst($request->getActionName()));
            $request->setControllerName('Api');
            $request->setActionName('Index');
            if(!empty($key)){
                $request->setActionName(ucfirst($key));
            }
        }
    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }
}