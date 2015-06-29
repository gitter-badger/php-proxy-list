<?php
/**
 * Created by JetBrains PhpStorm.
 * User: EC
 * Date: 14.05.13
 * Time: 2:36
 * Project: GetContent
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

use bpteam\Loader\Loader;
use bpteam\DryText\DryPath;
use bpteam\DryText\DryHtml;
use bpteam\ProxyList\ProxyUpdate;

$urlSource = "http://www.poststar.ru/proxy.htm";
$nameSource = "poststar.ru";
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setTypeContent("html");
$answerPoststar = $curl->load($urlSource);
$answerPoststar = DryHtml::betweenTag($answerPoststar, '<table width="730" border="0" align="center">');
$proxyPoststarProxy = [];
$ips = DryPath::getIp($answerPoststar);
if($ips){
	foreach ($ips as $valuePoststar) {
		$valuePoststar = trim($valuePoststar);
		if(DryPath::isIp($valuePoststar)){
			$proxyPoststarProxy[] = $valuePoststar;
		}
	}
}
$updateProxy->saveSource($nameSource, $proxyPoststarProxy);
return $proxyPoststarProxy;