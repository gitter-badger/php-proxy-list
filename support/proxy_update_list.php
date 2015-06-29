<?php
/**
 * Created by PhpStorm.
 * User: EC_l
 * Date: 31.01.14
 * Time: 13:02
 * Email: bpteam22@gmail.com
 */

use bpteam\ProxyList\ProxyUpdate;

register_shutdown_function('sendMessage');
$start = time();
echo date('[H:i:s Y/m/d]', $start);
$proxy= new ProxyUpdate();
$proxy->updateAllList();
$end = time();
$text = "\n";
$proxy->open($proxy->getDefaultListName());
$list = $proxy->read();
$subject = count($list['content']);
foreach($proxy->getAllNameList() as $nameList){
	$proxy->open($nameList);
	$list = $proxy->read();
	$text .= "$nameList " . count($list['content']) . "\n";
}
echo date('[H:i:s Y/m/d]', $end);
$time = round(($end-$start)/60);
echo $text = $time." m  $text";
function sendMessage(){
	global $text;
	mail("zking.nothingz@gmail.com", "update proxy", $text);
}