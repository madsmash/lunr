<?php

/**
 * This file contains a factory for the different database
 * connection drivers.
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
 * Database connection driver factory
 *
 * @category   Libraries
 * @package    Database
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */
class DBConFactory
{

    /**
     * Returns a database connection based on the input.
     *
     * @param array   $db       Database configuration values
     * @param Boolean $readonly Whether to return a readonly connection or not
     *
     * @return DBCon $db Reference to a new database connection driver
     */
    public static function get_db_connection($db, $readonly)
    {
        if (!isset($db['driver']))
        {
            $db['driver'] = '';
        }

        switch ($db['driver'])
        {
            case 'sqlite':
                return new DBConSqlite($db, $readonly);
                break;
            case 'mysql':
            default:
                return new DBConMySQL($db, $readonly);
                break;
        }
    }

}

?>
