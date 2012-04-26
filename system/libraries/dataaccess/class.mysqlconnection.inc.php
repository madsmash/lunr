<?php

/**
 * MySQL database connection class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @author     M2Mobi <info@m2mobi.com>
 */

namespace Lunr\Libraries\DataAccess;

/**
 * MySQL/MariaDB database access class.
 *
 * @category   Libraries
 * @package    DataAccess
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @author     M2Mobi <info@m2mobi.com>
 */
class MySQLConnection extends DatabaseConnection
{

    /**
     * Hostname of the database server (read/write access)
     * @var String
     */
    protected $rw_host;

    /**
     * Hostname of the database server (readonly access)
     * @var String
     */
    protected $ro_host;

    /**
     * Username of the user used to connect to the database
     * @var String
     */
    protected $user;

    /**
     * Password of the user used to connect to the database
     * @var String
     */
    protected $pwd;

    /**
     * Database to connect to.
     * @var String
     */
    protected $db;

    /**
     * Port to connect to the database server.
     * @var Integer
     */
    protected $port;

    /**
     * Path to the UNIX socket for localhost connection
     * @var String
     */
    protected $socket;

    /**
     * Instance of the Mysqli class
     * @var mysqli
     */
    protected $mysqli;

    /**
     * Constructor.
     *
     * @param Configuration &$configuration Reference to the configuration class
     * @param Logger        &$logger        Reference to the logger class
     * @param mysqli        $mysqli         Instance of the mysqli class
     */
    public function __construct(&$configuration, &$logger, $mysqli)
    {
        parent::__construct($configuration, $logger);

        $this->mysqli =& $mysqli;

        $this->set_configuration();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        if ($this->connected === TRUE)
        {
            $this->disconnect();
        }

        unset($this->mysqli);
        unset($this->rw_host);
        unset($this->ro_host);
        unset($this->user);
        unset($this->pwd);
        unset($this->db);
        unset($this->port);
        unset($this->socket);

        parent::__destruct();
    }

    /**
     * Set the configuration values.
     *
     * @return void
     */
    private function set_configuration()
    {
        $this->rw_host = $this->configuration['db']['rw_host'];
        $this->user    = $this->configuration['db']['username'];
        $this->pwd     = $this->configuration['db']['password'];
        $this->db      = $this->configuration['db']['database'];

        if (!isset($this->configuration['db']['ro_host']) || empty($this->configuration['db']['ro_host']))
        {
            $this->ro_host = $this->rw_host;
        }
        else
        {
            $this->ro_host = $this->configuration['db']['ro_host'];
        }

        if (isset($this->configuration['db']['port']))
        {
            $this->port = $this->configuration['db']['port'];
        }
        else
        {
            $this->port = ini_get('mysqli.default_port');
        }

        if (isset($this->configuration['db']['socket']))
        {
            $this->socket = $this->configuration['db']['socket'];
        }
        else
        {
            $this->socket = ini_get('mysqli.default_socket');
        }
    }

    /**
     * Establishes a connection to the defined mysql-server.
     *
     * @return void
     */
    public function connect()
    {
        if ($this->configuration['db']['driver'] != 'mysql')
        {
            $this->logger->log_error('Cannot connect to a non-mysql database connection!');
            return;
        }

        $host = ($this->readonly === TRUE) ? $this->ro_host : $this->rw_host;

        $this->mysqli->connect($host, $this->user, $this->pwd, $this->db, $this->port, $this->socket);

        if ($this->mysqli->errno === 0)
        {
            $this->mysqli->set_charset('utf8');
            $this->connected = TRUE;
        }
    }

    /**
     * Disconnects from mysql-server.
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->connected !== TRUE)
        {
            return;
        }

        $this->mysqli->kill($this->mysqli->thread_id);
        $this->mysqli->close();
        $this->connected = FALSE;
    }

    /**
     * Change the default database for the current connection.
     *
     * @param String $db New default database
     *
     * @return Boolean True on success, False on Failure
     */
    public function change_database($db)
    {
        if ($this->connected !== TRUE)
        {
            $this->connect();
        }

        if ($this->connected === TRUE)
        {
            return $this->mysqli->select_db($db);
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Return a new instance of a QueryBuilder object.
     *
     * @return DatabaseDMLQueryBuilder $builder New DatabaseDMLQueryBuilder object instance
     */
    public function get_new_dml_query_builder_object()
    {
        return new MySQLDMLQueryBuilder();
    }

}

?>
