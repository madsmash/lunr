<?php

/**
 * This file contains the RequestStoreTest class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Tests
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */

namespace Lunr\Libraries\Core;
use \ReflectionClass;

/**
 * Tests for storing superglobal values.
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Tests
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @covers     Lunr\Libraries\Core\Request
 */
class RequestStoreTest extends RequestTest
{

    /**
     * TestCase Constructor.
     */
    public function setUp()
    {
        $this->setUpEmpty();
    }

    /**
     * Test storing invalid $_COOKIE values.
     *
     * @param mixed $cookie Invalid $_COOKIE values
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_cookie_empty
     * @dataProvider invalidSuperglobalValueProvider
     * @covers       Lunr\Libraries\Core\Request::store_cookie
     */
    public function test_store_invalid_cookie_values($cookie)
    {
        $stored = $this->reflection_request->getProperty('cookie');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_cookie');
        $method->setAccessible(TRUE);

        $_COOKIE = $cookie;

        $method->invoke($this->request);

        $this->assertEmpty($stored->getValue($this->request));
    }

    /**
     * Test storing valid $_COOKIE values.
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_cookie_empty
     * @covers       Lunr\Libraries\Core\Request::store_cookie
     */
    public function test_store_valid_cookie_values()
    {
        $stored = $this->reflection_request->getProperty('cookie');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_cookie');
        $method->setAccessible(TRUE);

        $_COOKIE['test1'] = 'value1';
        $_COOKIE['test2'] = 'value2';
        $cache = $_COOKIE;

        $method->invoke($this->request);

        $this->assertEquals($cache, $stored->getValue($this->request));
    }

    /**
     * Test that $_COOKIE is empty after storing.
     *
     * @depends Lunr\Libraries\Core\RequestBaseTest::test_cookie_empty
     * @covers  Lunr\Libraries\Core\Request::store_cookie
     */
    public function test_superglobal_cookie_empty_after_store()
    {
        $stored = $this->reflection_request->getProperty('cookie');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_cookie');
        $method->setAccessible(TRUE);

        $_COOKIE['test1'] = 'value1';
        $_COOKIE['test2'] = 'value2';

        $method->invoke($this->request);

        $this->assertEmpty($_COOKIE);
    }

    /**
     * Test storing invalid $_POST values.
     *
     * @param mixed $post Invalid $_POST values
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_post_empty
     * @dataProvider invalidSuperglobalValueProvider
     * @covers       Lunr\Libraries\Core\Request::store_post
     */
    public function test_store_invalid_post_values($post)
    {
        $stored = $this->reflection_request->getProperty('post');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_post');
        $method->setAccessible(TRUE);

        $_POST = $post;

        $method->invoke($this->request);

        $this->assertEmpty($stored->getValue($this->request));
    }

    /**
     * Test storing valid $_POST values.
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_post_empty
     * @covers       Lunr\Libraries\Core\Request::store_post
     */
    public function test_store_valid_post_values()
    {
        $stored = $this->reflection_request->getProperty('post');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_post');
        $method->setAccessible(TRUE);

        $_POST['test1'] = 'value1';
        $_POST['test2'] = 'value2';
        $cache = $_POST;

        $method->invoke($this->request);

        $this->assertEquals($cache, $stored->getValue($this->request));
    }

    /**
     * Test that $_POST is empty after storing.
     *
     * @depends Lunr\Libraries\Core\RequestBaseTest::test_post_empty
     * @covers  Lunr\Libraries\Core\Request::store_post
     */
    public function test_superglobal_post_empty_after_store()
    {
        $stored = $this->reflection_request->getProperty('post');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_post');
        $method->setAccessible(TRUE);

        $_POST['test1'] = 'value1';
        $_POST['test2'] = 'value2';

        $method->invoke($this->request);

        $this->assertEmpty($_POST);
    }

