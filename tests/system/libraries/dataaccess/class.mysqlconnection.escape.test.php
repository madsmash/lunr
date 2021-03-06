<?php

/**
 * This file contains the MySQLConnectionEscapeTest class.
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

/**
 * This class contains string escape unit tests for MySQLConnection.
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Tests
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @covers     Lunr\Libraries\DataAccess\MySQLConnection
 */
class MySQLConnectionEscapeTest extends MySQLConnectionTest
{

    /**
     * Test that escape_string() returns FALSE when there is no active connection.
     *
     * @depends Lunr\EnvironmentTest::testMysqlndUh
     * @covers  Lunr\Libraries\DataAccess\MySQLConnection::escape_string
     */
    public function testEscapeStringReturnsFalseWhenNotConnected()
    {
        $mysqli = new \mysqli();

        $class = $this->db_reflection->getProperty('mysqli');
        $class->setAccessible(TRUE);
        $class->setValue($this->db, $mysqli);

        mysqlnd_uh_set_connection_proxy(new MockMySQLndFailedConnection());

        $this->assertFalse($this->db->escape_string('string'));
    }

    /**
     * Test that escape_string() properly escapes the given string.
     *
     * @param String $string       String to escape
     * @param String $part_escaped Partially escaped string (as returned by mysqli_escape_string)
     * @param String $escaped      Expected escaped string
     *
     * @dataProvider escapeStringProvider
     * @covers       Lunr\Libraries\DataAccess\MySQLConnection::escape_string
     */
    public function testEscapeString($string, $part_escaped, $escaped)
    {
        $property = $this->db_reflection->getProperty('connected');
        $property->setAccessible(TRUE);
        $property->setValue($this->db, TRUE);

        $this->mysqli->expects($this->once())
                     ->method('escape_string')
                     ->will($this->returnValue($part_escaped));

        $value = $this->db->escape_string($string);

        $this->assertEquals($escaped, $value);

        $property->setValue($this->db, FALSE);
    }

}

?>
