<?php

/**
 * Abtract database connection class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2012, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Libraries\DataAccess;

/**
 * This class defines abstract database access.
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */
abstract class DatabaseConnection
{

    /**
     * Connection status
     * @var Boolean
     */
    protected $connected;

    /**
     * Whether there's write access to the database or not
     * @var Boolean
     */
    protected $readonly;

    /**
     * Reference to the Configuration class
     * @var Configuration
     */
    protected $configuration;

    /**
     * Reference to the Logger class
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param Configuration &$configuration Reference to the configuration class
     * @param Logger        &$logger        Reference to the logger class
     */
    public function __construct(&$configuration, &$logger)
    {
        $this->connected = FALSE;
        $this->readonly  = FALSE;

        $this->configuration =& $configuration;
        $this->logger        =& $logger;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->readonly);
        unset($this->connected);
    }

    /**
     * Toggle readonly flag on the connection.
     *
     * @param Boolean $switch Whether to make the connection readonly or not
     *
     * @return void
     */
    public function set_readonly($switch)
    {
        $this->readonly = $switch;
    }

    /**
     * Establishes a connection to the defined database server.
     *
     * @return void
     */
    public abstract function connect();

    /**
     * Disconnects from database server.
     *
     * @return void
     */
    public abstract function disconnect();

    /**
     * Change the default database for the current connection.
     *
     * @param String $db New default database
     *
     * @return Boolean True on success, False on Failure
     */
    public abstract function change_database($db);

    /**
     * Return a new instance of a QueryBuilder object.
     *
     * @return DatabaseDMLQueryBuilder $builder New DatabaseDMLQueryBuilder object instance
     */
    public abstract function get_new_dml_query_builder_object();

    /**
     * Escape a string to be used in a SQL query.
     *
     * @param String $string The string to escape
     *
     * @return Mixed $return The escaped string on success, FALSE on error
     */
    public abstract function escape_string($string);

    /**
     * Run a SQL query.
     *
     * @param String $sql_query The SQL query to run on the database
     *
     * @return DatabaseQueryResult $result Query Result
     */
    public abstract function query($sql_query);

}

?>
