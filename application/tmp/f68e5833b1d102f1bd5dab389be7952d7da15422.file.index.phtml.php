<?php /* Smarty version Smarty-3.0.8, created on 2015-07-10 18:54:19
         compiled from "/home/work/user/huwei/getontrip/application/views/index/index.phtml" */ ?>
<?php /*%%SmartyHeaderCode:122664917559fa45b369c22-14966968%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f68e5833b1d102f1bd5dab389be7952d7da15422' => 
    array (
      0 => '/home/work/user/huwei/getontrip/application/views/index/index.phtml',
      1 => 1436442733,
      2 => 'file',
    ),
    '49bc4e1c970aabce260ef02371a17b2128af5921' => 
    array (
      0 => '/home/work/user/huwei/getontrip/application/views/common/page.phtml',
      1 => 1436442620,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '122664917559fa45b369c22-14966968',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (@MODULE=='Index'){?>
	<?php $_template = new Smarty_Internal_Template('common/header.phtml', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php }else{ ?>
	<?php $_template = new Smarty_Internal_Template('../../../views/common/header.phtml', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php }?>
</head>
<body class="page">

    <div id="main-wraper">
        
首页

    </div>

    
<script>
    require(['home/index'], function (main) {
        main.init();
    }); 
</script>


    <?php if (@MODULE=='Index'){?>
		<?php $_template = new Smarty_Internal_Template('common/footer.phtml', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<?php }else{ ?>
		<?php $_template = new Smarty_Internal_Template('../../../views/common/footer.phtml', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<?php }?>
    