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
use bpteam\ProxyList\ProxyUpdate;

$urlSource = "http://gatherproxy.com/subscribe/login";
$nameSource = "gatherproxy.com";
$proxyGatherproxyProxy = [];
/**
 * @var Loader|\bpteam\Loader\LoaderSingleCurl $curl
 */
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setDefaultOption(CURLOPT_REFERER, 'http://gatherproxy.com/subscribe/login');
$curl->setTypeContent("html");
$curl->setDefaultOption(CURLOPT_POSTFIELDS, 'Username=zking.nothingz@gmail.com&Password=)VQd$x;7');
$answerGatherproxy = $curl->load($urlSource);
if (!preg_match('%<a\s*href="(?<url>[^"]+)">Download\s*fully\s*\d+\s*proxies</a>%ims', $answerGatherproxy, $match)) {
	return $proxyGatherproxyProxy;
}
$curl->setDefaultOption(CURLOPT_REFERER, 'http://gatherproxy.com/subscribe/infos');
$answerGatherproxy = $curl->load('http://gatherproxy.com' . $match['url']);
$ips = DryPath::getIp($answerGatherproxy);
if ($ips){
	foreach ($ips as $valueGatherproxy) {
		$proxyGatherproxyProxy[] = trim($valueGatherproxy);
	}
}
$updateProxy->saveSource($nameSource, $proxyGatherproxyProxy);
return $proxyGatherproxyProxy;