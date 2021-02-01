<?php

namespace LoganStellway\Base\App;

class S3
{
    /**
     * Setup
     */
    public function __construct()
    {
        add_filter('s3_uploads_s3_client_params', [$this, "configureEndpoint"]);
    }

    /**
     * Configure S3 endpoint
     * 
     * @param array $params
     * @return array
     */
    public function configureEndpoint($params)
    {
        if (defined('S3_UPLOADS_ENDPOINT')) {
            $params['endpoint'] = constant('S3_UPLOADS_ENDPOINT');
            $params['use_path_style_endpoint'] = true;
            $params['debug'] = false; // Set to true if uploads are failing.
        }

        return $params;
    }
}
