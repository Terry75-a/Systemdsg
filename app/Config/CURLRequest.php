<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class CURLRequest extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * CURLRequest Share Options
     * --------------------------------------------------------------------------
     *
     * Whether share options between requests or not.
     *
     * If true, all the options won't be reset between requests.
     * It may cause an error request with unnecessary headers.
     */
    public bool $shareOptions = false;

    /**
     * Disable connection sharing for compatibility with older libcurl builds
     * (including XAMPP builds without CURL_LOCK_DATA_CONNECT).
     * Ordinary HTTPS requests and certificate verification remain enabled.
     *
     * @var list<int>
     */
    public array $shareConnectionOptions = [];
}
