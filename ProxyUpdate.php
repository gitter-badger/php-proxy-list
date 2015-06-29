<?php
/**
 * Created by PhpStorm.
 * User: ec
 * Date: 29.06.15
 * Time: 20:17
 * Project: php-proxy-list
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

namespace bpteam\ProxyList;

use bpteam\DryText\DryPath;
use bpteam\Loader\Loader;

class ProxyUpdate extends Proxy {

    protected $serverIp;
    protected $functionUrl;
    protected $modulesDir;
    protected $sourceDir;
    protected $sourceExt = 'source';
    protected $urlCheckServerIp;
    protected $archiveProxy = 'archive';
    /**
     * @var Loader|\bpteam\Loader\LoaderMultiCurl
     */
    protected $loader;

    /**
     * @param string $functionUrl
     */
    public function setFunctionUrl($functionUrl) {
        $this->functionUrl = $functionUrl;
    }

    /**
     * @return string
     */
    public function getFunctionUrl() {
        return $this->functionUrl;
    }

    /**
     * @param string $dir
     */
    public function setModulesDir($dir) {
        $this->modulesDir = $dir;
    }

    /**
     * @return string
     */
    public function getModulesDir() {
        return $this->modulesDir;
    }

    public function getAllModuleName(){
        $name = array();
        foreach (glob($this->getModulesDir() . '/*.php') as $fileModule) {
            $name[] = basename($fileModule, '.php');
        }
        return $name;
    }

    /**
     * @param string $urlCheckServerIp
     */
    public function setUrlCheckServerIp($urlCheckServerIp) {
        $this->urlCheckServerIp = $urlCheckServerIp;
    }

    /**
     * @return string
     */
    public function getUrlCheckServerIp() {
        return $this->urlCheckServerIp;
    }

    /**
     * @param string $sourceDir
     */
    public function setSourceDir($sourceDir) {
        $this->sourceDir = $sourceDir;
    }

    /**
     * @return string
     */
    public function getSourceDir() {
        return $this->sourceDir;
    }

    public function getFileNameSourceList($name){
        return $this->getSourceDir() . '/' . $name . '.' . $this->sourceExt;
    }


    function __construct($checkUrl = 'http://test1.ru/proxy_check.php', $port = 80, $serverIp = false, $urlCheckServerIp = 'http://bpteam.net/server_ip.php'){
        parent::__construct();
        $this->loader = new Loader('LoaderMultiCurl');
        $this->setUrlCheckServerIp($urlCheckServerIp);
        $this->loader->setTypeContent('file');
        $this->loader->setSleepTime(500000);
        $this->loader->setDefaultOption(CURLOPT_PORT, $port);
        $this->loader->setDefaultOption(CURLOPT_TIMEOUT, 15);
        $this->setModulesDir(__DIR__ . '/modules');
        $this->setSourceDir(__DIR__ . '/proxy_list/source');
        $this->setFunctionUrl($checkUrl);
        $this->setServerIp($serverIp);
    }


    /**
     * @param string     $proxy
     * @param string     $answer
     * @param null|array $curlInfo
     * @return array|bool
     */
    protected function genInfo($proxy, $answer, $curlInfo = null) {
        if (preg_match('%^[01]{5}%', $answer) && preg_match_all('%(?<fun_status>[01])%', $answer, $matches)) {
            $infoProxy['proxy'] = $proxy['proxy'];
            $infoProxy['source'] = isset($proxy['source'])?$proxy['source']:null;
            $infoProxy['protocol'] = isset($proxy['protocol'])?$proxy['protocol']:null;
            $infoProxy['anonym'] = (bool)$matches['fun_status'][0];
            $infoProxy['referer'] = (bool)$matches['fun_status'][1];
            $infoProxy['post'] = (bool)$matches['fun_status'][2];
            $infoProxy['get'] = (bool)$matches['fun_status'][3];
            $infoProxy['cookie'] = (bool)$matches['fun_status'][4];
            $infoProxy['last_check'] = time();
            preg_match('%(?<ip>\d+\.\d+\.\d+\.\d+)\:\d+%ims', $infoProxy['proxy'], $match);
            $countryName = isset($match['ip']) && function_exists('geoip_country_name_by_name') ? geoip_country_name_by_name($match['ip']) : NULL;
            $infoProxy['country'] = $countryName ? $countryName : 'no country';
            $infoProxy['starttransfer'] = isset($curlInfo['starttransfer_time']) ? $curlInfo['starttransfer_time'] : NULL;
            $infoProxy['upload_speed'] = isset($curlInfo['speed_upload']) ? $curlInfo['speed_upload'] : NULL;
            $infoProxy['download_speed'] = isset($curlInfo['speed_download']) ? $curlInfo['speed_download'] : NULL;
            return $infoProxy;
        } else {
            return [];
        }
    }

    public function updateAllList() {
        foreach ($this->getAllNameList() as $value) {
            if ($value == $this->getDefaultListName()) {
                continue;
            }
            $this->updateList($value);
        }
    }

    public function updateDefaultList($countStream = 1000000) {
        $this->open($this->getDefaultListName());
        $proxyList = $this->downloadArchiveProxy();
        $proxyList['content'] = $this->checkProxyArray($proxyList['content'], $countStream);
        if($proxyList['content']) {
            $this->write($proxyList['content'], 'content');
        }
    }

    public function updateList($nameList) {
        $this->open($this->getDefaultListName());
        $allProxy = $this->read();
        $this->open($nameList);
        $proxyList = $this->read();
        $proxyList['content'] = $this->getProxyByFunction($allProxy['content'], $proxyList['function']);
        $proxyList['content'] = $this->checkProxyArrayToSite($proxyList['content'], $proxyList['url'], $proxyList['check_word']);
        $this->write($proxyList['content'], 'content');
    }

    public function downloadAllProxy() {
        $proxy['content'] = array();
        foreach (glob($this->getSourceDir() . '/*.' . $this->sourceExt) as $fileSource) {
            $this->loadList($proxy, file_get_contents($fileSource));
        }
        return $proxy;
    }

    public function updateArchive(){
        $data = $this->downloadAllProxy();
        $proxy = array_keys($data['content']);
        $this->saveSource($this->archiveProxy, $proxy);
    }

    public function downloadArchiveProxy(){
        $proxy['content'] = array();
        $this->loadList($proxy, file_get_contents($this->getFileNameSourceList($this->archiveProxy)));
        return $proxy;
    }

    public function saveSource($name, $proxy){
        return file_put_contents($this->getSourceDir() . '/' . $name . '.' . $this->sourceExt, implode("\n", $proxy));
    }

    public function getProxyByFunction($proxyList, $function = array()) {
        if (!is_array($proxyList)){
            return false;
        }
        $goodProxy = array();
        foreach ($proxyList as $challenger) {
            if($this->checkProxyFunctions($challenger, $function)){
                $goodProxy[] = $challenger;
            }
        }
        if (count($goodProxy)) return $goodProxy;
        return false;
    }

    /**
     * @param $proxyFunctions
     * @param array $needFunctions list of functions:
     *                             anonym=(true|false)
     *                             referer=(true|false)
     *                             post=(true|false)
     *                             get=(true|false)
     *                             cookie=(true|false)
     *                             starttransfer= < float
     *                             country= name of country
     *                             last_check= > int
     *                             upload_speed= > float
     *                             download_speed= > float
     * @return bool
     */
    protected function checkProxyFunctions($proxyFunctions, $needFunctions){
        foreach($needFunctions as $name => $value){
            switch(true){
                case in_array( $name, array('anonym','referer','post','get','cookie')):
                    if($proxyFunctions[$name] != $value){
                        return false;
                    }
                    continue;
                case in_array($name, array('starttransfer')):
                    if($proxyFunctions[$name] > $value){
                        return false;
                    }
                    continue;
                case in_array( $name, array('country')):
                    if($value){
                        if((is_array($value) && !in_array( $proxyFunctions[$name], $value))
                            || (is_string($value) && $proxyFunctions[$name] != $value)){
                            return false;
                        }
                    }
                    continue;
                case in_array( $name, array('last_check', 'upload_speed', 'download_speed')):
                    if($proxyFunctions[$name] < $value){
                        return false;
                    }
                    continue;
                /*case in_array( $name, array('source', 'protocol')):
                    if(!array_key_exists( $value, $proxyFunctions[$name])){
                        return false;
                    }
                    continue;*/
            }
        }
        return true;
    }

    protected function checkProxyArray($arrayProxy, $chunk = 150) {
        if (is_array($arrayProxy)) {
            $goodProxy = array();
            $url = $this->getFunctionUrl() . '?ip=' . $this->getServerIp() . '&proxy=yandex';
            $this->loader->setCountStream(1);
            $this->loader->setMinSizeAnswer(5);
            $this->loader->setDefaultOption(CURLOPT_TIMEOUT, 30);
            $this->loader->setMaxRepeat(0);
            $this->loader->setDefaultOption(CURLOPT_REFERER, "proxy-check.net");
            $this->loader->setDefaultOption(CURLOPT_POST, true);
            $this->loader->setDefaultOption(CURLOPT_POSTFIELDS, "proxy=yandex");
            foreach (array_chunk($arrayProxy, $chunk) as $challenger) {
                $this->loader->setCountCurl(count($challenger));
                $urlList = array();
                $descriptorArray =& $this->_curl->getDescriptorArray();
                foreach ($descriptorArray as $key => &$descriptor) {
                    $this->loader->setOption($descriptor, CURLOPT_PROXY, $challenger[$key]['proxy']);
                    $urlList[] = $url;
                }
                foreach ($this->loader->load($urlList) as $key => $answer) {
                    $infoProxy = $this->genInfo($challenger[$key], $answer, $descriptorArray[$key]['info']);
                    if ($infoProxy) {
                        $goodProxy[] = $infoProxy;
                    }
                }
                $this->loader->genNewKeyStream();
            }
            if (count($goodProxy)) {
                return $goodProxy;
            }
        }
        return array();
    }

    protected function checkProxyArrayToSite($arrayProxy, $url, $checkWord, $chunk = 100) {
        if (!is_array($arrayProxy)) return array();
        $goodProxy = array();
        $this->loader->setCountStream(1);
        $this->loader->setTypeContent('text');
        $this->loader->setDefaultOption(CURLOPT_POST, false);
        $this->loader->setDefaultOption(CURLOPT_TIMEOUT, 30);
        $this->loader->setCheckAnswer(false);
        foreach (array_chunk($arrayProxy, $chunk) as $challenger) {
            $this->loader->setCountCurl(count($challenger));
            $descriptorArray =& $this->loader->getDescriptorArray();
            $urlList = array();
            foreach ($descriptorArray as $key => &$descriptor) {
                $this->loader->setOption($descriptor, CURLOPT_PROXY, $challenger[$key]['proxy']);
                $urlList[] = $url;
            }
            foreach ($this->loader->load($urlList) as $key => $answer) {
                $testCount = 0;
                $countGood = 0;
                foreach ($checkWord as $valueCheckWord) {
                    $testCount++;
                    if (preg_match($valueCheckWord, $answer)){
                        $countGood++;
                    }
                }
                if ($countGood == $testCount) {
                    $goodProxy[] = $challenger[$key];
                }
            }
        }
        return count($goodProxy) ? $goodProxy : array();
    }

    /**
     * @return string
     */
    public function getServerIp() {
        return $this->serverIp;
    }

    /**
     * Переделать или делать запрос на другой сервис
     * @param $ip
     * @return void
     */
    public function setServerIp($ip) {
        if (!$ip) {
            $answer = file_get_contents($this->getUrlCheckServerIp());
            $ip = DryPath::getIp($answer);
            if (!$ip[0]) exit('NO SERVER IP');
            $this->setServerIp($ip[0]);
        } else {
            $this->serverIp = $ip;
        }
    }

    public function setUpdateList($value, $name = false){
        if($name){
            $this->open($name);
        }
        $this->write($value, 'need_update');
    }
}