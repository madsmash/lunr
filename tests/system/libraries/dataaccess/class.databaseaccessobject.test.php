<?php

/**
 * This file contains the DatabaseAccessObjectTest class.
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

use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * This class contains the tests for the DatabaseAccessObject class.
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Tests
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @covers     Lunr\Libraries\DataAccess\DatabaseAccessObject
 */
abstract class DatabaseAccessObjectTest extends PHPUnit_Framework_TestCase
{

    /**
     * Mock instance of the DatabaseConnectionPool
     * @var DatabaseConnectionPool
     */
    protected $pool;

    /**
     * Mock instance of a DatabaseConnection
     * @var DatabaseConnection
     */
    protected $db;

    /**
     * Instance of the DatabaseAccessObject
     * @var DatabaseAccessObject
     */
    protected $dao;

    /**
     * Reflection instance of the DatabaseAccessObject
     * @var ReflectionClass
     */
    protected $reflection_dao;

    /**
     * Testcase Constructor.
     *
     * @return void
     */
    public function setUpNoPool()
    {
        $this->pool = NULL;

        $this->db = $this->getMockBuilder('Lunr\Libraries\DataAccess\MySQLConnection')
                         ->disableOriginalConstructor()
                         ->getMock();

        $this->dao = $this->getMockBuilder('Lunr\Libraries\DataAccess\DatabaseAccessObject')
                          ->setConstructorArgs(array(&$this->db))
                          ->getMockForAbstractClass();

        $this->reflection_dao = new ReflectionClass('Lunr\Libraries\DataAccess\DatabaseAccessObject');
    }

    /**
     * Testcase Constructor.
     *
     * @return void
     */
    public function setUpPool()
    {
        $this->pool = $this->getMockBuilder('Lunr\Libraries\DataAccess\DatabaseConnectionPool')
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->db = $this->getMockBuilder('Lunr\Libraries\DataAccess\MySQLConnection')
                         ->disableOriginalConstructor()
                         ->getMock();

        $this->dao = $this->getMockBuilder('Lunr\Libraries\DataAccess\DatabaseAccessObject')
                          ->setConstructorArgs(array(&$this->db, &$this->pool))
                          ->getMockForAbstractClass();

        $this->reflection_dao = new ReflectionClass('Lunr\Libraries\DataAccess\DatabaseAccessObject');
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown()
    {
        unset($this->pool);
        unset($this->db);
        unset($this->dao);
        unset($this->reflection_dao);
    }

}

?>
