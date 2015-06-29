<?php
/**
 * Created by JetBrains PhpStorm.
 * User: EC
 * Date: 08.05.13
 * Time: 5:33
 * Project: GetContent
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

use bpteam\Loader\Loader;
use bpteam\DryText\DryPath;
use bpteam\ProxyList\ProxyUpdate;

return array();
$urlSource = "http://www.freeproxylists.net/ru/";
$nameSource = "freeproxylists.net";
/**
 * @var Loader|bpteam\Loader\LoaderSingleCurl $curl
 */
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$proxyArray = [];
do {
	$answer = $curl->load($urlSource);
	var_dump($urlSource,$answer);
	$curl->setDefaultOption(CURLOPT_REFERER, $urlSource);
	$answer = $curl->load('http://www.freeproxylists.net/php/h.php');
	var_dump($urlSource,$answer);
	exit;
	if (!$answer) break;
	if (preg_match_all('%IPDecode\("(?<encoded_ip>[^"]+)"\)</script>\s*</td>\s*<td\s*align="center">\s*(?<port>\d+)\s*</td>%imsu', $answer, $matchesHtml)) {
		foreach ($matchesHtml['encoded_ip'] as $key => $proxy) {
			$proxy = trim(urldecode($proxy));
			$proxyAddress = $proxy . ':' . $matchesHtml['port'][$key];
			if (DryPath::isIp($proxyAddress)) {
				$proxyArray[] = $proxyAddress;
			}
		}
	}
	if (preg_match('%<a\s*href="\./\?page=(?<next>\d+)">Следующая%imsu', $answer, $matchNext)) {
		$urlSource = "http://www.freeproxylists.net/ru/?page=" . $matchNext['next'];
	} else {
		unset($urlSource);
	}
	sleep(rand(1, 3));
	if(!isset($urlSource)){
		break;
	}
} while (true);
$updateProxy->saveSource($nameSource, $proxyArray);
return $proxyArray;