<?php

/**
 * This file contains the class DateTime, which is a collection
 * of commonly used Date and Time methods.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Libraries
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @author     Jose Viso <jose@m2mobi.com>
 *
 */

namespace Lunr\Libraries\Core;

/**
 * Date/Time related functions
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Libraries
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @author     Jose Viso <jose@m2mobi.com>
 *
 */
class DateTime
{

    /**
     * Date/Time format used for function calls.
     * @var String
     */
    private $datetime_format;

    /**
     * Posix Locale used for text representations.
     * @var String
     */
    private $locale;

    /**
     * Constructor
     */
    public function __construct()
    {
        // set a default DateTime format
        $this->datetime_format = 'Y-m-d';

        // set a default POSIX locale
        $this->locale = 'en_US';
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->datetime_format);
        unset($this->locale);
    }

    /**
     * Set used Date/Time format.
     *
     * @param String $format Date/Time format to use
     *
     * @return DateTime $self Reference to the class instance.
     */
    public function set_datetime_format($format)
    {
        $this->datetime_format = $format;
        return $this;
    }

    /**
     * Set used POSIX locale format.
     *
     * @param String $locale  POSIX locale to use
     * @param String $charset Default charset to be used (optional, UTF-8 by default)
     *
     * @return DateTime $self Reference to the class instance.
     */
    public function set_locale($locale, $charset = 'UTF-8')
    {
        $this->locale = $locale . '.' . $charset;
        setlocale(LC_ALL, $this->locale);

        return $this;
    }

    /**
     * Return today's date/time.
     *
     * @return String Current Date/Time
     */
    public function today()
    {
        return strftime($this->datetime_format);
    }

    /**
     * Alias for today().
     *
     * @return String Current Date/Time
     */
    public function now()
    {
        return $this->today();
    }

    /**
     * Return yesterday's date.
     *
     * @return String Tomorrow's date
     */
    public function yesterday()
    {
        return strftime($this->datetime_format, strtotime('-1 day'));
    }

    /**
     * Return tomorrow's date.
     *
     * @return String Tomorrow's date
     */
    public function tomorrow()
    {
        return strftime($this->datetime_format, strtotime('+1 day'));
    }

    /**
     * Returns a MySQL compatible Date & Time definition.
     *
     * @param Integer $timestamp PHP-like Unix Timestamp (optional)
     *
     * @return String $datetime Date & Time as a string
     */
    public function get_datetime($timestamp = FALSE)
    {
        if ($timestamp === FALSE)
        {
            return strftime($this->datetime_format, time());
        }
        else
        {
            return strftime($this->datetime_format, $timestamp);
        }
    }

    /**
     * Return a delayed datetime string.
     *
     * @param String  $delay     Definition for a timeframe
     *                           ("+1 day", "-10 minutes")
     * @param Integer $timestamp Base timestamp, now by default (optional)
     *
     * @return String $return DateTime definition, format YYYY-MM-DD HH:MM:SS
     */
    public function delayed_datetime($delay, $timestamp = 0)
    {
        if ($timestamp === 0)
        {
            $timestamp = time();
        }

        return strftime($this->datetime_format, strtotime($delay, $timestamp));
    }

    /**
     * Return a timestamp of a certain timeframe in the past/future.
     *
     * @param String  $delay     Definition for a timeframe
     *                           ("+1 day", "-10 minutes")
     * @param Integer $timestamp Base timestamp, now by default (optional)
     *
     * @return Integer Delayed timestamp
     */
    public function delayed_timestamp($delay, $timestamp = 0)
    {
        if ($timestamp === 0)
        {
            $timestamp = time();
        }

        return strtotime($delay, $timestamp);
    }

    /**
     * Checks whether a given input string is a valid time definition.
     *
     * @param String $string Input String
     *
     * @return Boolean $return True if it is valid, False otherwise
     */
    public function is_time($string)
    {
        // accepts HHH:MM:SS, e.g. 23:59:30 or 12:30 or 120:17
        if (preg_match('/^(\-)?[0-9]{1,3}(:[0-5][0-9]){1,2}$/', $string))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Checks whether a given input string is a valid date definition.
     *
     * @param String $string Input String
     *
     * @return Boolean $return True if it is a proper date String,
     *                         False otherwise
     */
    public function is_date($string)
    {
        $leap_day = '/^(\d{1,4})[\- \/ \.]02[\- \/ \.]29$/';

        if (preg_match($leap_day, $string))
        {
            $year = preg_replace('/[\- \/ \.]02[\- \/ \.]29$/', '', $string);
            return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0)));
        }
        else
        {
            $feb     = '02[\- \/ \.](0[1-9]|1[0-9]|2[0-8])';
            $_30days = '(0[469]|11)[\- \/ \.](0[1-9]|[12][0-9]|30)';
            $_31days = '(0[13578]|1[02])[\- \/ \.](0[1-9]|[12][0-9]|3[01])';

            if (preg_match("/^(\d{1,4})[\- \/ \.]($_31days|$_30days|$feb)$/", $string))
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
    }

    /**
     * Compares two datetime strings and returns smaller or bigger.
     *
     * This function can be used for PHP's sorting functions.
     *
     * @param String $a DateTime String 1
     * @param String $b DateTime String 2
     *
     * @return Integer -1 if $a < $b, 1 otherwise
     */
    public function sort_compare_datetime($a, $b)
    {
        $a = strtotime($a);
        $b = strtotime($b);

        if ($a == $b)
        {
            return 0;
        }
        else
        {
            return ($a < $b) ? -1 : 1;
        }
    }

}

?>