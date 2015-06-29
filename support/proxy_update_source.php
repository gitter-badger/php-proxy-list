<?php
/**
 * Created by PhpStorm.
 * User: EC_l
 * Date: 31.01.14
 * Time: 13:02
 * Email: bpteam22@gmail.com
 */

use bpteam\Loader\Loader;
use bpteam\DryText\DryPath;
use bpteam\ProxyList\ProxyUpdate;

register_shutdown_function('sendMessage');
$start = time();
echo date('[H:i:s Y/m/d]', $start);
$proxy= new ProxyUpdate();
foreach($proxy->getAllModuleName() as $source){
	$cmd = 'php -f ' . $proxy->getModulesDir() . '/' . $source . '.php > /dev/null &';
	exec($cmd);
}
$end = time();
$text = "\n";
echo date('[H:i:s Y/m/d]', $end);
$time = round(($end-$start)/60);
echo $text = $time." m  $text";

function sendMessage(){
	//global $text;
	//mail("zking.nothingz@gmail.com", "update proxy source", $text);
}