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

use bpteam\BigList\JsonList;
use bpteam\DryText\DryPath;

class Proxy extends JsonList {
    protected $ext = 'proxy';
    protected $defaultNameList = 'all';
    protected $proxyFunction = [
        'anonym',
        'referer',
        'post',
        'get',
        'cookie',
        'country',
        'last_check',
        'starttransfer',
        'upload_speed',
        'download_speed',
        'source',
        'protocol',
    ];

    /**
     * @return string
     */
    public function getDefaultListName() {
        return $this->defaultNameList;
    }

    /**
     * @param array $proxyFunction
     */
    public function setProxyFunction($proxyFunction) {
        $this->proxyFunction = $proxyFunction;
    }

    /**
     * @return array
     */
    public function getProxyFunction() {
        return $this->proxyFunction;
    }

    function __construct($path = NULL){
        parent::__construct($path);
    }

    /**
     * Создает профиль прокси адресов
     * @param string $name      Название
     * @param string $checkUrl  Проверочный URL
     * @param array  $checkWord Проверочные регулярные выражения
     * @param array  $function  Перечень поддерживаемых функций
     * @param bool   $needUpdate
     */
    public function createList($name, $checkUrl = "http://ya.ru", $checkWord = ["#yandex#iUm"], $function = [], $needUpdate = false) {
        $this->open($name);
        $this->write($checkUrl, 'url');
        $this->write($checkWord, 'check_word');
        $this->write($function, 'function');
        $this->write($needUpdate, 'need_update');
        $this->write([], 'content');
    }

    public function getAllNameList() {
        $fileList = glob($this->path . '/*.' . $this->ext);
        $proxyLists = [];
        foreach ($fileList as $fileName) {
            if ($data = DryPath::parsePath($fileName)) {
                $proxyLists[] = $data['filename'];
            }
        }
        return $proxyLists;
    }

    public function listExist($name){
        return in_array($name, $this->getAllNameList());
    }

    /**
     * @param string $proxy
     * @param array  $properties self::_proxyFunction
     */
    public function add($proxy, $properties = array()){
        $this->write( ['proxy' => $proxy], $proxy, 'content');
        foreach($this->getProxyFunction() as $function){
            $this->write(isset($properties[$function]) ? $properties[$function] : null, $function, $proxy);
        }
    }

    public function get($key = false, $url = false){
        if(!$key && !$url) {
            $proxy = $this->getNextRecord('content');
            return is_array($proxy) ? $proxy : false;
        }
        return false;
    }

    public function shuffleProxyList(){
        return $this->shuffleList('content');
    }

    public function loadProxy($url){
        $proxyListPage = file_get_contents($url);
        $proxy = [];
        if($proxies = DryPath::getIp($proxyListPage)){
            foreach($proxies as $findProxy){
                $proxy[$findProxy] = ['proxy' => $findProxy];
            }
            $this->write($proxy, 'content');
            return $proxy;
        } else {
            return false;
        }
    }

    protected function loadList(&$proxyList, $proxySource){
        foreach(explode("\n", $proxySource) as $challenger){
            if(DryPath::isIp($challenger)){
                $proxyList['content'][$challenger]['proxy'] = $challenger;
            }
        }
    }
}