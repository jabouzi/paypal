<?php

/*
 * This file is part of the Pepper framework.
 * (c) 2005-2012 Louis-Philippe Favreau
 *
 * For the full copyright and license information, please view
 * the license.txt file that is included in this project.
 *
 * http://www.pepperframework.org/
 *
 */

/**
 * @package pepper
 * @subpackage Helpers
 */
class Network
{

    /**
     * Returns the real IP address taking into account proxies.
     *     Taken from http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
     *
     * @return string The real IP address.
     */
    public static function visitorIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { // check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { // check if ip is passed from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}
