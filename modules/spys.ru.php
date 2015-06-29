<?php
/**
 * Created by JetBrains PhpStorm.
 * User: EC
 * Date: 14.05.13
 * Time: 3:35
 * Project: GetContent
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

use bpteam\Loader\Loader;
use bpteam\DryText\DryPath;
use bpteam\DryText\DryHtml;
use bpteam\ProxyList\ProxyUpdate;

return array();
$urlSource = "http://spys.ru/aproxy/";
$nameSource = "spys.ru";
/**
 * @var Loader|\bpteam\Loader\LoaderSingleCurl $curl
 */
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setTypeContent("html");
$curl->setDefaultOption(CURLOPT_POST, true);
$curl->setDefaultOption(CURLOPT_POSTFIELDS, 'sto=%CF%EE%EA%E0%E7%E0%F2%FC+200');
$answerSpys = $curl->load($urlSource);
$answerSpys = DryHtml::betweenTag($answerSpys, '<table width="100%" BORDER=0 CELLPADDING=1 CELLSPACING=1>');
$ips = DryPath::getIp($answerSpys);
if ($ips){
	foreach ($ips as $valueSpys) {
		$proxySpysProxy[] = trim($valueSpys);
	}
}
$updateProxy->saveSource($nameSource, $proxySpysProxy);
return $proxySpysProxy;