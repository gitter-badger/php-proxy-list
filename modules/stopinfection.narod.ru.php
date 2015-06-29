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
use bpteam\ProxyList\ProxyUpdate;

//return array();
$urlSource = "http://stopinfection.narod.ru/Proxy.htm";
$nameSource = "stopinfection.narod.ru";
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setEncodingAnswer(true);
$curl->setEncodingName('UTF-8');
$curl->setTypeContent("html");
$answerStopinfection = $curl->load($urlSource);
$proxyStopinfectionProxy = [];

if ($answerStopinfection && $ips = DryPath::getIp($answerStopinfection)){
	foreach ($ips as $valueStopinfection) {
		$proxyStopinfectionProxy[] = trim($valueStopinfection);
	}
}
$updateProxy->saveSource($nameSource, $proxyStopinfectionProxy);
return $proxyStopinfectionProxy;