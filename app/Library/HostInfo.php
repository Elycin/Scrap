<?php
/**
 * Created by PhpStorm.
 * User: elyci
 * Date: 1/12/2018
 * Time: 8:17 PM
 */

namespace App\Library;


class HostInfo
{
    /**
     * Get Accessed Domain
     *
     * @return mixed
     */
    public static function getAccessedDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Get Protocol
     * (http/https)
     *
     * This function has support for cloudflare's reverse proxy.
     *
     * @return mixed
     */
    public static function getAccessedProtocol()
    {
        if (isset($_SERVER["X-FORWARDED-PROTO"])) {
            return sprintf("%s://", $_SERVER["X-FORWARDED-PROTO"]);
        } elseif (isset($_SERVER['HTTPS'])) {
            return "https://";
        } else {
            return "http://";
        }
    }


    /**
     * Get IP Address
     *
     * This function has support for cloudflare's reverse proxy.
     *
     * @return mixed
     */
    public static function getAccessedIpAddress()
    {
        return (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
    }
}