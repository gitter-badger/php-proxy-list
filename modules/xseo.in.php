<?php
/**
 * Created by JetBrains PhpStorm.
 * User: EC
 * Date: 14.05.13
 * Time: 3:26
 * Project: GetContent
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

use bpteam\Loader\Loader;
use bpteam\DryText\DryPath;
use bpteam\DryText\DryHtml;
use bpteam\ProxyList\ProxyUpdate;

$urlSource="http://xseo.in/freeproxy";
$nameSource="xseo.in";
/**
 * @var Loader|\bpteam\Loader\LoaderSingleCurl $curl
 */
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setTypeContent("html");
$curl->setDefaultOption(CURLOPT_POST,true);
$curl->setDefaultOption(CURLOPT_POSTFIELDS,'submit=%CF%EE%EA%E0%E7%E0%F2%FC+%EF%EE+100+%EF%F0%EE%EA%F1%E8+%ED%E0+%F1%F2%F0%E0%ED%E8%F6%E5');
$answerXseo = $curl->load($urlSource);
$proxyXseoProxy=array();
$answerXseo = DryHtml::betweenTag($answerXseo,'<table width="100%" BORDER=0 CELLPADDING=0 CELLSPACING=1>',false);
$ips = DryPath::getIp($answerXseo);
if($ips){
	foreach ($ips as $value_xseo){
		$proxyXseoProxy[] = trim($value_xseo);
	}
}
$updateProxy->saveSource($nameSource, $proxyXseoProxy);
return $proxyXseoProxy;