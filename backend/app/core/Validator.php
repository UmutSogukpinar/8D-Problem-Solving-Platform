<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\ValidationException;

/**
 * Simple validation utility for request payloads.
 *
 * Supported rules:
 *  - required : value must be present and not empty ("")
 *  - string   : value must be a string
 *  - int      : value must be an integer
 *  - nullable : value may be null (skips type checks when null)
 *
 * This validator also rejects unexpected fields: if the input contains keys
 * that are not declared in the rules array, a ValidationException is thrown.
 */
final class Validator
{
    /**
     * Validates input data against the given rules.
     *
     * Rules format:
     *  [
     *      'field' => 'required|string',
     *      'age'   => 'nullable|int'
     *  ]
     *
     * @param array<string, mixed> $data  Input data to validate
     * @param array<string, string> $rules Validation rules per field
     *
     * @return void
     *
     * @throws ValidationException If validation fails
     */
    public static function validate(array $data, array $rules): void
    {
        $errors = [];

        $allowedFields = array_keys($rules);
        $incomingFields = array_keys($data);
        $extras = array_diff($incomingFields, $allowedFields);

        if (!empty($extras))
        {
            $unexpectedFieldsError = [
                'general' => 'Unexpected fields: ' . implode(', ', $extras)
            ];
            throw new ValidationException(json_encode($unexpectedFieldsError));
        }

        foreach ($rules as $field => $ruleString)
        {
            $rulesList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            if (in_array('required', $rulesList, true))
            {
                if ($value === null || $value === '')
                {
                    $errors[$field] = "{$field} is required";
                    continue;
                }
            }

            if ($value === null && in_array('nullable', $rulesList, true))
            {
                continue;
            }

            if ($value !== null)
            {
                if (in_array('string', $rulesList, true) && !is_string($value))
                {
                    $errors[$field] = "{$field} must be a string";
                }

                if (in_array('int', $rulesList, true) && !is_int($value))
                {
                    $errors[$field] = "{$field} must be an integer";
                }
            }
        }

        if (!empty($errors))
        {
            throw new ValidationException(json_encode($errors));
        }
    }
}
