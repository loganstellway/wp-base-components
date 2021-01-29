<?php

namespace LoganStellway\Base\Api;

use WP_REST_Request;

abstract class AbstractForm extends AbstractApi
{
    /**
     * Define the accepted field keys
     * @var string[]
     */
    protected $_fields = [];

    /**
     * Define the required field keys
     * @var string[]
     */
    protected $_required = [];

    /**
     * Define regex validators for fields
     * Eg: $_regex = ['field' => '/(foo)(bar)(baz)/']
     * 
     * @var string[]
     */
    protected $_regex = [];

    /**
     * Define custom validation functions
     * Eg: $_validations = ['field' => function($val) {} : bool]
     * 
     * @var (function(string $val): bool)[]
     */
    protected $_validations = [];

    /**
     * Send failure response
     */
    protected function fail($message, $field, $statusCode = 400)
    {
        return wp_send_json_error([
            "message" => sprintf(__($message), $field),
            "field" => $field
        ], $statusCode);
    }

    /**
     * Get request data
     * 
     * @param WP_REST_Request $request
     * @return mixed
     */
    protected function getPostData(WP_REST_Request $request)
    {
        return $request->get_body_params() ?: $request->get_json_params();
    }

    /**
     * Get submission data
     */
    protected function getFields(WP_REST_Request $request)
    {
        $data = [];
        $fields = $this->getPostData($request);

        foreach ($this->_fields as $key) {
            $data[$key] = $fields[$key] ?? "";
        }

        return $data;
    }

    /**
     * Validate request
     * 
     * @param array $fields
     * @return mixed
     */
    protected function validate(array $fields)
    {
        // Required
        foreach ($this->_required as $field) {
            if (!isset($fields[$field]) || $fields[$field] === null || trim($fields[$field]) === "") {
                return $this->fail("Please enter a valid %s", $field);
            }
        }

        // Validation functions and regex
        foreach ($this->_regex as $field => $pattern) {
            if (!isset($fields[$field]) || !preg_match($pattern, $fields[$field])) {
                return $this->fail("Please enter a valid %s", $field);
            }
        }

        // Custom checks
        foreach ($this->_validations as $field => $check) {
            if (!isset($fields[$field]) || !$check($fields[$field])) {
                return $this->fail("Please enter a valid %s", $field);
            }
        }

        return true;
    }
}
