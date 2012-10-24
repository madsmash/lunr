<?php

/**
 * This file contains the DatabaseDMLQueryBuilderQueryPartsTest class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Tests
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Felipe Martinez <felipe@m2mobi.com>
 */

namespace Lunr\Libraries\DataAccess;

/**
 * This class contains the tests for the query parts methods.
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Tests
 * @author     Felipe Martinez <felipe@m2mobi.com>
 * @covers     Lunr\Libraries\DataAccess\DatabaseDMLQueryBuilder
 */
class DatabaseDMLQueryBuilderQueryPartsDeleteTest extends DatabaseDMLQueryBuilderTest
{

    /**
     * Test specifying the delete part of a query.
     *
     * @depends Lunr\Libraries\DataAccess\DatabaseDMLQueryBuilderBaseTest::testDeleteEmptyByDefault
     * @covers  Lunr\Libraries\DataAccess\DatabaseDMLQueryBuilder::sql_delete
     */
    public function testInitialDelete()
    {
        $method = $this->builder_reflection->getMethod('sql_delete');
        $method->setAccessible(TRUE);

        $property = $this->builder_reflection->getProperty('delete');
        $property->setAccessible(TRUE);

        $method->invokeArgs($this->builder, array('table'));

        $string = 'table';

        $this->assertEquals($string, $property->getValue($this->builder));
    }

    /**
     * Test specifying the select part of a query.
     *
     * @depends Lunr\Libraries\DataAccess\DatabaseDMLQueryBuilderBaseTest::testDeleteEmptyByDefault
     * @covers  Lunr\Libraries\DataAccess\DatabaseDMLQueryBuilder::sql_delete
     */
    public function testIncrementalDelete()
    {
        $method = $this->builder_reflection->getMethod('sql_delete');
        $method->setAccessible(TRUE);

        $property = $this->builder_reflection->getProperty('delete');
        $property->setAccessible(TRUE);

        $method->invokeArgs($this->builder, array('table'));
        $method->invokeArgs($this->builder, array('table.*'));

        $string = 'table, table.*';

        $this->assertEquals($string, $property->getValue($this->builder));
    }

}

?>
