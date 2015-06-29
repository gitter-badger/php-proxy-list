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

$urlSource = "http://www.cool-tests.com/all-working-proxies.php";
$nameSource = "cool-tests.com";
/**
 * @var Loader|bpteam\Loader\LoaderSingleCurl $curl
 */
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setEncodingAnswer(true);
$curl->setEncodingName('UTF-8');
$curl->load('http://www.cool-tests.com');
$curl->setTypeContent("html");
$curl->setDefaultOption(CURLOPT_REFERER, 'http://www.cool-tests.com');
$answerCoolTests = $curl->load($urlSource);
$proxyCoolTestsProxy = [];
$ips = DryPath::getIp($answerCoolTests);
if (!$ips) return [];
foreach ($ips as $valueCoolTests) {
	$proxyCoolTestsProxy[] = trim($valueCoolTests);
}
$updateProxy->saveSource($nameSource, $proxyCoolTestsProxy);
return $proxyCoolTestsProxy;