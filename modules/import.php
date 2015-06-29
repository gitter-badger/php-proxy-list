<?php
/**
 * Created by JetBrains PhpStorm.
 * User: EC
 * Date: 14.05.13
 * Time: 3:35
 * Project: GetContent
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

use bpteam\DryText\DryPath;
use bpteam\ProxyList\ProxyUpdate;


$urlSource = __DIR__ . "/../proxy_list/source/import.page";
$nameSource = "import";

$updateProxy = new ProxyUpdate();
$fh = fopen($urlSource, 'r');
while($answer = fgets($fh)){
    $ips = DryPath::getIp($answer);
	if ($ips){
		foreach ($ips as $value) {
			$value = trim($value);
			if(DryPath::isIp($value)){
				$proxy[] = $value;
			}
		}
	}
}
$updateProxy->saveSource($nameSource, $proxy);
return $proxy;