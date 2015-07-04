<?php
/**
 * Created by PhpStorm.
 * User: ec
 * Date: 29.06.15
 * Time: 20:19
 * Project: php-proxy-list
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

namespace bpteam\ProxyList;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;

class ProxyTest extends PHPUnit_Framework_TestCase
{

    public static $name;

    public static function setUpBeforeClass()
    {
        self::$name = 'unit_test';
    }

    /**
     * @param        $name
     * @param string $className
     * @return \ReflectionMethod
     */
    protected static function getMethod($name, $className = 'bpteam\ProxyList\Proxy')
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @param        $name
     * @param string $className
     * @return \ReflectionProperty
     */
    protected static function getProperty($name, $className = 'bpteam\ProxyList\Proxy')
    {
        $class = new ReflectionClass($className);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }
    
    public function testCreateList()
    {
        $listName = 'bpteam';
        $proxy = new Proxy();
        $proxy->createList($listName);
        $this->assertTrue($proxy->listExist($listName));
        $proxy->deleteList();
    }

    public function testOpen()
    {
        $listName = 'bpteam';
        $checkUrl = 'http://bpteam.net';
        $checkWord = ['%bpteam22\@gmail\.com%ims', '%380632359213%ms'];
        $function = [];
        $needUpdate = true;
        $proxy = new Proxy();
        $proxy->createList($listName, $checkUrl, $checkWord, $function, $needUpdate);
        $proxy->close();
        unset($proxy);
        $proxy = new Proxy();
        $proxy->open($listName);
        $list = $proxy->read();
        $this->assertTrue(is_array($list));
        $this->assertArrayHasKey('url', $list);
        $this->assertEquals('http://bpteam.net', $list['url']);
        $this->assertArrayHasKey('check_word', $list);
        $this->assertTrue(is_array($list['check_word']));
    }

    public function testGet()
    {
        $listName = 'bpteam';
        $proxyIp = '127.0.0.1:8080';
        $properties = [
            'anonym' => false,
            'referer' => true,
            'post' => true,
            'get' => true,
            'cookie' => false,
            'country' => 'China',
            'last_check' => 13255444887,
            'starttransfer' => 21,
            'upload_speed' => 1,
            'download_speed' => 2,
            'source' => ['proxy.net'],
            'protocol' => ['http'],
        ];
        $proxy = new Proxy();
        $proxy->open($listName);
        $proxy->add($proxyIp, $properties);
        $getProxy = $proxy->get();

        $this->assertTrue(is_array($getProxy));
        $this->assertArrayHasKey('proxy', $getProxy);
        $this->assertEquals($proxyIp, $getProxy['proxy']);
    }
}