<?php

/**
 * Date/Time related functions
 * @author M2Mobi, Heinz Wiesinger
 */
class M2DateTime
{

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Destructor
     */
    public function __destruct()
    {

    }

    /**
     * Return today's date (YYYY-MM-DD)
     * @return String Today's date
     */
    public static function today()
    {
        return date('Y-m-d');
    }

    /**
     * Return tomorrow's date (YYYY-MM-DD)
     * @return String Tomorrow's date
     */
    public static function tomorrow()
    {
        return date('Y-m-d', strtotime("+1 day"));
    }

    /**
     * Return the current time (HH:MM:SS)
     * @return String current time
     */
    public static function now()
    {
        return strftime("%H:%M:%S", time());
    }

}

?>