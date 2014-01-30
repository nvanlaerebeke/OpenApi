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
 * @since         OpenApi v0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
 
/**
 * Api parameter validator
 * Validates parameters based on the available validators
 *
 * @package       OpenApi.Lib
 * @since         OpenApi v0.0.1
 */
class ApiParameterValidator extends Object {
    /**
     * @var array $pData
     * @var array $pRules
     */
    public static function Validate($pData, $pRules) {
        App::uses('CakeValidationSet', 'Model/Validator');
        foreach($pRules as $fieldName => $rules) {
            if(!is_array($rules)) {
                $rules = array($rules => $rules);
            }
            if(!isset($rules['notEmpty'])) {
                $rules['notEmpty'] =  array('rule' => 'notEmpty', 'required' => true);
            }
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
        foreach($pRules as $name => $rule) {
            $set = new CakeValidationSet(strtolower($pFieldName), array());
            $set->setRule($name, $rule);
            $errors = $set->validate($pData);
            
            if(!empty($errors)) {
                if($name == 'notEmpty') {
                    App::uses('ApiMissingParamException', 'OpenApi.Lib/Error');
                    throw new ApiMissingParamException($pFieldName); 
                } else {
                    App::uses('ApiValidationException', 'OpenApi.Lib/Error');
                    throw new ApiValidationException($pFieldName, $name, ($errors[0] != $name) ? $errors[0] : null);
                }
            }
        }
    }
}
