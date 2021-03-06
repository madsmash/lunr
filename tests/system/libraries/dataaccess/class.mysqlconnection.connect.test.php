<?php

/**
 * This file contains the MySQLConnectionConnectTest class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Tests
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2012, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Libraries\DataAccess;

use \ReflectionClass;

/**
 * This class contains connection related unit tests for MySQLConnection.
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Tests
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @covers     Lunr\Libraries\DataAccess\MySQLConnection
 */
class MySQLConnectionConnectTest extends MySQLConnectionTest
{

    /**
     * Test a successful readonly connection.
     *
     * @covers Lunr\Libraries\DataAccess\MySQLConnection::connect
     */
    public function testSuccessfulConnectReadonly()
    {
        $readonly = $this->db_reflection->getProperty('readonly');
        $readonly->setAccessible(TRUE);
        $readonly->setValue($this->db, TRUE);

        $ro_host = $this->db_reflection->getProperty('ro_host');
        $ro_host->setAccessible(TRUE);
        $ro_host->setValue($this->db, 'ro_host');

        $port   = ini_get('mysqli.default_port');
        $socket = ini_get('mysqli.default_socket');

        $this->mysqli->expects($this->once())
                     ->method('connect')
                     ->with('ro_host', 'username', 'password', 'database', $port, $socket);

        $this->mysqli->expects($this->once())
                     ->method('set_charset');

        $this->db->connect();

        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);

        $this->assertTrue($property->getValue($this->db));

        $property->setValue($this->db, FALSE);
    }

    /**
     * Test a successful readwrite connection.
     *
     * @covers Lunr\Libraries\DataAccess\MySQLConnection::connect
     */
    public function testSuccessfulConnectReadwrite()
    {
        $port   = ini_get('mysqli.default_port');
        $socket = ini_get('mysqli.default_socket');

        $this->mysqli->expects($this->once())
                     ->method('connect')
                     ->with('rw_host', 'username', 'password', 'database', $port, $socket);

        $this->mysqli->expects($this->once())
                     ->method('set_charset');

        $this->db->connect();

        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);

        $this->assertTrue($property->getValue($this->db));

        $property->setValue($this->db, FALSE);
    }

    /**
     * Test a failed connection attempt.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\EnvironmentTest::testMysqlndUh
     * @covers  Lunr\Libraries\DataAccess\MySQLConnection::connect
     */
    public function testFailedConnect()
    {
        $mysqli = new \mysqli();

        $class = $this->db_reflection->getProperty('mysqli');
        $class->setAccessible(TRUE);
        $class->setValue($this->db, $mysqli);

        mysqlnd_uh_set_connection_proxy(new MockMySQLndFailedConnection());

        $this->db->connect();

        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);

        $this->assertFalse($property->getValue($this->db));
    }

    /**
     * Test that connect() does not reconnect when we are already connected.
     *
     * @covers Lunr\Libraries\DataAccess\MySQLConnection::connect
     */
    public function testConnectDoesNotReconnectWhenAlreadyConnected()
    {
        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);
        $property->setValue($this->db, TRUE);

        $this->mysqli->expects($this->never())
                     ->method('connect');

        $this->db->connect();

        $this->assertTrue($property->getValue($this->db));

        $property->setValue($this->db, FALSE);
    }

    /**
     * Test that connect() fails when the driver specified is not mysql.
     *
     * @covers Lunr\Libraries\DataAccess\MySQLConnection::connect
     */
    public function testConnectFailsWhenDriverIsNotMysql()
    {
        $sub_configuration = $this->getMock('Lunr\Libraries\Core\Configuration');

        $configuration = $this->getMock('Lunr\Libraries\Core\Configuration');

        $map = array(
            array('db', $sub_configuration),
        );

        $configuration->expects($this->any())
                      ->method('offsetGet')
                      ->will($this->returnValueMap($map));

        $map = array(
            array('rw_host', 'rw_host'),
            array('username', 'username'),
            array('password', 'password'),
            array('database', 'database'),
            array('driver', 'not_mysql')
        );

        $sub_configuration->expects($this->any())
                      ->method('offsetGet')
                      ->will($this->returnValueMap($map));

        $config = $this->db_reflection->getProperty('configuration');
        $config->setAccessible(TRUE);
        $config->setValue($this->db, $configuration);

        $this->logger->expects($this->once())
                     ->method('log_error');

        $this->db->connect();
    }

    /**
     * Test that disconnect() does not try to disconnect when we are not connected yet.
     *
     * @covers Lunr\Libraries\DataAccess\MySQLConnection::disconnect
     */
    public function testDisconnectDoesNotTryToDisconnectWhenNotConnected()
    {
        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);

        $this->mysqli->expects($this->never())
                     ->method('kill');
        $this->mysqli->expects($this->never())
                     ->method('close');

        $this->db->disconnect();

        $this->assertFalse($property->getValue($this->db));
    }

    /**
     * Test that disconnect() works correctly.
     *
     * @runInSeparateProcess
     *
     * @depends Lunr\EnvironmentTest::testMysqlndUh
     * @covers  Lunr\Libraries\DataAccess\MySQLConnection::disconnect
     */
    public function testDisconnect()
    {
        $mysqli = new \mysqli();

        $class = $this->db_reflection->getProperty('mysqli');
        $class->setAccessible(TRUE);
        $class->setValue($this->db, $mysqli);

        mysqlnd_uh_set_connection_proxy(new MockMySQLndSuccessfulConnection());

        $this->db->connect();

        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);

        $this->assertTrue($property->getValue($this->db));

        $this->db->disconnect();

        $this->assertFalse($property->getValue($this->db));
    }

    /**
     * Test that change_database() returns FALSE when we couldn't connect.
     *
     * @depends Lunr\EnvironmentTest::testMysqlndUh
     * @covers  Lunr\Libraries\DataAccess\MySQLConnection::change_database
     */
    public function testChangeDatabaseReturnsFalseWhenNotConnected()
    {
        $mysqli = new \mysqli();

        $class = $this->db_reflection->getProperty('mysqli');
        $class->setAccessible(TRUE);
        $class->setValue($this->db, $mysqli);

        mysqlnd_uh_set_connection_proxy(new MockMySQLndFailedConnection());

        $this->assertFalse($this->db->change_database('new_db'));
    }

    /**
     * Test that change_database() returns FALSE when select_db() fails.
     *
     * @covers Lunr\Libraries\DataAccess\MySQLConnection::change_database
     */
    public function testChangeDatabaseReturnsFalseWhenSelectDBFailed()
    {
        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);
        $property->setValue($this->db, TRUE);

        $this->mysqli->expects($this->once())
                     ->method('select_db')
                     ->will($this->returnValue(FALSE));

        $this->assertFalse($this->db->change_database('new_db'));
        $property->setValue($this->db, FALSE);
    }

    /**
     * Test that change_database() returns TRUE when select_db() works.
     *
     * @covers Lunr\Libraries\DataAccess\MySQLConnection::change_database
     */
    public function testChangeDatabaseReturnsTrueWhenSelectDBWorked()
    {
        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);
        $property->setValue($this->db, TRUE);

        $this->mysqli->expects($this->once())
                     ->method('select_db')
                     ->will($this->returnValue(TRUE));

        $this->assertTrue($this->db->change_database('new_db'));
        $property->setValue($this->db, FALSE);
    }

}

?>
