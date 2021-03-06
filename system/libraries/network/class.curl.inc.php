<?php

/**
 * This file contains a Curl wrapper class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2011-2012, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Libraries\Network;

/**
 * Curl wrapper class.
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */
class Curl implements HttpRequestInterface
{

    /**
     * Curl options array
     * @var array
     */
    private $options;

    /**
     * HTTP Headers used by the Curl request
     * @var array
     */
    private $headers;

    /**
     * Curl request resource handle
     * @var resource
     */
    private $handle;

    /**
     * Information about a successfully completed request
     * @var array
     */
    private $info;

    /**
     * Curl error number
     * @var Integer
     */
    private $errno;

    /**
     * Curl error message
     * @var String
     */
    private $errmsg;

    /**
     * HTTP status code of the request made
     * @var Integer
     */
    private $http_code;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array();
        $this->headers = array();

        // default: no error
        $this->errno  = 0;
        $this->errmsg = '';

        // default: no info
        $this->info = array();

        // set http_code to zero to indicate we haven't made a request yet
        $this->http_code = 0;

        // set default curl options
        $this->options[CURLOPT_TIMEOUT]        = 30;
        $this->options[CURLOPT_RETURNTRANSFER] = TRUE;
        $this->options[CURLOPT_FOLLOWLOCATION] = TRUE;
        $this->options[CURLOPT_FAILONERROR]    = TRUE;

        // pre-initialization
        $this->handle = NULL;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->options);
        unset($this->headers);
        unset($this->errno);
        unset($this->errmsg);
        unset($this->info);
        unset($this->http_code);
        unset($this->handle);
    }

    /**
     * Get access to certain private attributes.
     *
     * This gives access to errno, errmsg and info.
     *
     * @param String $name Attribute name
     *
     * @return mixed $return Value of the chosen attribute
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'errno':
            case 'errmsg':
            case 'info':
            case 'http_code':
                return $this->{$name};
            default:
                return NULL;
        }
    }

    /**
     * Set multiple curl config options at once.
     *
     * @param array $options Array of curl config options
     *
     * @return Curl $self Self-reference
     */
    public function set_options($options)
    {
        if (!is_array($options))
        {
            return $this;
        }

        foreach ($options as $key => $value)
        {
            $this->set_option($key, $value);
        }

        return $this;
    }

    /**
     * Set a curl config option.
     *
     * @param String $key   Name of a curl config key
     * @param mixed  $value Value of that config options
     *
     * @return Curl $self Self-reference
     */
    public function set_option($key, $value)
    {
        if (substr($key, 0, 4) !== 'CURL')
        {
            return $this;
        }

        if (defined($key) === FALSE)
        {
            return $this;
        }

        $this->options[constant($key)] = $value;

        return $this;
    }

    /**
     * Set multiple additional HTTP headers to be used by the request.
     *
     * @param array $headers Array of HTTP Header Strings
     *
     * @return Curl $self Self-reference
     */
    public function set_http_headers($headers)
    {
        if (is_array($headers))
        {
            $this->headers = $headers + $this->headers;
        }

        return $this;
    }

    /**
     * Set additional HTTP headers to be used by the request.
     *
     * @param String $header Header String
     *
     * @return Curl $self Self-reference
     */
    public function set_http_header($header)
    {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * Initialize the curl request.
     *
     * @param String $uri URI for the request
     *
     * @return Boolean $return TRUE if the initialization was successful,
     *                         FALSE otherwise
     */
    private function init($uri)
    {
        $this->handle = curl_init($uri);

        if (!empty($this->headers))
        {
            $this->set_option('CURLOPT_HTTPHEADER', $this->headers);
        }

        if (curl_setopt_array($this->handle, $this->options) !== TRUE)
        {
            $this->errmsg = 'Could not set curl options!';
            $this->errno  = -1;
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Execute a curl request.
     *
     * @return mixed $return Return value
     */
    private function execute()
    {
        $return = curl_exec($this->handle);

        if ($return === FALSE)
        {
            $this->errno     = curl_errno($this->handle);
            $this->errmsg    = curl_error($this->handle);
            $this->http_code = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
        }
        else
        {
            $this->info = curl_getinfo($this->handle);
        }

        curl_close($this->handle);
        $this->handle = NULL;

        return $return;
    }

    /**
     * Retrieve remote content.
     *
     * @param String $uri Remote URI
     *
     * @return mixed $return Return value
     */
    public function get_request($uri)
    {
        if ($this->init($uri) === FALSE)
        {
            return FALSE;
        }

        return $this->execute();
    }

    /**
     * Post data to a remote service.
     *
     * @param String $uri  Remote URI
     * @param mixed  $data Data to post
     *
     * @return mixed $return Return value
     */
    public function post_request($uri, $data)
    {
        $this->options[CURLOPT_CUSTOMREQUEST] = 'POST';
        $this->options[CURLOPT_POST]          = TRUE;
        $this->options[CURLOPT_POSTFIELDS]    = $data;

        if ($this->init($uri) === FALSE)
        {
            return FALSE;
        }

        $output = $this->execute();

        unset($this->options[CURLOPT_CUSTOMREQUEST]);
        unset($this->options[CURLOPT_POST]);
        unset($this->options[CURLOPT_POSTFIELDS]);

        return $output;
    }

}

?>
