<?php

/**
 * This file contains an abstract definition for a Query result class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    Database
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2010-2012, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Libraries\Database;

/**
 * Abstract definition for a Json wrapper
 *
 * @category   Libraries
 * @package    Database
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */
interface QueryInterface
{

    /**
     * Returns the number of affected rows.
     *
     * @return Integer number of affected rows
     */
    public function affected_rows();

    /**
     * Returns the number of result rows.
     *
     * @return Integer number of result rows
     */
    public function num_rows();

    /**
     * Return an array of results.
     *
     * @return Array Array of Results
     */
    public function result_array();

    /**
     * Return the first row of results.
     *
     * @return Array One data row
     */
    public function result_row();

    /**
     * Return one field for the first row.
     *
     * @param String $col The field to return
     *
     * @return mixed Field value
     */
    public function field($col);

    /**
     * Return a column of results.
     *
     * @param String $col The column to return
     *
     * @return Array Query Results
     */
    public function col($col);

}

?>
