<?php /* Smarty version Smarty-3.0.8, created on 2015-07-10 18:54:19
         compiled from "/home/work/user/huwei/getontrip/application/views/common/header.phtml" */ ?>
<?php /*%%SmartyHeaderCode:562090392559fa45b3b73f4-76327015%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e47a6d1a70c1b9c4294ecdb775ca904aa9949c50' => 
    array (
      0 => '/home/work/user/huwei/getontrip/application/views/common/header.phtml',
      1 => 1436443253,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '562090392559fa45b3b73f4-76327015',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>途知</title>
    <!--[if lt IE 7]>
    <script>
        // window.location.href = '<?php echo $_smarty_tpl->getVariable('webroot')->value;?>
/static/ieup/index.html';
    </script>
    <![endif]-->
    <link rel="icon" href="<?php echo $_smarty_tpl->getVariable('webroot')->value;?>
/favicon.ico" type="image/x-icon" />

    <script src="<?php echo $_smarty_tpl->getVariable('feroot')->value;?>
/common/extra/esl.js"></script>

    <script>
        require.config({
            'baseUrl': '<?php echo $_smarty_tpl->getVariable('feroot')->value;?>
',
            'paths': {
            },
            'packages': [
                {
                    'name': 'echarts',
                    'location': '../dep/echarts/2.1.9/asset',
                    'main': 'echarts'
                },
                {
                    'name': 'est',
                    'location': '../dep/est/1.3.0/asset'
                },
                {
                    'name': 'etpl',
                    'location': '../dep/etpl/3.0.0/asset',
                    'main': 'main'
                },
                {
                    'name': 'jquery',
                    'location': '../dep/jquery/1.9.1/asset',
                    'main': 'jquery.min'
                },
                {
                    'name': 'moment',
                    'location': '../dep/moment/2.7.0/asset',
                    'main': 'moment'
                },
                {
                    'name': 'saber-emitter',
                    'location': '../dep/saber-emitter/1.0.0/asset',
                    'main': 'emitter'
                },
                {
                    'name': 'zrender',
                    'location': '../dep/zrender/2.0.6/asset',
                    'main': 'zrender'
                } 
            ]
        });

        window.GLOBAL = {
            token: '<?php echo $_smarty_tpl->getVariable('token')->value;?>
'
        };
    </script>
