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
 * @package       OpenApi.Routing
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
            $set = new CakeValidationSet(strtolower($fieldName), $rules);
            $set->setRule('required', array('rule' => 'notEmpty', 'required' => true));
            $errors = $set->validate($pData);

            if(!empty($errors)) {
                if(isset($rules[$errors[0]]['rule'])) {
                    $rule = $rules[$errors[0]]['rule'];
                } else {
                    App::uses('ApiMissingParamException', 'OpenApi.Lib/Error');
                    throw new ApiMissingParamException($fieldName);
                }
                if(is_array($rule)) {
                    $rule = $rule[0];
                }
                App::uses('ApiValidationException', 'OpenApi.Lib/Error');
                throw new ApiValidationException($fieldName, $rule);
            }
        }
    }
}
