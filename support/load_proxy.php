<?php
/**
 * Created by PhpStorm.
 * User: EC_l
 * Date: 31.01.14
 * Time: 11:40
 * Email: bpteam22@gmail.com
 */
use bpteam\ProxyList\ProxyUpdate;

set_time_limit(3600);
$proxy = new ProxyUpdate();
$name = 'all';
$proxy->open($name);
$list = $proxy->read();
if(isset($_GET['filter'])){
	$function = array();
	foreach($proxy->getProxyFunction() as $functionName){
		if(isset($_GET[$functionName])) $function[$functionName] = $_GET[$functionName];
	}
	$proxyList = $proxy->getProxyByFunction( $list['content'], $function);
	foreach($proxyList as $ipProxy){
		$data[] = $ipProxy['proxy'];
	}
	echo implode("\n",$data);
}