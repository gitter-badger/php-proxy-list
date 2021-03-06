<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ec
 * Date: 25.09.13
 * Time: 22:25
 * Project: GetContent
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

use bpteam\Loader\Loader;
use bpteam\DryText\DryPath;
use bpteam\ProxyList\ProxyUpdate;

//return array();
$urlSource = "http://proxylist.hidemyass.com";
$nameSource = "hidemyass.com";
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setTypeContent("html");
$proxyHidemyass = array();
do {
	$answerHidemyass = $curl->load($urlSource);
	if (!$answerHidemyass) break;
	if (preg_match_all('%<tr\s*class="[^"]*"\s*rel="\d*">(?U)(?<proxyHtml>.*)</tr>%imsu', $answerHidemyass, $matchesHtml)) {
		foreach ($matchesHtml['proxyHtml'] as $proxyHtml) {
			preg_match_all('%\.(?<class>[\w_-]+){display\:\s*inline\s*}%imsu', $proxyHtml, $matchesClass);
			$needClass = implode('|', $matchesClass['class']);
			preg_match_all('%(<(span|div)\s*(style\s*=\s*"\s*display\s*\:\s*inline\s*"|class\s*=\s*"(\d+|' . $needClass . ')")\s*>\s*([^<>]+)\s*|</(span|div|style)>\s*([^"<>]+)\s*)%imsu', $proxyHtml, $matchesProxy);
			preg_match('%</td>\s*<td>\s*(?<port>\d+)\s*</td>%imsu', $proxyHtml, $matchPort);
			$proxyAddress = implode('', $matchesProxy[0]) . ':' . $matchPort['port'];
			$proxyAddress = preg_replace('%<[^<>]*>%imsu', '', $proxyAddress);
			$proxyAddress = preg_replace('%\s+%ms', '', $proxyAddress);
			$proxyAddress = trim($proxyAddress);
			if (DryPath::isIp($proxyAddress)) {
				$proxyHidemyass[] = $proxyAddress;
			}
		}
	}
	if (preg_match('%<a\s*href="(?<next>[^"]+)"\s*class="next">%imsu', $answerHidemyass, $matchNext)) {
		$urlSource = "http://proxylist.hidemyass.com" . $matchNext['next']. '#listable';
	} else {
		unset($urlSource);
	}
	sleep(rand(1, 3));
} while (isset($urlSource));
$updateProxy->saveSource($nameSource, $proxyHidemyass);
return $proxyHidemyass;