    /**
     * Test that the base_path is constructed and stored correctly.
     *
     * @runInSeparateProcess
     * @covers Lunr\Libraries\Core\Request::store_url
     */
    public function test_store_base_path()
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);

        $this->set_request_sapi_non_cli($stored);

        $method = $this->reflection_request->getMethod('store_url');
        $method->setAccessible(TRUE);

        $_SERVER = $this->setup_server_superglobal();

        $method->invokeArgs($this->request, array(&$this->configuration));

        $request = $stored->getValue($this->request);

        $this->assertEquals('/path/to/', $request['base_path']);
    }

    /**
     * Test that the domain is stored correctly.
     *
     * @runInSeparateProcess
     * @covers Lunr\Libraries\Core\Request::store_url
     */
    public function test_store_domain()
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);

        $this->set_request_sapi_non_cli($stored);

        $method = $this->reflection_request->getMethod('store_url');
        $method->setAccessible(TRUE);

        $_SERVER = $this->setup_server_superglobal();

        $method->invokeArgs($this->request, array(&$this->configuration));

        $request = $stored->getValue($this->request);

        $this->assertEquals('www.domain.com', $request['domain']);
    }

    /**
     * Test that the port is stored correctly.
     *
     * @runInSeparateProcess
     * @covers Lunr\Libraries\Core\Request::store_url
     */
    public function test_store_port()
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);

        $this->set_request_sapi_non_cli($stored);

        $method = $this->reflection_request->getMethod('store_url');
        $method->setAccessible(TRUE);

        $_SERVER = $this->setup_server_superglobal();

        $method->invokeArgs($this->request, array(&$this->configuration));

        $request = $stored->getValue($this->request);

        $this->assertEquals('443', $request['port']);
    }

    /**
     * Test that the protocol is constructed and stored correctly.
     *
     * @runInSeparateProcess
     * @covers Lunr\Libraries\Core\Request::store_url
     */
    public function test_store_port_if_https_unset()
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);

        $this->set_request_sapi_non_cli($stored);

        $method = $this->reflection_request->getMethod('store_url');
        $method->setAccessible(TRUE);

        $_SERVER = $this->setup_server_superglobal();
        unset($_SERVER['HTTPS']);

        $method->invokeArgs($this->request, array(&$this->configuration));

        $request = $stored->getValue($this->request);

        $this->assertEquals('http', $request['protocol']);
    }

    /**
     * Test that the protocol is constructed and stored correctly.
     *
     * @param String $value    HTTPS value
     * @param String $protocol Protocol according to the HTTPS value
     *
     * @runInSeparateProcess
     *
     * @dataProvider httpsServerSuperglobalValueProvider
     * @covers Lunr\Libraries\Core\Request::store_url
     */
    public function test_store_port_if_https_isset($value, $protocol)
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);

        $this->set_request_sapi_non_cli($stored);

        $method = $this->reflection_request->getMethod('store_url');
        $method->setAccessible(TRUE);

        $_SERVER = $this->setup_server_superglobal();
        $_SERVER['HTTPS'] = $value;

        $method->invokeArgs($this->request, array(&$this->configuration));

        $request = $stored->getValue($this->request);

        $this->assertEquals($protocol, $request['protocol']);
    }

    /**
     * Test that the protocol is constructed and stored correctly.
     *
     * @param String $https HTTPS value
     * @param String $port  Port for the webserver
     * @param String $value The expected base_url value
     *
     * @runInSeparateProcess
     *
     * @dataProvider baseurlProvider
     * @covers Lunr\Libraries\Core\Request::store_url
     */
    public function test_store_base_url($https, $port, $value)
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);

        $this->set_request_sapi_non_cli($stored);

        $method = $this->reflection_request->getMethod('store_url');
        $method->setAccessible(TRUE);

        $_SERVER = $this->setup_server_superglobal();
        $_SERVER['HTTPS'] = $https;
        $_SERVER['SERVER_PORT'] = $port;

        $method->invokeArgs($this->request, array(&$this->configuration));

        $request = $stored->getValue($this->request);

        $this->assertEquals($value, $request['base_url']);
    }

    /**
     * Test storing invalid $_GET values.
     *
     * @param mixed $get Invalid $_GET values
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_get_empty
     * @dataProvider invalidSuperglobalValueProvider
     * @covers       Lunr\Libraries\Core\Request::store_get
     */
    public function test_store_invalid_get_values($get)
    {
        $stored = $this->reflection_request->getProperty('get');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_get');
        $method->setAccessible(TRUE);

        $_GET = $get;

        $method->invoke($this->request);

        $this->assertEmpty($stored->getValue($this->request));
    }

    /**
     * Test storing valid $_GET values.
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_get_empty
     * @covers       Lunr\Libraries\Core\Request::store_get
     */
    public function test_store_valid_get_values()
    {
        $stored = $this->reflection_request->getProperty('get');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_get');
        $method->setAccessible(TRUE);

        $_GET['test1'] = 'value1';
        $_GET['test2'] = 'value2';
        $cache = $_GET;

        $method->invoke($this->request);

        $this->assertEquals($cache, $stored->getValue($this->request));
    }

    /**
     * Test storing special $_GET values.
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_get_empty
     * @covers       Lunr\Libraries\Core\Request::store_get
     */
    public function test_store_special_get_values()
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);
        $stored->setValue($this->request, array());

        $method = $this->reflection_request->getMethod('store_get');
        $method->setAccessible(TRUE);

        $_GET['controller'] = 'controller';
        $_GET['method'] = 'method';
        $_GET['param1'] = 'param1';
        $_GET['param2'] = 'param2';
        $cache = $_GET;

        $method->invoke($this->request);

        $request = $stored->getValue($this->request);
        $this->assertEquals($cache['controller'], $request['controller']);
        $this->assertEquals($cache['method'], $request['method']);
        $this->assertEquals($cache['param1'], $request['params'][0]);
        $this->assertEquals($cache['param2'], $request['params'][1]);
    }

    /**
     * Test storing special $_GET values, if they are not present.
     *
     * @depends      Lunr\Libraries\Core\RequestBaseTest::test_get_empty
     * @covers       Lunr\Libraries\Core\Request::store_get
     */
    public function test_store_special_get_values_if_not_set()
    {
        $stored = $this->reflection_request->getProperty('request');
        $stored->setAccessible(TRUE);
        $stored->setValue($this->request, array());

        $method = $this->reflection_request->getMethod('store_get');
        $method->setAccessible(TRUE);

        $_GET['test1'] = 'value1';
        $_GET['test2'] = 'value2';
        $cache = $_GET;

        $method->invoke($this->request);

        $request = $stored->getValue($this->request);
        $this->assertNull($request['controller']);
        $this->assertNull($request['method']);
        $this->assertInternalType('array', $request['params']);
        $this->assertEmpty($request['params']);
    }

    /**
     * Test that $_GET is empty after storing.
     *
     * @depends Lunr\Libraries\Core\RequestBaseTest::test_get_empty
     * @covers  Lunr\Libraries\Core\Request::store_get
     */
    public function test_superglobal_get_empty_after_store()
    {
        $stored = $this->reflection_request->getProperty('get');
        $stored->setAccessible(TRUE);

        $method = $this->reflection_request->getMethod('store_get');
        $method->setAccessible(TRUE);

        $_GET['test1'] = 'value1';
        $_GET['test2'] = 'value2';

        $method->invoke($this->request);

        $this->assertEmpty($_GET);
    }


}

?>