<?php
/**
 * Api parameter validator
 *
 * Copyright (c)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c)
 * @link          http://blog.crazytje.com
 * @package       OpenApi.Lib
 * @since         OpenApi v1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace OpenApi\Lib;

use Cake\Log\LogTrait;

use Cake\Validation\Validator;
use Cake\Validation\ValidationRule;

use OpenApi\Error\Exception\MissingParameterException;
use OpenApi\Error\Exception\ValidationException;

/**
 * Api parameter validator
 * Validates parameters based on the available validators
 *
 * @package       OpenApi.Lib
 * @since         OpenApi v1.0.0
 */
class ApiParameterValidator {

    use LogTrait;

    /**
     * @var array $pData
     * @var array $pRules
     */
    public static function Validate($pData, $pRules) {
        foreach($pRules as $fieldName => $rules) {
            if(!is_array($rules)) {
                $rules = array($rules => [ 'rule' => $rules ]);
            }
            /*if(!isset($rules['notBlank'])) {
                $rules['notBlank'] =  array('rule' => 'notBlank', 'required' => true);
            }*/
            self::_validateField($fieldName, $rules, $pData);
        }
    }

    /**
     * Validates a single field with 1 or more rules
     *
     * @param string $pFieldName
     * @param array $pRules
     * @param array $pData
     * @return null
     */
    private static function _validateField($pFieldName, $pRules, $pData) {
        $validator = new Validator();
        $validator->requirePresence($pFieldName)->notEmpty($pFieldName);
        foreach($pRules as $name => $rule) {
            $validator->add($pFieldName, $name, $rule);
        }
        $errors = $validator->errors($pData);
        if(!empty($errors)) {
            $name = array_keys($errors[$pFieldName])[0];
            $msg = array_values($errors[$pFieldName])[0];
            if($name == 'notEmpty' || $name == '_required' || $name == '_empty') {
                throw new MissingParameterException($pFieldName);
            } else {
                throw new ValidationException($pFieldName, $name);
            }
        }
    }
}