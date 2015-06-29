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

$urlSource = "http://www.cool-proxy.net/proxies/http_proxy_list/page:";
$nameSource = "cool-proxy.net";
$curl = new Loader('LoaderSingleCurl');
$updateProxy = new ProxyUpdate();
$curl->setTypeContent("html");
$i = 1;
if (!$content = $curl->load($urlSource . $i . "/sort:working_average/direction:asc")) return [];
$countPage = preg_match_all('#/proxies/http_proxy_list/sort:working_average/direction:asc/page:(?<pagination>\d*)"#iUm', $content, $matches) ? max($matches['pagination']) : 0;
$proxyCoolProxy = array();
do {
	if ($countProxy = preg_match_all('#<td\s*style=\"text.align.left.\s*font.weight.bold.\"><script type="text/javascript">document\.write\(Base64\.decode\("(?<ip_base64>.*)"\)\)</script></td>\s*<td>(?<port>\d+)</td>#iUms', $content, $matches)) {
		for ($j = 0; $j < $countProxy; $j++) {
			$is_ip = base64_decode($matches['ip_base64'][$j]) . ":" . $matches['port'][$j];
			if (DryPath::isIp($is_ip)) {
				$proxyCoolProxy[] = trim($is_ip);
			}
		}
	}
	$i++;
	sleep(rand(1, 3));
	$content = $curl->load($urlSource . $i . "/sort:working_average/direction:asc");
} while ($i <= $countPage);
$updateProxy->saveSource($nameSource, $proxyCoolProxy);
return $proxyCoolProxy;
