<?php

namespace Zippy_Core\Src\Helpers;


defined('ABSPATH') or die();

class Shipping_Helper
{
    /**
     *  $rules = [
     *     'keyword' => 'string',
     *      'limit'   => 'int',
     *   ];
     */
    public static function validate_request_data($params, $rules = [])
    {
        $errors = [];

        foreach ($rules as $field => $type) {
            $value = $params[$field] ?? null;

            if (is_null($value) || (is_string($value) && trim($value) === '')) {
                $errors[] = "Missing or empty parameter: {$field}";
                continue;
            }

            switch ($type) {
                case 'string':
                    if (!is_string($value)) {
                        $errors[] = "{$field} must be a string.";
                    }
                    break;

                case 'int':
                case 'integer':
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $errors[] = "{$field} must be an integer.";
                    }
                    break;

                case 'float':
                case 'decimal':
                    if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                        $errors[] = "{$field} must be a float.";
                    }
                    break;

                case 'bool':
                case 'boolean':
                    if (!is_bool($value) && !in_array($value, ['true', 'false', '1', '0', 1, 0], true)) {
                        $errors[] = "{$field} must be a boolean.";
                    }
                    break;

                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "{$field} must be a valid email.";
                    }
                    break;

                case 'array':
                    if (!is_array($value)) {
                        $errors[] = "{$field} must be an array.";
                    }
                    break;

                default:
                    $errors[] = "Unknown validation rule for field: {$field}";
            }
        }

        return empty($errors) ? true : new WP_Error('invalid_request', implode('; ', $errors), ['status' => 400]);
    }
}
