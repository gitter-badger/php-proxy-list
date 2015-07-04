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

class ProxyUpdateTest extends PHPUnit_Framework_TestCase {

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
    protected static function getMethod($name, $className = 'bpteam\ProxyList\ProxyUpdate')
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
    protected static function getProperty($name, $className = 'bpteam\ProxyList\ProxyUpdate')
    {
        $class = new ReflectionClass($className);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    public function testSetServerIp()
    {

    }
}