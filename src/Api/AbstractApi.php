<?php

namespace LoganStellway\Base\Api;

abstract class AbstractApi
{
    /**
     * Server IP properties
     */
    const SERVER_IP_PROPERTIES = [
        "HTTP_CF_CONNECTING_IP",
        "HTTP_CLIENT_IP",
        "HTTP_X_FORWARDED_FOR",
        "HTTP_X_FORWARDED",
        "HTTP_X_CLUSTER_CLIENT_IP",
        "HTTP_FORWARDED_FOR",
        "HTTP_FORWARDED",
        "REMOTE_ADDR",
    ];

    /**
     * Get client IP
     * 
     * @return string
     */
    public function getClientIp()
    {
        foreach (self::SERVER_IP_PROPERTIES as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }
}
