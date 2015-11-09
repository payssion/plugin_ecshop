<?php
if (!defined('IN_ECS'))
{
	die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/payssion.php';

if (file_exists($payment_lang))
{
	global $_LANG;

	include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
	$i = isset($modules) ? count($modules) : 0;

	/* 代码 */
	$code = basename(__FILE__, '.php');
	$modules[$i]['code']    = $code;

	/* 是否支持货到付款 */
	$modules[$i]['is_cod']  = '0';
	if (strlen($code) > strlen('payssion')) {
		/* 是否支持在线支付 */
		$modules[$i]['is_online']  = '1';
	} else {
		/* 是否支持在线支付 */
		$modules[$i]['is_online']  = '0';
	}

	/* 描述对应的语言项 */
	$modules[$i]['desc']    = $code . '_desc';

	/* 作者 */
	$modules[$i]['author']  = 'Payssion Limited';

	/* 网址 */
	$modules[$i]['website'] = 'http://www.payssion.com';

	/* 版本号 */
	$modules[$i]['version'] = '1.0.0';

	return;
}

require_once(realpath(dirname(__FILE__)) . "/payssion/payssion.php");
class payssion7­elevenmy extends payssion {
	protected $pm_id = '7­eleven_my';
	protected $title = '7­eleven Malaysia';
}

?>