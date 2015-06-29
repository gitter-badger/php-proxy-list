<?php
/**
 * Created by JetBrains PhpStorm.
 * User: EC
 * Date: 14.05.13
 * Time: 2:36
 * Project: GetContent
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

use bpteam\Curl\SingleCurl;
use bpteam\DryText\DryPath;
use bpteam\ProxyList\ProxyUpdate;

return array();
$urlSource = "http://checkerproxy.net/all_proxy";
$nameSource = "checkerproxy.net";
$curl = new SingleCurl();
$updateProxy = new ProxyUpdate();
$answerCheckerProxy = $curl->load($urlSource);
$proxyCheckerProxy = [];
$ips = DryPath::getIp($answerCheckerProxy);
if($ips){
	foreach ($ips as $valueCheckerProxy) {
		$valueCheckerProxy = trim($valueCheckerProxy);
		if(DryPath::isIp($valueCheckerProxy)){
			$proxyCheckerProxy[] = $valueCheckerProxy;
		}
	}
}
$updateProxy->saveSource($nameSource, $proxyCheckerProxy);
return $proxyCheckerProxy;